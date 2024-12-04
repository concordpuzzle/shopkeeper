<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Automattic\WooCommerce\Client;
use Carbon\Carbon;
use App\Models\Expense;

class DashboardController extends Controller
{
    protected $woocommerce;

    public function __construct()
    {
        $this->woocommerce = new Client(
            config('services.woocommerce.store_url'),
            config('services.woocommerce.consumer_key'),
            config('services.woocommerce.consumer_secret'),
            [
                'version' => 'wc/v3',
            ]
        );
    }

    public function index(Request $request)
    {
        $period = $request->get('period', 'month');
        
        // Get date range based on period
        $startDate = $this->getStartDate($period);
        $endDate = now();

        // Fetch products and calculate profit
        $products = Product::with('materials')->get();
        
        // Get sales data from WooCommerce
        $salesData = $this->getSalesData($startDate, $endDate);
        
        // Calculate profit data
        $profitData = $this->calculateProfitData($salesData, $products);

        // Generate labels for the date range
        $labels = $this->generateDateLabels($startDate, $endDate, $period);

        // Define the groupByFormat based on the period
        $groupByFormat = match($period) {
            'year' => 'Y',
            '6months' => 'Y-m',
            'month' => 'Y-m',
            'week' => 'Y-m-d',
            default => 'Y-m-d',
        };

        // Fetch expenses data
        $expenses = Expense::whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();

        $expensesData = [
            'labels' => $labels,
            'data' => $expenses->isEmpty() ? array_fill(0, count($labels), 0) : $expenses
                ->groupBy(function($expense) use ($groupByFormat) {
                    return $expense->date->format($groupByFormat);
                })
                ->map(function($group) {
                    return $group->sum('amount');
                })
                ->values()
                ->toArray()
        ];

        dd($expensesData); // Check the output

        return view('dashboard', compact('salesData', 'profitData', 'expensesData', 'labels'));
    }

    private function getStartDate($period)
    {
        return match($period) {
            'year' => now()->subYear(),
            '6months' => now()->subMonths(6),
            'month' => now()->subMonth(),
            'week' => now()->subWeek(),
            default => now()->subMonth(),
        };
    }

    private function getSalesData($startDate, $endDate)
    {
        // Initialize an empty collection for all orders
        $allOrders = collect();
        $page = 1;
        $per_page = 100;

        do {
            // Fetch orders from WooCommerce with pagination
            $orders = collect($this->woocommerce->get('orders', [
                'after' => $startDate->toIso8601String(),
                'before' => $endDate->toIso8601String(),
                'per_page' => $per_page,
                'page' => $page,
                'status' => ['completed', 'processing'],
            ]));

            // Add orders to our collection
            $allOrders = $allOrders->concat($orders);

            // Increment page number
            $page++;

            // Continue until we get less than per_page results (meaning it's the last page)
        } while ($orders->count() === $per_page);

        // Group orders by date
        $salesByDate = $allOrders->groupBy(function($order) {
            return Carbon::parse($order->date_created)->format('Y-m-d');
        });

        // Generate labels and data arrays
        $labels = [];
        $data = [];

        $currentDate = clone $startDate;
        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $labels[] = $currentDate->format('M d');
            
            // Sum the total sales for this date (excluding shipping)
            $dailySales = $salesByDate->get($dateStr, collect())->sum(function($order) {
                return floatval($order->total) - floatval($order->shipping_total);
            });
            
            $data[] = $dailySales;
            $currentDate->addDay();
        }

        \Log::info('Sales Data', [
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'total_orders' => $allOrders->count(),
            'total_sales' => array_sum($data)
        ]);

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function calculateProfitData($salesData, $products)
    {
        $profitMargin = 0.5; // Default 50% margin for products without mapping
        $productProfitMargins = $products->mapWithKeys(function($product) {
            if ($product->sale_price && $product->materials->sum('price_per')) {
                $margin = ($product->sale_price - $product->materials->sum('price_per')) / $product->sale_price;
                return [$product->woo_category_id => $margin];
            }
            return [];
        });

        // Calculate profit based on sales data (which now excludes shipping) and known margins
        $profitData = collect($salesData['data'])->map(function($sale) use ($profitMargin) {
            return $sale * $profitMargin;
        })->toArray();

        return [
            'labels' => $salesData['labels'],
            'data' => $profitData
        ];
    }

    private function calculateSalesData($startDate, $endDate)
    {
        try {
            $orders = collect($this->woocommerce->get('orders', [
                'after' => $startDate->toISOString(),
                'before' => $endDate->toISOString(),
                'per_page' => 100,
            ]));

            $totalOrders = $orders->count();
            $totalSales = $orders->sum(function($order) {
                // Exclude shipping cost from total sales
                return floatval($order->total) - floatval($order->shipping_total);
            });

            return [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'total_orders' => $totalOrders,
                'total_sales' => $totalSales
            ];
        } catch (\Exception $e) {
            \Log::error('Failed to calculate sales data', [
                'error' => $e->getMessage()
            ]);
            return [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'total_orders' => 0,
                'total_sales' => 0
            ];
        }
    }

    private function getDailySales($startDate, $endDate)
    {
        try {
            $orders = collect($this->woocommerce->get('orders', [
                'after' => $startDate->toISOString(),
                'before' => $endDate->toISOString(),
                'per_page' => 100,
            ]));

            return $orders->groupBy(function($order) {
                return Carbon::parse($order->date_created)->format('Y-m-d');
            })->map(function($dayOrders) {
                return $dayOrders->sum(function($order) {
                    // Exclude shipping cost from daily sales
                    return floatval($order->total) - floatval($order->shipping_total);
                });
            });
        } catch (\Exception $e) {
            \Log::error('Failed to get daily sales', [
                'error' => $e->getMessage()
            ]);
            return collect([]);
        }
    }

    private function generateDateLabels($startDate, $endDate, $period)
    {
        $labels = [];
        $currentDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        while ($currentDate <= $endDate) {
            switch ($period) {
                case 'daily':
                    $labels[] = $currentDate->format('Y-m-d');
                    $currentDate->addDay();
                    break;
                case 'weekly':
                    $labels[] = $currentDate->format('W');
                    $currentDate->addWeek();
                    break;
                case 'monthly':
                    $labels[] = $currentDate->format('M Y');
                    $currentDate->addMonth();
                    break;
                default:
                    $labels[] = $currentDate->format('Y-m-d');
                    $currentDate->addDay();
            }
        }

        return $labels;
    }
}

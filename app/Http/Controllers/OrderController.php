<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Automattic\WooCommerce\Client;

class OrderController extends Controller
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

    public function index()
    {
        try {
            $wooOrders = collect($this->woocommerce->get('orders', [
                'status' => ['processing', 'on-hold'],
                'per_page' => 100,
            ]));

            $orders = $wooOrders->map(function($order) {
                return [
                    'id' => $order->id,
                    'status' => $order->status,
                    'customer_name' => $order->billing->first_name . ' ' . $order->billing->last_name,
                    'shipping_address' => sprintf(
                        '%s, %s, %s %s, %s',
                        $order->shipping->address_1,
                        $order->shipping->city,
                        $order->shipping->state,
                        $order->shipping->postcode,
                        $order->shipping->country
                    ),
                    'total' => $order->total,
                    'options' => $this->getOrderOptions($order),
                    'date_created' => $order->date_created,
                ];
            });

            return view('orders.index', compact('orders'));
        } catch (\Exception $e) {
            \Log::error('Failed to fetch WooCommerce orders', [
                'error' => $e->getMessage()
            ]);
            
            return view('orders.index', [
                'orders' => collect([]),
                'error' => 'Failed to fetch orders. Please try again later.'
            ]);
        }
    }

    private function getOrderOptions($order)
    {
        try {
            if (!isset($order->line_items)) {
                return [];
            }

            return collect($order->line_items)->map(function($item) {
                $slug = str_replace(' ', '-', strtolower($item->name));
                return [
                    'product' => $item->name ?? 'Unknown Product',
                    'product_url' => "https://concordpuzzle.com/shop/{$slug}",
                    'quantity' => $item->quantity ?? 0,
                    'total' => $item->total ?? 0,
                ];
            })->toArray();
        } catch (\Exception $e) {
            \Log::error('Error processing order options', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }
}

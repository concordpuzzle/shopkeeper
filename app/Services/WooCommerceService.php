<?php

namespace App\Services;

use Automattic\WooCommerce\Client;
use Carbon\Carbon;

class WooCommerceService
{
    private $client;

    public function __construct()
    {
        if (config('woocommerce.consumer_key') && config('woocommerce.consumer_secret')) {
            $this->client = new Client(
                config('woocommerce.store_url'),
                config('woocommerce.consumer_key'),
                config('woocommerce.consumer_secret'),
                [
                    'version' => 'wc/v3',
                ]
            );
        }
    }

    public function getSalesData($period = 'year')
    {
        // Return empty dataset if WooCommerce isn't configured
        if (!$this->client) {
            return [
                'labels' => [],
                'data' => []
            ];
        }

        try {
            $after = match($period) {
                'week' => Carbon::now()->subWeek(),
                'month' => Carbon::now()->subMonth(),
                '6months' => Carbon::now()->subMonths(6),
                'year' => Carbon::now()->subYear(),
                default => Carbon::now()->subYear()
            };

            $orders = $this->client->get('orders', [
                'after' => $after->toIso8601String(),
                'status' => ['completed', 'processing'],
                'per_page' => 100,
            ]);

            return $this->aggregateSalesData($orders ?? [], $period);
        } catch (\Exception $e) {
            \Log::error('WooCommerce API Error: ' . $e->getMessage());
            return [
                'labels' => [],
                'data' => []
            ];
        }
    }

    private function aggregateSalesData($orders, $period)
    {
        if (empty($orders)) {
            return [
                'labels' => [],
                'data' => []
            ];
        }

        $salesByDate = collect($orders)->groupBy(function ($order) use ($period) {
            $date = Carbon::parse($order->date_created);
            return match($period) {
                'week' => $date->format('Y-m-d'),
                'month' => $date->format('Y-m-d'),
                '6months' => $date->format('Y-m'),
                'year' => $date->format('Y-m'),
                default => $date->format('Y-m')
            };
        })->map(function ($orders) {
            return $orders->sum(function ($order) {
                return $order->total ?? 0;
            });
        })->sortKeys();

        return [
            'labels' => $salesByDate->keys()->toArray(),
            'data' => $salesByDate->values()->toArray()
        ];
    }
}

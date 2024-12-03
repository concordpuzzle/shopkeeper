<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Automattic\WooCommerce\Client;

class WooCommerceController extends Controller
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

    public function getCategories()
    {
        try {
            $categories = $this->woocommerce->get('products/categories', ['per_page' => 100]);
            return response()->json($categories);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch categories'], 500);
        }
    }
}

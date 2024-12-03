<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        \Log::info('Starting products index method');
        
        $user = auth()->user();
        \Log::info('Current user:', ['user_id' => $user->id]);
        
        $products = $user->products()->with('materials')->get();
        \Log::info('Products query result:', [
            'count' => $products->count(),
            'products' => $products->toArray()
        ]);
        
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'materials.*.name' => 'required|string|max:255',
            'materials.*.inventory_count' => 'required|integer|min:0',
            'materials.*.price_per' => 'required|numeric|min:0',
            'materials.*.source' => 'required|string|max:255',
        ]);

        $product = auth()->user()->products()->create([
            'title' => $request->title,
        ]);

        foreach ($request->materials as $material) {
            $product->materials()->create($material);
        }

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function updateSalePrice(Request $request, Product $product)
    {
        \Log::info('Updating sale price for product ' . $product->id, [
            'request' => $request->all(),
            'product' => $product
        ]);

        $validated = $request->validate([
            'sale_price' => 'required|numeric|min:0',
        ]);

        $product->update($validated);

        return response()->json(['success' => true]);
    }

    public function updateWooCategory(Request $request, Product $product)
    {
        try {
            $validated = $request->validate([
                'woo_category_id' => 'nullable|string',
                'woo_category_name' => 'nullable|string',
            ]);

            $product->update($validated);

            return response()->json([
                'success' => true,
                'woo_category_id' => $product->woo_category_id,
                'woo_category_name' => $product->woo_category_name
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to update WooCommerce category', [
                'error' => $e->getMessage(),
                'product_id' => $product->id,
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false, 
                'message' => 'Failed to update category: ' . $e->getMessage()
            ], 500);
        }
    }
}

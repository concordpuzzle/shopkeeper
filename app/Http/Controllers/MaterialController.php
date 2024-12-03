<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function store(Request $request, Product $product)
    {
        \Log::info('Creating new material', [
            'product_id' => $product->id,
            'request' => $request->all()
        ]);

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'inventory_count' => 'required|integer|min:0',
                'price_per' => 'required|numeric|min:0',
                'source' => 'required|string|max:255',
            ]);

            $material = $product->materials()->create($validated);

            \Log::info('Material created successfully', [
                'material_id' => $material->id,
                'product_id' => $product->id
            ]);

            return response()->json([
                'success' => true,
                'material' => $material->fresh()
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to create material', [
                'error' => $e->getMessage(),
                'product_id' => $product->id,
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create material: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Material $material)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'inventory_count' => 'required|integer|min:0',
                'price_per' => 'required|numeric|min:0',
                'source' => 'required|string|max:255',
            ]);

            $material->update($validated);

            return response()->json([
                'success' => true,
                'material' => $material->fresh()
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to update material', [
                'error' => $e->getMessage(),
                'material_id' => $material->id,
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update material'
            ], 500);
        }
    }
}

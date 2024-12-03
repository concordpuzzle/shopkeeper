<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = [
        'name',
        'inventory_count',
        'price_per',
        'source',
        'product_id'
    ];

    protected $casts = [
        'inventory_count' => 'integer',
        'price_per' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

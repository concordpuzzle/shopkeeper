<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['title', 'sale_price', 'user_id', 'woo_category_id', 'woo_category_name'];

    public function materials()
    {
        return $this->hasMany(Material::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

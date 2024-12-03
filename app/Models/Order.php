<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'status',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address_line1',
        'shipping_address_line2',
        'shipping_city',
        'shipping_state',
        'shipping_postal_code',
        'shipping_country',
        'total_amount',
        'options',
        'notes',
        'ordered_at',
        'processed_at',
        'shipped_at',
    ];

    protected $casts = [
        'options' => 'array',
        'ordered_at' => 'datetime',
        'processed_at' => 'datetime',
        'shipped_at' => 'datetime',
    ];

    // Helper method to get full shipping address
    public function getFullShippingAddressAttribute()
    {
        $address = $this->shipping_address_line1;
        if ($this->shipping_address_line2) {
            $address .= ', ' . $this->shipping_address_line2;
        }
        return sprintf(
            '%s, %s, %s %s, %s',
            $address,
            $this->shipping_city,
            $this->shipping_state,
            $this->shipping_postal_code,
            $this->shipping_country
        );
    }
}

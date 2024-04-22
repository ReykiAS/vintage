<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $fillable = [
        'delivery_details',
        'payment_details',
        'shipping_fee',
        'qty',
        'weight',
        'protection_fee',
        'total',
        'product_id',
        'origin',
        'destination',
        'courier',
        'cart_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function rating()
    {
        return $this->hasOne(Rating::class);
    }
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}

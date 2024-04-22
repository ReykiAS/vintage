<?php

namespace App\Models;

use App\Enums\ProductStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'total',
        'snap_token',
        'user_id',
        'status',
        'order_detail_id',
        'product_id',
        'qty',
        'order',
        'cart_id',
    ];

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    protected $casts = [
        'status' => ProductStatusEnum::class,
    ];
}

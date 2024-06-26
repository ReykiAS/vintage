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
        'tracking_number',
        'cart_id',
        'order_id'
    ];

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    protected $casts = [
        'status' => ProductStatusEnum::class,
    ];
    
    public function updateShippingStatus($trackingNumber, $status)
    {
        $this->tracking_number = $trackingNumber;
        $this->status = $status;
        $this->save();
    }

}

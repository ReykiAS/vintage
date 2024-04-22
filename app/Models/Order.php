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
    ];

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    protected $casts = [
        'status' => ProductStatusEnum::class,
    ];

    public function updateShippingStatus(Request $request, $orderId)
    {
        $request->validate([
            'tracking_number' => 'required|string|max:255',
        ]);

        $order = Order::find($orderId);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $trackingNumber = $request->input('tracking_number');
        
        // Deklarasi status di dalam controller
        $status = 'shipping';

        $order->updateShippingStatus($trackingNumber, $status);

        return response()->json(['message' => 'Order updated successfully', 'order' => $order]);
    }
}

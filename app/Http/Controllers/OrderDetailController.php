<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Cart;
use App\Models\OrderDetail;
use App\Models\Order;
use App\Traits\RajaOngkirTrait;
use App\Traits\MidtransTrait;
use Illuminate\Http\Request;
use Midtrans\Transaction;
use Midtrans\Config;
use Illuminate\Support\Facades\Log;

class OrderDetailController extends Controller
{
    use RajaOngkirTrait, MidtransTrait;

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'cart_id' => 'nullable|integer',
            'product_id' => 'required_without:cart_id|integer',
            'delivery_details' => 'nullable|string',
            'payment_details' => 'nullable|string',
            'protection_fee' => 'required|numeric',
            'destination' => 'required|numeric',
            'courier' => 'required|string'
        ]);

        $product = null;
        $qty = 1;

        if ($validatedData['cart_id'] !== null) {
            $cartItem = $request->user()->carts()->find($validatedData['cart_id']);
            if ($cartItem) {
                $product = $cartItem->product;
                $validatedData['product_id'] = $product->id;
                $qty = $cartItem->qty;
            } else {
                return response()->json(['message' => 'Cart item not found'], 404);
            }
        } elseif ($validatedData['product_id'] !== null) {
            $product = Product::findOrFail($validatedData['product_id']);
        } else {
            return response()->json(['message' => 'Either cart_id or product_id must be provided'], 400);
        }
        if ($product) {
            $totalPrice = ($product->price * $qty);

            // Ambil weight dari product
            $weight = $product->weight;
            $origin = 114;
            $shippingCost = $this->getShippingCost(
                $origin,
                $validatedData['destination'],
                $weight, // Menggunakan weight dari product
                $validatedData['courier']
            );

            if ($shippingCost === null) {
                return response()->json(['message' => 'Failed to get shipping cost'], 500);
            }

            $total = $totalPrice + $shippingCost + $validatedData['protection_fee'];

            $orderDetailData = [
                'product_id' => $validatedData['product_id'],
                'delivery_details' => $validatedData['delivery_details'],
                'protection_fee' => $validatedData['protection_fee'],
                'weight' => $weight, // Menggunakan weight dari product
                'total' => $total,
                'qty' => $qty,
                'shipping_fee' => $shippingCost,
                'origin' => $origin,
                'destination' => $validatedData['destination'],
                'courier' => $validatedData['courier'],
            ];

        $orderDetail = OrderDetail::create($orderDetailData);
        $order = Order::create([
            'user_id' => $request->user()->id,
            'status' => 'new',
            'product_id' => $validatedData['product_id'],
            'order' => $product->price,
            'qty' => $qty,
            'order_detail_id' => $orderDetail->id,
            'cart_id'=> $validatedData['cart_id'],
        ]);

        $order_id = 'ORDER-' . $order->id . '-' . time();
            $this->initializeMidtrans();

        $order->order_id = $order_id;
        $product->save();

            // Prepare transaction details for Midtrans
            $transaction_details = [
                'order_id' => $order_id,
                'gross_amount' => $total,
            ];


            $transaction = [
                'transaction_details' => $transaction_details,
            ];

            try {
                // Define transaction parameters
                $params = array(
                    'transaction_details' => array(
                        'order_id' => $order_id, // Use your order ID here
                        'gross_amount' => $total,
                    ),
                    'customer_details' => array(
                        'first_name' => $request->user()->name,
                        'email' => $request->user()->email,
                    ),
                );

                // Get Snap Token from Midtrans
                $snapToken = \Midtrans\Snap::getSnapToken($params);

                // Log Snap Token Response
                if (!empty($snapToken)) {
                    Log::info('Snap Token Response: ', ['snapToken' => $snapToken]);
                }

                // Update order status and save Snap Token
                $order->snap_token = $snapToken;
                $order->save();
                $redirectUrl = \Midtrans\Snap::createTransaction($params)->redirect_url;

                return response()->json([
                    'snapToken' => $snapToken,
                    'redirectUrl' => $redirectUrl
                ]);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }
    }

    public function handlePaymentNotification(Request $request)
    {
    $transactionStatus = $request->input('transaction_status');
    $orderId = $request->input('order_id');
    $fraudStatus = $request->input('fraud_status');

    // Retrieve the order from the database
    $order = Order::where('order_id', $orderId)->first();

    if ($order) {
        // Update order status based on transaction status and fraud status
        if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
            // Kurangi stok produk
            $product = Product::find($order->product_id);
            if ($product) {
                $product->qty -= $order->qty;
                $product->save();
            }
            Cart::where('id', $order->cart_id)->delete();
            $order->status = 'Onprocess';
        } else if ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
            $order->status = 'Failed';
        } else if ($transactionStatus == 'pending') {
            $order->status = 'Pending';
        }

        $order->save();
    }

    return response()->json(['message' => 'Payment notification received']);
}



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // TODO: Implement show method to display order details
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // TODO: Implement destroy method to handle order cancellation
    }

    public function updateShippingStatus(Request $request, $orderId)
    {
        $request->validate([
            'tracking_number' => 'required|int',
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
    public function completePurchase($orderId)
    {
        $order = Order::find($orderId);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->status = 'Done';
        $order->save();

        return response()->json(['message' => 'Purchase completed successfully', 'order' => $order]);
    }
}

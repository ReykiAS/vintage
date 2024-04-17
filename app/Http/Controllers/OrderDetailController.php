<?php

namespace App\Http\Controllers;

use App\Models\Product;
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
            'product_id' => 'required_if:cart_id,null|integer',
            'cart_id' => 'nullable|integer',
            'address' => 'required|string',
            'delivery_details' => 'nullable|string',
            'payment_details' => 'nullable|string',
            'protection_fee' => 'required|numeric',
            'origin' => 'required|numeric',
            'destination' => 'required|numeric',
            'weight' => 'required|numeric',
            'courier' => 'required|string'
        ]);

        $product = null;
        $qty = 1;

        // Check if cart_id exists
        if ($validatedData['cart_id'] !== null) {
            $cartItem = $request->user()->carts()->find($validatedData['cart_id']);
            if ($cartItem) {
                $product = $cartItem->product;
                $validatedData['product_id'] = $product->id; // Set product_id from the cart
                $qty = $cartItem->qty;
            }
        } elseif ($validatedData['product_id'] !== null) {
            $product = Product::findOrFail($validatedData['product_id']);
        }

        if ($product) {
            $totalPrice = ($product->price * $qty);

            $shippingCost = $this->getShippingCost(
                $validatedData['origin'],
                $validatedData['destination'],
                $validatedData['weight'],
                $validatedData['courier']
            );

            if ($shippingCost === null) {
                return response()->json(['message' => 'Failed to get shipping cost'], 500);
            }

            $total = $totalPrice + $shippingCost + $validatedData['protection_fee'];

            $order = Order::create([
                'user_id' => $request->user()->id,
                'status' => 'new', // Change status to 'Pending' before payment
                'total' => $total,
            ]);

            $orderDetailData = array_merge($validatedData, [
                'price' => $product->price,
                'qty' => $qty,
                'order_id' => $order->id,
                'shipping_fee' => $shippingCost,
            ]);
            $orderDetail = OrderDetail::create($orderDetailData);
            $this->initializeMidtrans();

            // Prepare transaction details for Midtrans
            $transaction_details = [
                'order_id' => $order->id,
                'gross_amount' => $total,
            ];

            $transaction = [
                'transaction_details' => $transaction_details,
            ];

            try {
                // Define transaction parameters
                $params = array(
                    'transaction_details' => array(
                        'order_id' => $order->id, // Use your order ID here
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
        $order = Order::where('id', $orderId)->first();

        if ($order) {
            // Update order status based on transaction status and fraud status
            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'challenge') {
                    // Handle fraud challenge
                } else if ($fraudStatus == 'accept') {
                    // Handle fraud accept
                }
                $order->status = 'Onprocess';
            } else if ($transactionStatus == 'settlement') {
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
        // TODO: Implement update method to handle payment status/callback from Midtrans
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // TODO: Implement destroy method to handle order cancellation
    }
}

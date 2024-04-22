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
        try {
            $validatedData = $request->validate([
                'product_id' => 'required_if:cart_id,null|integer',
                'cart_id' => 'nullable|integer',
                'delivery_details' => 'nullable|string',
                'payment_details' => 'nullable|string',
                'protection_fee' => 'required|numeric',
                'origin' => 'required|numeric',
                'destination' => 'required|numeric',
                'courier' => 'required|string'
            ]);

            // Set default origin and protection_fee
            $validatedData['origin'] = 114;
            $validatedData['protection_fee'] = 10;

            // Initialize variables
            $orderDetails = [];
            $totalPrice = 0;

            // Process cart items or single product
            if ($validatedData['cart_id'] !== null) {
                // Retrieve cart items for the user
                $cartItems = $request->user()->carts()->whereIn('id', (array)$validatedData['cart_id'])->get();

                // Calculate total price from cart items
                foreach ($cartItems as $cartItem) {
                    $product = $cartItem->product;
                    $totalPrice += $product->price * $cartItem->qty;

                    // Store order details for each cart item
                    $orderDetails[] = [
                        'product_id' => $product->id,
                        'qty' => $cartItem->qty,
                        'price' => $product->price,
                        'shipping_fee' => $this->getShippingCost($validatedData['origin'], $validatedData['destination'], $product->weight * $cartItem->qty, $validatedData['courier']),
                        'cart_id' => $cartItem->id,
                    ];
                }
            } elseif ($validatedData['product_id'] !== null) {
                // If product_id is provided directly
                $product = Product::findOrFail($validatedData['product_id']);
                $totalPrice += $product->price;

                // Store order details for the single product
                $orderDetails[] = [
                    'product_id' => $product->id,
                    'qty' => 1,
                    'price' => $product->price,
                    'shipping_fee' => $this->getShippingCost($validatedData['origin'], $validatedData['destination'], $product->weight, $validatedData['courier']),
                    'cart_id' => null,
                ];
            }

            // Calculate total cost including protection fee
            $total = $totalPrice + $validatedData['protection_fee'];

            // Create a new order
            $order = Order::create([
                'user_id' => $request->user()->id,
                'status' => 'new',
                'total' => $total,
            ]);

            // Create order details for each product
            foreach ($orderDetails as $detail) {
                $order->orderDetails()->create([
                    'delivery_details' => $validatedData['delivery_details'],
                    'payment_details' => $validatedData['payment_details'],
                    'protection_fee' => $validatedData['protection_fee'],
                    'origin' => $validatedData['origin'],
                    'destination' => $validatedData['destination'],
                    'courier' => $validatedData['courier'],
                    'qty' => $detail['qty'],
                    'weight' => $detail['qty'] * Product::find($detail['product_id'])->weight,
                    'price' => $detail['price'],
                    'shipping_fee' => $detail['shipping_fee'],
                    'product_id' => $detail['product_id'],
                    'cart_id' => $detail['cart_id'],
                ]);
            }

            // Initialize Midtrans
            $this->initializeMidtrans();

            // Prepare transaction details for Midtrans
            $params = [
                'transaction_details' => [
                    'order_id' => $order->id,
                    'gross_amount' => $order->total,
                ],
                'customer_details' => [
                    'first_name' => $request->user()->name,
                    'email' => $request->user()->email,
                ],
            ];

            // Get Snap Token from Midtrans
            $snapToken = \Midtrans\Snap::getSnapToken($params);

            // Update order status and save Snap Token
            $order->update(['snap_token' => $snapToken]);

            return response()->json([
                'snapToken' => $snapToken,
                'redirectUrl' => \Midtrans\Snap::createTransaction($params)->redirect_url
            ]);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error in storing order: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to store order.'], 500);
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
                $order->save();

                // Handle product quantity deduction
                $orderDetails = $order->orderDetails;
                foreach ($orderDetails as $orderDetail) {
                    $product = Product::find($orderDetail->product_id);
                    if ($product) {
                        if ($orderDetail->cart_id !== null) {
                            // If ordered from cart, delete the cart item
                            $cartItem = $request->user()->carts()->find($orderDetail->cart_id);
                            if ($cartItem) {
                                $cartItem->delete();
                            }
                        }
                        // Decrease product quantity
                        $product->quantity -= $orderDetail->qty;
                        $product->save();
                    }
                }
            } else if ($transactionStatus == 'settlement') {
                $order->status = 'Onprocess';
                $order->save();
            } else if ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
                $order->status = 'Failed';
                $order->save();
            } else if ($transactionStatus == 'pending') {
                $order->status = 'Pending';
                $order->save();
            }
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

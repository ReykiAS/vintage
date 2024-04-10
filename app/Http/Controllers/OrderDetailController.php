<?php

namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\OrderDetail;
use App\Models\Order;
use App\Models\CartItem;
use Illuminate\Http\Request;

class OrderDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

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
        'shipping_fee' => 'required|integer',
        'protection_fee' => 'required|numeric',
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
        $order = Order::create([
            'user_id' => $request->user()->id,
            'status' => 'Onprocess',
            'total' => $totalPrice + $validatedData['shipping_fee'] + $validatedData['protection_fee'],
        ]);

        $orderDetailData = array_merge($validatedData, [
            'price' => $product->price,
            'qty' => $qty,
            'order_id' => $order->id,
        ]);
        $orderDetail = OrderDetail::create($orderDetailData);

        if ($validatedData['cart_id'] !== null) {
            $product->qty -= $qty;
            $product->save();
            $cartItem->delete();
        }
        elseif($validatedData['cart_id'] === null) {
            $product->qty -= 1;
            $product->save();
        }
    }

    // Redirect to WhatsApp API
    $whatsappApiUrl = 'https://api.whatsapp.com/send?phone=1234567890&text=Hello,%20I%20want%20to%20pay%20for%20my%20order';
    return redirect()->away($whatsappApiUrl);
}




    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

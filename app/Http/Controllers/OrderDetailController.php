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
        // Validate incoming request data
        $validatedData = $request->validate([
            'product_id' => 'required|integer',
            'cart_id' => 'required|integer',
            'address' => 'required|string',
            'delivery_details' => 'nullable|string',
            'payment_details' => 'nullable|string',
            'shipping_fee' => 'required|integer',
            'protection_fee' => 'required|numeric',
        ]);

        // Get product quantity based on cart items or default to 1
        $qty = $this->getQuantityFromCart($request->user(), $validatedData['product_id']);

        // Retrieve product
        $product = Product::findOrFail($validatedData['product_id']);

        // Calculate order total
        $orderTotal = ($product->price * $qty) + $validatedData['shipping_fee'] + $validatedData['protection_fee'];

        // Create a new OrderDetail instance
        $orderDetailData = array_merge($validatedData, ['total' => $orderTotal, 'qty' => $qty]);
        $orderDetail = OrderDetail::create($orderDetailData);

        // Update product stock
        $product->qty -= $qty;
        $product->save();

        // Update the status of the corresponding order
        $orderId = $orderDetail->order_id;
        $order = Order::findOrFail($orderId);
        $order->status = 'processing'; // You can set the initial status as per your requirements
        $order->save();

        // Redirect to WhatsApp API
        $whatsappApiUrl = 'https://api.whatsapp.com/send?phone=1234567890&text=Hello,%20I%20want%20to%20pay%20for%20my%20order';
        return redirect()->away($whatsappApiUrl);
    }

    protected function getQuantityFromCart($user, $productId)
    {
        if ($user->Carts()->exists()) {
            $cartItem = $user->Carts()->where('product_id', $productId)->first();
            if ($cartItem) {
                return $cartItem->qty;
            }
        }
        return 1;
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

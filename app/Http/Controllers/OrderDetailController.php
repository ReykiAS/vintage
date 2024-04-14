<?php

namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\OrderDetail;
use App\Models\Order;
use App\Models\Cart;
use Illuminate\Http\Request;
use App\Http\Requests\OrderDetailStoreRequest;

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
    public function store(OrderDetailStoreRequest $request)
{
    $validatedData = $request->validated();

    // Mengambil product_id dan qty dari Cart jika cart_id tersedia
    if ($request->filled('cart_id')) {
        $cartItem = Cart::find($validatedData['cart_id']);
        if ($cartItem) {
            $product = $cartItem->product;
            $qty = $cartItem->qty;
        }
    } else {
        // Mengambil product_id dan qty dari permintaan langsung jika tidak ada cart_id
        $product = Product::findOrFail($validatedData['product_id']);
        $qty = $validatedData['qty'] ?? 1;
    }

    // Lanjutkan dengan menyimpan data jika product tersedia
    if ($product) {
        // Kurangi stok product
        $product->qty -= $qty;
        $product->save();

        // Buat order baru
        $order = Order::create([
            'user_id' => $request->user()->id,
            'status' => 'Onprocess',
            'total' => ($product->price * $qty) + $validatedData['shipping_fee'] + $validatedData['protection_fee'],
        ]);

        // Buat detail order
        $orderDetailData = [
            'product_id' => $product->id,
            'address' => $validatedData['address'],
            'qty' => $qty,
            'delivery_details' => $validatedData['delivery_details'],
            'payment_details' => $validatedData['payment_details'],
            'price' => $product->price,
            'order_id' => $order->id,
            'shipping_fee' => $validatedData['shipping_fee'],
            'protection_fee' => $validatedData['protection_fee'],
        ];
        OrderDetail::create($orderDetailData);

        // Hapus item dari cart jika dipesan dari cart
        if ($request->filled('cart_id')) {
            $cartItem->delete();
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

<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartRequest;
use App\Http\Resources\CartResource;
use App\Http\Resources\ProductResource;
use App\Models\Cart;
use App\Models\Product;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth('sanctum')->user();
        $cart = new Cart;

        if ($user){
            $cart = $cart->where('user_id', $user->id);
        }
        $cart = $cart->get();
        return CartResource::collection($cart);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CartRequest $request)
    {
        $validated = $request->validated();

        // Retrieve product details (assuming a relationship between Cart and Product)
        $product = Product::findOrFail($validated['product_id']);

        // Check available quantity
        if ($product->qty < $validated['qty']) {
            return response()->json(['error' => 'Insufficient stock available'], 400);
        }

        // Create cart item only if stock is sufficient
        $cart = Cart::create($validated);

        return response()->json(['message' => 'Cart added successfully'], 201);
        }

    /**
     * Display the specified resource.
     */
    public function show(Cart $cart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CartRequest $request, string $id)
    {
        $validated = $request->validated();

        // Retrieve product details (assuming a relationship between Cart and Product)
        $product = Product::findOrFail($validated['product_id']);

        // Check available quantity
        if ($product->qty < $validated['qty']) {
            return response()->json(['error' => 'Insufficient stock available'], 400);
        }

        $cart = Cart::findOrFail($id);

        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }

        $cart->update($validated);

        return response()->json(['message' => 'Cart succesfully updated', 'cart' => CartResource::make($cart)]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $cart = Cart::findOrFail($id);
        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }
        $cart->delete();

        return response()->json(['message' => 'Cart deleted successfully'], 200);
    }
}

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

        $validated['user_id'] = $request->user()->id;

        // Check if product already exists in user's cart
        $existingCartItem = Cart::where('user_id', $validated['user_id'])
                                ->where('product_id', $validated['product_id'])
                                ->first();

        if ($existingCartItem) {
            // Update quantity for existing item
            $existingCartItem->qty += $validated['qty'];
            if ($product->qty < $existingCartItem->qty) {
                return response()->json(['error' => 'Insufficient stock available'], 400);
            }
            $existingCartItem->save();
            $message = 'Cart item quantity updated.';
        } else {
            // Create a new cart item
            $cart = Cart::create($validated);
            $message = 'Cart item added successfully.';
        }

        return response()->json(['message' => $message], 201);
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

        $cart = Cart::find($id);

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
        $cart = Cart::find($id);
        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }
        $cart->delete();

        return response()->json(['message' => 'Cart deleted successfully'], 200);
    }
}

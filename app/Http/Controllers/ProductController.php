<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index( Request $request)
    {
        $products = Product::with('category', 'brand', 'images', 'user');


        if ($request->has('search')) {
            $products = $products->where('name', 'like', '%' . $request->search . '%');
        }

         // check if the user is filtering by brand
         if ($request->has('brand')) {
            $products = $products->where('brand_id', $request->brand);
        }

        // check if the user is filtering by price range
        if ($request->has('min_price') && $request->has('max_price')) {
            $products = $products->whereBetween('price', [$request->min_price, $request->max_price]);
        }

        // check if the user is sorting the products by name, and price in ascending or descending
        if ($request->has('sort_by')) {
            $sortOrder = $request->has('sort_order') ? $request->sort_order : 'asc';
            $products = $products->orderBy($request->sort_by, $sortOrder);
        }

        // pagination mechanism
        $products = $products->paginate(10);

       return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductStoreRequest $request)
    {
        $validated = $request->validated();
        $product = Product::create($validated);

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('photos');
            $product->addImage($photoPath);
        }

        return response()->json(['message' => 'Product created successfully'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::with('image')->find($id);

        if ($product) {
            return ProductResource::make($product)->withDetail();
        } else {
            return response()->json(['message' => 'Product not found'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductUpdateRequest $request, string $id)
    {
        $validated = $request->validated();
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        $product->update($validated);
        $product->updateImage($request);

        return response()->json(['message' => 'Product succesfully updated', 'product' => ProductResource::make($product)->withDetail()]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }

    /**
     * Show resource from storage where already deleted.
     */
    public function showSoftDeleted()
    {
        $deletedProduct = Product::onlyTrashed()->get();
        return response()->json($deletedProduct);
    }

    public function restore($id)
    {
        $product = Product::withTrashed()->find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        $product->restore();

        return response()->json(['message' => 'Product succesfully restored.']);
    }
}

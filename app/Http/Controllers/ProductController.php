<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Variant;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Product $products)
    {
        $products->loadMissing('category', 'brand', 'images', 'user','variants');
        $products = Product::filter($request)->paginate(20);
        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductStoreRequest $request)
    {
        $validated = $request->validated();

        $validated['user_id'] = auth()->id(); // atau auth()->user()->id

        $product = Product::create($validated);

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('photos');
            $product->addImage($photoPath);
        }
        if ($request->has('variants') && is_array($request->variants)) {
            foreach ($request->variants as $variant) {
                $product->variants()->create([
                    'size' => $variant['size'],
                    'quality' => $variant['quality'],
                ]);
            }
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
        $product->fill($validated)->save();

        $product->updateImage($request);
        if ($request->has('variants')) {
            foreach ($request->variants as $variantData) {
                if (isset($variantData['id'])) {
                    $variant = Variant::find($variantData['id']);

                    if ($variant && $variant->product_id === $product->id) {
                        $variant->update([
                            'size' => $variantData['size'],
                            'quality' => $variantData['quality'],
                        ]);
                    }
                } else {
                    $product->variants()->create([
                        'size' => $variantData['size'],
                        'quality' => $variantData['quality'],
                    ]);
                }
            }
        }

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

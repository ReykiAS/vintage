<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Http\Resources\BrandResource;
use App\Http\Requests\BrandStoreRequest as RequestsBrandStoreRequest;
use App\Http\Requests\BrandUpdateRequest as RequestsBrandUpdateRequest;

class BrandController extends Controller
{
    /* *
     * Display a listing of the resource.
     */
    public function index()
    {
        $brand = Brand::all();
        return BrandResource::collection($brand);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RequestsBrandStoreRequest $request)
    {
        $validated = $request->validated();
        $brand = Brand::create($validated);

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('photos');
            $brand->addImage($photoPath);
        }

        return response()->json(['message' => 'Brand created successfully'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $brand = Brand::with('image')->find($id);

        if ($brand) {
            return BrandResource::make($brand)->withDetail();
        } else {
            return response()->json(['message' => 'Brand not found'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RequestsBrandUpdateRequest $request, string $id)
    {
        $validated = $request->validated();
        $brand = Brand::find($id);

        if (!$brand) {
            return response()->json(['message' => 'Brand not found'], 404);
        }
        $brand->update($validated);
        $brand->updateImage($request);

        return response()->json(['message' => 'Brand succesfully updated', 'brand' => BrandResource::make($brand)->withDetail()]);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $brand = brand::find($id);
        if (!$brand) {
            return response()->json(['message' => 'Brand not found'], 404);
        }

        $brand->products()->delete();
        $brand->delete();

        return response()->json(['message' => 'Brand deleted successfully']);
    }

    /**
     * Restore the removed resource from storage.
     */
    public function restore($id)
    {
        $brand = Brand::withTrashed()->find($id);
        if (!$brand) {
            return response()->json(['message' => 'Brand not found'], 404);
        }
        $brand->products()->restore();
        $brand->restore();

        return response()->json(['message' => 'Brand succesfully restored.']);
    }

    public function showSoftDeleted()
    {
        $deletedBrand = Brand::onlyTrashed()->get();
        return response()->json($deletedBrand);
    }
}

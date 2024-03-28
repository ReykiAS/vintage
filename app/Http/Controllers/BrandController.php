<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use App\Http\Requests\BrandStoreRequest as RequestsBrandStoreRequest;

class BrandController extends Controller
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
    public function show(Brand $brand)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        //
    }
}

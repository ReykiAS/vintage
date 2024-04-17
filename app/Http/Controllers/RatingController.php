<?php

namespace App\Http\Controllers;

use App\Http\Requests\RatingStoreRequest;
use App\Http\Resources\RatingResource;
use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Rating $ratings)
    {
        $ratings = Rating::with('user', 'product', 'order')->get();
        return RatingResource::collection($ratings);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RatingStoreRequest $request)
    {
        $validated = $request->validated();

        // Check for existing rating for the same order detail and user
        $existingRating = Rating::where('order_detail_id', $validated['order_detail_id'])
                                ->where('product_id', $validated['product_id'])
                                ->where('user_id', auth()->id())
                                ->first();

        if ($existingRating) {
            return response()->json(['message' => 'You have already rated this product for this order'], 422);
        }

        // Create the rating if it doesn't exist
        $product = Rating::create($validated);

        return response()->json(['message' => 'Rating created successfully'], 201);
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

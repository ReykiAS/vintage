<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;
use App\Http\Requests\FavoriteStoreRequest;
use App\Http\Resources\FavoriteResource;

class FavoriteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth('sanctum')->user();
        $favorite = new Favorite;

        if ($user){
            $favorite = $favorite->where('user_id', $user->id);
        }
        $favorite = $favorite->get();
        return FavoriteResource::collection($favorite);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FavoriteStoreRequest $request)
    {
        $validated = $request->validated();
        $favorite = Favorite::create($validated);

        return response()->json(['message' => 'Favorite created successfully'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Favorite $favorite)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Favorite $favorite)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $favorite = Favorite::find($id);
        if (!$favorite) {
            return response()->json(['message' => 'Favorite not found'], 404);
        }
        $favorite->delete();

        return response()->json(['message' => 'Favorite deleted successfully'], 200);
    }
}

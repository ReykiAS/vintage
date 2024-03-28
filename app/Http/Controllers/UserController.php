<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Image;
use App\Http\Requests\UserStoreRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
class UserController extends Controller
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
    public function store(UserStoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $user = User::create($validated);

            $token = $user->createToken('user_token')->plainTextToken;

        return response()->json(['message' => 'User created successfully', 'token' => $token], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error occurred while creating user: ' . $e->getMessage()], 500);
        }
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

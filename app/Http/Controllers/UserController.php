<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Image;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
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
    public function login(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $user = User::where('email', $request->email)->first();
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }
        return $user->createToken('user login')->plainTextToken;
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
    public function update(UserUpdateRequest $request, string $id)
{
    $validated = $request->validated();
    $user = User::find($id);

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    $user->update($validated);
    if ($request->hasFile('photo')) {
        $user->updateImage($request);
    }

    return response()->json(['message' => 'User updated successfully', 'user' => $user]);
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

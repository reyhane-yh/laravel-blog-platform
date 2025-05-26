<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index()
    {
        return UserResource::collection(User::all());
    }

    public function register(RegisterRequest $request)
    {
        // Validation
        $validated_data = $request->validated();

        $user = User::createUser($validated_data);

        if ($user) {
            return (new UserResource($user))
            ->additional(['message' => 'User registered successfully.'])
            ->response()
            ->setStatusCode(201);
        }

        // Registration failed
        return response()->json(
            ['message' => 'Registration failed'],
            500);
    }

    public function login(LoginRequest $request)
    {
        // Validation
        $validated_data = $request->validated();

        $user = User::findByUsername($validated_data['username']);

        if (!$user || !Hash::check($validated_data['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials.']
                , 401);
        }

        $plainTextToken = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'id' => $user->id,
            'access_token' => $plainTextToken,
        ]);
    }

    public function logout()
    {
        // Get the authenticated user
        $user = Auth::user();
        //Log::info($user);

        if (!$user) {
            return response()->json(['message' => 'Not authenticated.'], 401);
        }

        // Delete the user's api token
        $user->currentAccessToken()->delete();

        return response()->json(['message' => 'Successfully logged out.']);
    }

    public function show()
    {
        // Get the authenticated user
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'Not authenticated.']
            , 401);
        }

        // Return the user data
        return new UserResource($user);
    }
}

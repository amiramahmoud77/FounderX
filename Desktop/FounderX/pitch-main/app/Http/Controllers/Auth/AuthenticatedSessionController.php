<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    public function store(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.'
            ], 401);
        }

        if (!$user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Please verify your email before logging in.',
                'verified' => false,
                'user_id' => $user->id
            ], 403);
        }

        if (!$user->hasPassword()) {
            return response()->json([
                'message' => 'Please set your password before logging in.',
                'needs_password' => true,
                'user_id' => $user->id
            ], 403);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.'
            ], 401);
        }

        $user = $request->user();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'verified' => true,
            'message' => 'Logged in successfully'
        ]);
    }

    public function destroy(Request $request): JsonResponse
    {
        try {
            if (!$request->user()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No authenticated user'
                ], 401);
            }
            $request->user()->tokens()->delete();
            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed. Please try again.'. $e->getMessage()
            ], 500);
        }
    }
}

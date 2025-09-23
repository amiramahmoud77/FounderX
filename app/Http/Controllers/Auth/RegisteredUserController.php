<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
    'name' => ['required', 'string', 'max:255'],
    'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
]);

        try {
            $verificationCode = rand(1000, 9999);
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => null,
                'role' => 'founder',
                'email_verified_at' => null,
                'verification_code' => $verificationCode,
                'verification_code_sent_at' => now(),
            ]);
            event(new Registered($user));
            $user->notify(new \App\Notifications\VerificationCodeNotification($verificationCode));
            Log::info('User registered and verification code sent', [
                'user_id' => $user->id,
                'email' => $user->email,
                'verification_code' => $verificationCode
            ]);

            return response()->json([
                'message' => 'Registration successful. Verification code sent to your email.',
                'user_id' => $user->id,
                'needs_verification' => true
            ], 201);
        } catch (\Exception $e) {
            Log::error('Registration failed', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Registration failed: ' . $e->getMessage()
            ], 500);
        }
    }

    private function sendVerificationCode($user, $code)
    {
        $user->notify(new \App\Notifications\VerificationCodeNotification($code));
    }
}

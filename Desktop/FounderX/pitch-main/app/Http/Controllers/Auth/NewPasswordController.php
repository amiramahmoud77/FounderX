<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class NewPasswordController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'string', 'min:4', 'max:4'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        $user = User::where('email', $request->email)->first();
        if (!$user || $user->password_reset_code !== $request->code) {
            throw ValidationException::withMessages([
                'code' => ['The provided code is invalid.'],
            ]);
        }
        if ($user->password_reset_code_sent_at->diffInMinutes(now()) > 60) {
            throw ValidationException::withMessages([
                'code' => ['The code has expired.'],
            ]);
        }
        $user->password = Hash::make($request->password);
        $user->password_reset_code = null;
        $user->password_reset_code_sent_at = null;
        $user->save();
        event(new PasswordReset($user));
        return response()->json(['status' => 'Password reset successfully.']);
    }
}

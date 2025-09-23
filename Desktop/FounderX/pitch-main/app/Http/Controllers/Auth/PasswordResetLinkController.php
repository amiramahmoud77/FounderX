<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PasswordResetLinkController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['status' => 'If the email exists, a reset code has been sent.']);
        }
        $user->sendPasswordResetCode();
        return response()->json(['status' => 'If the email exists, a reset code has been sent.']);
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->away(
                config('http://com.example.app', 'http://localhost:3000') .'/login?verified=1&message=Email already verified'
            );
        }
        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }
        return redirect()->away(
            config('http://com.example.app', 'http://localhost:3000') .'/login?verified=1&message=Email verified successfully'
        );
    }
}

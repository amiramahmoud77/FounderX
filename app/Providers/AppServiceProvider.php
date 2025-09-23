<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;
    use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\App;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
{
    ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
        return config('http://com.example.app')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
    });

    if (isset($_SERVER['HTTP_X_ORIGINAL_HOST'])) {
        URL::forceRootUrl('https://'.$_SERVER['HTTP_X_ORIGINAL_HOST']);
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
        URL::forceRootUrl('https://'.$_SERVER['HTTP_X_FORWARDED_HOST']);
    }
    config(['session.domain' => '.ngrok-free.app']);
    config(['sanctum.stateful' => [$_SERVER['HTTP_X_ORIGINAL_HOST'] ?? $_SERVER['HTTP_X_FORWARDED_HOST'] ?? 'localhost']]);
}
}


<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\PitchController;
use App\Http\Controllers\PitchTextController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});


require __DIR__.'/auth.php';

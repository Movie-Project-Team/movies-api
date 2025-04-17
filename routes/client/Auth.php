<?php

use App\Http\Controllers\Client\AuthController;
use App\Http\Controllers\Client\SocialAuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('/register', 'register')->name('auth.register');
        Route::post('/login', 'login')->name('auth.login');
        Route::post('/change/password', 'changePasswordUser')->name('auth.changePassword');
        Route::post('/verify/password', 'verifyOTPUser')->name('auth.verifyOtp');
    });

    Route::prefix('google')->controller(SocialAuthController::class)->group(function () {
        Route::get('/redirect', 'redirectToGoogle')->name('auth.google.redirect');
        Route::get('/callback', 'handleGoogleCallback')->name('auth.google.callback');
    });
});
<?php

use App\Http\Controllers\Client\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('profile')->middleware('auth')->group(function () {
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/{id}', 'getListProfile')->name('profile.list');
        Route::get('/info/{id}', 'getProfile')->name('profile.detail');
        Route::post('/change/password', 'changePasswordProfile')->name('profile.changePassword');
        Route::post('/verify/password', 'verifyPasswordProfile')->name('profile.verifyPassword');
    });
});;
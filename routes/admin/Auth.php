<?php

use App\Http\Controllers\AdminAuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::controller(AdminAuthController::class)->group(function () {
        Route::post('/login', 'loginAdmin')->name('auth.login');
        Route::post('/logout', 'logoutAdmin')->name('auth.logout');
    });
});;
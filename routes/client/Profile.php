<?php

use App\Http\Controllers\Client\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('profile')->group(function () {
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/{id}', 'getListProfile')->name('profile.list');
        Route::get('/info/{id}', 'getProfile')->name('profile.detail');
        Route::post('/change/password', 'changePasswordProfile')->name('profile.changePassword');
        Route::post('/verify/password', 'verifyPasswordProfile')->name('profile.verifyPassword');
        Route::post('/favorite/create', 'addFavorite')->name('profile.addFavorite');
        Route::delete('/favorite/delete', 'removeFavorite')->name('profile.removeFavorite');
    });
});
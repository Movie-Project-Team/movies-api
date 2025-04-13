<?php

use App\Http\Controllers\Client\RoomController;
use Illuminate\Support\Facades\Route;

Route::prefix('room')->group(function () {
    Route::controller(RoomController::class)->group(function () {
        Route::get('/', 'list')->name('room.list');
        Route::post('/create', 'create')->name('room.create');
        Route::post('/verify', 'verify')->name('room.verify');
    });
});;
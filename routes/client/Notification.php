<?php

use App\Http\Controllers\Client\NotificationController;
use Illuminate\Support\Facades\Route;

Route::prefix('notification')->group(function () {
    Route::controller(NotificationController::class)->group(function () {
        Route::get('/{profileId}', 'list')->name('notification.list');
    });
});;
<?php

use App\Http\Controllers\Client\WatchHistoryController;
use Illuminate\Support\Facades\Route;

Route::prefix('profile')->group(function () {
    Route::controller(WatchHistoryController::class)->group(function () {
        Route::get('/{profileId}/history', 'list')->name('history.list');
        Route::get('/{profileId}/history/{movieId}', 'detail')->name('history.detail');
        Route::post('/history/save', 'save')->name('history.save');
    });
});
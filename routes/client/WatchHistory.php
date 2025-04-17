<?php

use App\Http\Controllers\Client\WatchHistoryController;
use Illuminate\Support\Facades\Route;

Route::prefix('movie/history')->group(function () {
    Route::controller(WatchHistoryController::class)->group(function () {
        Route::get('/{profileId}', 'list')->name('history.list');
        Route::post('/save', 'save')->name('history.save');
        Route::get('/{profileId}/detail/{movieId}', 'detail')->name('history.detail');
    });
});;
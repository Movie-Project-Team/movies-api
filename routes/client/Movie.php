<?php

use App\Http\Controllers\Client\MovieController;
use Illuminate\Support\Facades\Route;

Route::prefix('movies')->group(function () {
    Route::controller(MovieController::class)->group(function () {
        Route::get('/', 'list')->name('movie.list');
        Route::get('/{slug}', 'detail')->name('movie.detail');
        Route::get('/ranking/{type}', 'ranking')->name('movie.ranking');
    });
});;
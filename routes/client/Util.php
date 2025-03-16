<?php

use App\Http\Controllers\Client\GenresController;
use App\Http\Controllers\Client\LanguagesController;
use Illuminate\Support\Facades\Route;

Route::prefix('util')->group(function () {
    Route::controller(GenresController::class)->group(function () {
        Route::get('/genres', 'getListGenres')->name('genres.list');
    });
    Route::controller(LanguagesController::class)->group(function () {
        Route::get('/languages', 'getListLanguages')->name('languages.list');
    });
});

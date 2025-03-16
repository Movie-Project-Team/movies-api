<?php

use App\Http\Controllers\Client\CommentController;
use Illuminate\Support\Facades\Route;

Route::prefix('comment')->group(function () {
    Route::controller(CommentController::class)->group(function () {
        Route::get('/{movieId}', 'list')->name('comment.list');
        Route::post('/create', 'create')->name('comment.create');
    });
});;
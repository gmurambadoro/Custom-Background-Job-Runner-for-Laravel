<?php

use App\Http\Controllers\BackgroundJobsController;
use Illuminate\Support\Facades\Route;

Route::controller(BackgroundJobsController::class)
    ->name('background-jobs.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/{job}/show', 'show')->name('show');
        Route::get('/{job}/retry', 'retry')->name('retry');
    });

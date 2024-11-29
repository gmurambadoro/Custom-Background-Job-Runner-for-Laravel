<?php

use App\Http\Controllers\BackgroundJobsController;
use Illuminate\Support\Facades\Route;

Route::controller(BackgroundJobsController::class)
    ->name('background-jobs.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
    });

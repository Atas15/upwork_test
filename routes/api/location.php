<?php

use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\Admin\LocationController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/admin')
    ->group(function () {
        Route::controller(AuthController::class)
            ->middleware('throttle:10,1')
            ->group(function () {
                Route::post('login', 'login');
                Route::post('logout', 'logout')->middleware('auth:sanctum');
            });

        Route::middleware('auth:sanctum')
            ->prefix('location')
            ->group(function () {
                Route::controller(LocationController::class)
                    ->group(function () {
                        Route::get('index', 'index');
                        Route::post('create', 'create');
                        Route::post('update/{id}', 'update');
                        Route::delete('delete/{id}', 'destroy');
                    });
            });
    });

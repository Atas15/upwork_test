<?php

use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\Admin\ClientController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\LocationController;
use App\Http\Controllers\Api\Admin\SkillController;
use App\Http\Controllers\Api\Admin\WorkController;
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
            ->prefix('auth')
            ->group(function () {
                Route::controller(DashboardController::class)
                    ->group(function () {
                        Route::get('dashboard', 'index');
                    });
            });

        Route::middleware('auth:sanctum')
            ->prefix('locations')
            ->group(function () {
                Route::controller(LocationController::class)
                    ->group(function () {
                        Route::get('index', 'index');
                        Route::post('create', 'create');
                        Route::post('update/{id}', 'update');
                        Route::delete('delete/{id}', 'destroy');
                    });
            });

        Route::middleware('auth:sanctum')
            ->prefix('skills')
            ->group(function () {
                Route::controller(SkillController::class)
                    ->group(function () {
                        Route::get('index', 'index');
                        Route::post('create', 'create');
                        Route::post('update/{id}', 'update');
                        Route::delete('delete/{id}', 'destroy');
                    });
            });

        Route::middleware('auth:sanctum')
            ->prefix('clients')
            ->group(function () {
                Route::controller(ClientController::class)
                    ->group(function () {
                        Route::get('index', 'index');
                    });
            });

        Route::middleware('auth:sanctum')
            ->prefix('works')
            ->group(function () {
                Route::controller(WorkController::class)
                    ->group(function () {
                        Route::get('index', 'index');
                        Route::post('create', 'store');
                        Route::post('update/{work}', 'update');
                        Route::delete('delete/{id}', 'destroy');
                    });
            });
    });

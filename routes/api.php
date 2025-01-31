<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\PromoController;
use App\Http\Controllers\Api\AmountController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('signup', [AuthController::class, 'signup']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::get('logout', [AuthController::class, 'logout']);
        Route::delete('delete', [AuthController::class, 'delete']);
    });
    Route::apiResource('company', CompanyController::class);
    Route::apiResource('promo', PromoController::class);
    Route::apiResource('amount', AmountController::class);
});

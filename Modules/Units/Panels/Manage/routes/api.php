<?php

use Illuminate\Support\Facades\Route;
use Modules\Basic\Http\Controllers\ModelController;


/**
 * مسیرهای API برای پنل manage
 */
Route::prefix('manage')->group(function () {
    // مسیرهای مربوط به کاربران
    Route::prefix('models')->group(function () {
        Route::post('/', [ModelController::class, 'query']);
    });
});

/**
 * مسیرهای API تست برای پنل manage
 */
Route::prefix('test/manage')->group(function () {
    // مسیرهای مربوط به مدل‌ها برای تست
    Route::post('/', [ModelController::class, 'query']);
});
//
//Route::middleware('auth:sanctum')->group(function () {
//    Route::post('/models', [ModelController::class, 'query']);
//});

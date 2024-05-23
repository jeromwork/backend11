<?php

use Illuminate\Support\Facades\Route;
use Modules\Orders\Http\Controllers\Control\PayController;
use Modules\Orders\Http\Controllers\Control\OrdersController;

/*
 *--------------------------------------------------------------------------
 * API Routes
 *--------------------------------------------------------------------------
 *
 * Here is where you can register API routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * is assigned the "api" middleware group. Enjoy building your API!
 *
*/




Route::prefix('v1/control')->middleware('admin')->group(function () {

    Route::post('pay-notification', [PayController::class, 'notification']);
    //todo move PayController notification
    Route::apiResources([
        'orders'=>OrdersController::class
    ]);
});

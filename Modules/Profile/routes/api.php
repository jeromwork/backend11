<?php

use Illuminate\Support\Facades\Route;
use Modules\Profile\Http\Controllers\ProfileController;
use Modules\Profile\Http\Controllers\ProfileSlotsController;

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


Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::get('/profile/slots', [ProfileSlotsController::class, 'index']);
    Route::apiResources([
        'profile'=>ProfileController::class
    ]);
});

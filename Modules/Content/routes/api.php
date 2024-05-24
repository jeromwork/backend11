<?php

use Illuminate\Support\Facades\Route;
use Modules\Content\Http\Controllers\ContentController;
use Modules\Content\Http\Controllers\Control\ContentController as ControlContentController;

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

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('content', ContentController::class)->names('content');
});


Route::middleware(['admin'])->prefix('v1/control')->group(function ($router) {
//    Route::put('content/save', [ContentController::class, 'save']);
//    Route::patch('content/save', [ContentController::class, 'save']);
    Route::apiResources([
        'content'=>ControlContentController::class
    ]);

});

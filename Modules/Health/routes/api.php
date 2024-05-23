<?php

use Illuminate\Support\Facades\Route;
use Modules\Health\Http\Controllers\Control\HealthBindsController;
use Modules\Health\Http\Controllers\Control\VariationResourceController;

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


Route::prefix('v1/control/health')->middleware('admin')->group(function() {
//    Route::get('/', 'PagesController@index');

    Route::resource('binds', HealthBindsController::class);
    Route::resource('variations', VariationResourceController::class);
});

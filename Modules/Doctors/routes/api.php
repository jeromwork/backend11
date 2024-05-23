<?php

use Modules\Doctors\Http\Controllers\Control\DoctorDiplomsResourceController;
use Modules\Doctors\Http\Controllers\Control\DoctorResourceController;
use Modules\Doctors\Http\Controllers\Control\DoctorsListController;
use Illuminate\Support\Facades\Route;

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

Route::middleware('admin')->prefix('v1/control')->group( function ($router) {
    Route::get('/doctors-list', [DoctorsListController::class, 'index']);
    Route::apiResources([
        'doctors'=>DoctorResourceController::class

    ]);
    Route::apiResources([
        'doctor-diploms'=>DoctorDiplomsResourceController::class
    ]);


});

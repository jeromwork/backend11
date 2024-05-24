<?php

use Illuminate\Support\Facades\Route;
use Modules\Reviews\Http\Controllers\Control\MessageResourceController;
use Modules\Reviews\Http\Controllers\Control\ReviewResourceController;
use Modules\Reviews\Http\Controllers\Control\TargetTypeController;
use Modules\Reviews\Http\Controllers\DoctorReviewsController;
use Modules\Reviews\Http\Controllers\ReviewsController;
use Modules\Reviews\Http\Requests\ApiListReviewsByDoctorRequest;

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

Route::prefix('v1')->group(function () {
        Route::get('doctor/reviews', function (ApiListReviewsByDoctorRequest $request){
        $apiRequestQueryBuilder = resolve('App\Services\ApiRequestQueryBuilders\ApiListService');
        $doctorReviewController = new  DoctorReviewsController($apiRequestQueryBuilder);
        return $doctorReviewController->getReviews($request);
    });
//    Route::get('doctor/reviews', [DoctorReviewsController::class, 'getReviews']);

});



Route::middleware(['admin'])->prefix('v1/control')->group( function ($router) {

    Route::get('reviews/reviewable-type', [TargetTypeController::class, 'index']);

    Route::apiResource('reviews', ReviewsController::class)->names('reviews');

    Route::apiResources([
        'reviews'=>ReviewResourceController::class
    ]);
    Route::apiResources([
        'reviews.messages'=>MessageResourceController::class
    ]);
});

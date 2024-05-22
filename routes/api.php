<?php

use App\Http\Controllers\Api\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Control\AuthController as AdminAuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::middleware('auth:sanctum')->post('/tokens/create', function (Request $request) {
    $token = $request->user()->createToken($request->token_name);

    return ['token' => $token->plainTextToken];
});
Route::controller(RegisterController::class)->group(function(){
    Route::post('register', 'register');
    Route::post('login', 'login');
});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});




Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::get('fer', function () {
        return 'cool2';
    });
});


Route::prefix('v1')->group(function () {


//    Route::prefix('auth')->group(function ($router) {
//        Route::post('login', [AuthController::class, 'login']);
//        Route::post('login/phone', [AuthController::class, 'loginByPhone']);
//        Route::post('login/phone/send-sms', [AuthController::class, 'sendSms']);
////        Route::post('register', [AuthController::class, 'register']);
//        //'jwt.auth'
//        Route::group(['middleware' => 'jwt.auth'], function () {
//            Route::post('logout', [AuthController::class, 'logout']);
//            Route::post('refresh', [AuthController::class, 'refresh']);
//            Route::post('me', [AuthController::class, 'me']);
//        });
//
//
//        Route::get('/', function () {
//            return 'cool2';
//        });
//
//    });

    Route::prefix('control/auth')->group(function ($router) {
        Route::post('login', [AdminAuthController::class, 'login']);
//        Route::post('register', [AdminAuthController::class, 'register']);
        //'jwt.auth'
        Route::middleware(['auth:sanctum', 'admin'])->group( function () {
            Route::post('logout', [AdminAuthController::class, 'logout']);
            Route::post('refresh', [AdminAuthController::class, 'refresh']);
        });


    });
});

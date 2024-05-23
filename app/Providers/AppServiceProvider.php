<?php

namespace App\Providers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //

        Response::macro('ok', function ($data, $extraData = [], $statusCode = 200){
            $res = ['ok' => true,];
            if (is_array($data) || is_object($data)){
                $res['data'] = $data;
                if(is_array($extraData)){
                    $res += $extraData;
                }
                elseif (is_int($extraData)) {
                    $statusCode = $extraData;
                }
                return response()->json($res, $statusCode );
            }else{
                return response()->json(['ok' => true, 'message' => $data], $statusCode );
            }
        });
        Response::macro('okMessage', function ($message, $statusCode = 200){
            return response()->json(['ok' => true, 'message' => $message], $statusCode );

        });


        Response::macro('apiCollection', function (\Illuminate\Http\Resources\Json\ResourceCollection $itemsCollection, array $data = []){

            return response()->json(['ok' => true, 'items' => $itemsCollection->items(),
                    //'total' => $itemsCollection->count(),
                    'count' => $itemsCollection->total(),
                    'per_page' => $itemsCollection->perPage(),
                    //'current_page' => $itemsCollection->currentPage(),
                    'total_pages' => $itemsCollection->lastPage(),
//                'toSql' => $itemsCollection->toSql(),
                ]+$data);
        });
        Response::macro('apiEntity', function ( $entity ){
            return response()->json(['ok' => true, 'item' => $entity]);
        });


        Response::macro('error', function ( $error, $errorCode = 422 ){
            return response()->json(['ok' => false, 'error' => $error, 'code' => $errorCode], $errorCode);
        });
        Response::macro('errorValidation', function ( ValidationException $exception ){
            return response()->json(['ok' => false, 'error' => $exception->getMessage(), 'errors'  =>$exception->errors()], $exception->status);
        });
    }
}

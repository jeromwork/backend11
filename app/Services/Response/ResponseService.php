<?php


namespace App\Services\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class ResponseService extends Response
{
    /**
     *
     *
     * @see \App\Providers\AppServiceProvider::boot()
     * @param mixed $data
     * @param mixed $extraData
     * @param mixed $statusCode
     * @static
     */
    public static function ok($data, $extraData = [], $statusCode = 200)
    {
        return \Illuminate\Routing\ResponseFactory::ok($data, $extraData, $statusCode);
    }

    public static function okMessageWithData(string $message, $data = [], $statusCode = 200)
    {
        return response()->json(['ok' => true, 'message' => $message, 'data' => $data], $statusCode );
    }

    /**
     *
     *
     * @see \App\Providers\AppServiceProvider::boot()
     * @param mixed $message
     * @param mixed $statusCode
     * @static
     */
    public static function okMessage($message, $statusCode = 200)
    {
        return response()->json(['ok' => true, 'message' => $message], $statusCode );
    }
    /**
     *
     *
     * @see \App\Providers\AppServiceProvider::boot()
     * @static
     */
    public static function apiCollection($modelQuery, array $extraData = [])
    {
        return \Illuminate\Routing\ResponseFactory::apiCollection($modelQuery, $extraData);
    }


    public static function collection(\Illuminate\Http\Resources\Json\ResourceCollection $itemsCollection, array $data = []):JsonResponse
    {
        return response()->json(['ok' => true,
                'items' => $itemsCollection->items(),
                //'total' => $itemsCollection->count(),
                'count' => $itemsCollection->total(),
                'per_page' => $itemsCollection->perPage(),
                //'current_page' => $itemsCollection->currentPage(),
                'total_pages' => $itemsCollection->lastPage(),
//                'toSql' => $itemsCollection->toSql(),
            ]+$data);

    }

//    /**
//     *
//     *
//     * @see \App\Providers\AppServiceProvider::boot()
//     * @param \Illuminate\Database\Eloquent\Builder $modelQuery
//     * @static
//     */
//    public static function apiCollection($modelQuery)
//    {
//        return \Illuminate\Routing\ResponseFactory::apiCollection($modelQuery);
//    }


    /**
     *
     *
     *
     * @see \App\Providers\AppServiceProvider::boot()
     * @param mixed $error
     * @param mixed $errorCode
     * @static
     */
    public static function error($error, $errorCode = 422)
    {
        return \Illuminate\Routing\ResponseFactory::error($error, $errorCode);
    }
    /**
     *
     *
     * @see \App\Providers\AppServiceProvider::boot()
     * @param \Illuminate\Validation\ValidationException $exception
     * @static
     */
    public static function errorValidation($exception)
    {
        return \Illuminate\Routing\ResponseFactory::errorValidation($exception);
    }
}

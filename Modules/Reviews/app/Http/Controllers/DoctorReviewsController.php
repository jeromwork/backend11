<?php

namespace Modules\Reviews\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiAbstractRequest;
use App\Services\ApiRequestQueryBuilders\ApiRequestQueryBuilderAbstractService;
use App\Services\Response\ResponseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Modules\Reviews\Models\Review;
use Modules\Reviews\Transformers\ReviewsByDoctorResource;

class DoctorReviewsController extends \Illuminate\Routing\Controller
{

    private ApiRequestQueryBuilderAbstractService $queryBuilderByRequest;

    public function __construct(ApiRequestQueryBuilderAbstractService $apiHandler)    {
        $this->queryBuilderByRequest = $apiHandler;
    }

    public function getReviews(ApiAbstractRequest $request)
    {
        DB::enableQueryLog();

        //checked doctorId in request class
        $reviews = Review::where('published', 1)
            ->where('reviewable_id',  $request->input('doctorId'))
            ->where('reviewable_type',  'doctor')
            ->with('content')
            ->with('messages')
            ->orderBy('published_at')
            ->orderBy('created_at');

        $reviews = $this->queryBuilderByRequest->build( $reviews, $request );
        return ResponseService::collection( ReviewsByDoctorResource::collection($reviews->paginate()) );
    }

}

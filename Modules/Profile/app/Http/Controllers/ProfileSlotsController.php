<?php

namespace Modules\Profile\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiListRequest;
use App\Services\ApiRequestQueryBuilders\ApiListService;
use App\Services\Response\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Profile\Transformers\ProfileSlotsResource;
use Modules\Slots\Model\Slot;

class ProfileSlotsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private ApiListService $QueryBuilderByRequest;

    public function __construct(
        ApiListService $apiHandler//,
    )    {
        $this->QueryBuilderByRequest = $apiHandler;

    }
    /**
     * Display a listing of the resource.
     * @return []
     */
    public function index(ApiListRequest $request):JsonResponse
    {
        if(!$user = auth()->user()) return ResponseService::error('error auth');//todo #errors
        $slots = Slot::query()->where('user_id', $user->id);
        $slots = $this->QueryBuilderByRequest->build( $slots, $request );
        $slots->with('order');

        return ResponseService::collection( ProfileSlotsResource::collection($slots->paginate()) );
    }

}

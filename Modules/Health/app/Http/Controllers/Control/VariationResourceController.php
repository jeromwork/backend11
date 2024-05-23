<?php

namespace Modules\Health\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiDataTableRequest;
use App\Services\ApiRequestQueryBuilders\ApiDataTableService;
use App\Services\Response\ResponseService;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Health\Models\Variation;
use Modules\Health\Transformers\Control\ServiceResource;

class VariationResourceController extends \Illuminate\Routing\Controller
{


    private ApiDataTableService $QueryBuilderByRequest;


    public function __construct( ApiDataTableService $apiHandler )    {
        $this->QueryBuilderByRequest = $apiHandler;


    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    /**
     * Display a listing of the resource.
     * @param ApiDataTableRequest $request
     * @return array|string
     */
    public function index(ApiDataTableRequest $request)
    {

        $variations = Variation::query();

        //Log::info('ReviewResourceController index!');
        $variations = $this->QueryBuilderByRequest->build( $variations, $request );

        //necessarily models to collection must get with pagination data:  collection($model->paginate())

        return ResponseService::apiCollection( ServiceResource::collection($variations->paginate()) );
    }


    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('health::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('health::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('health::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}


<?php

namespace Modules\Reviews\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiDataTableRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Health\Services\ApiRequestQueryBuilders\ApiBindsService;
use Modules\Reviews\Models\Review;
use Modules\Reviews\Models\ReviewMessage;
use Modules\Reviews\Http\Requests\Control\Messages\StoreRequest;
use Modules\Reviews\Http\Requests\Control\Messages\UpdateRequest;
use Modules\Reviews\Services\Target;

class MessageResourceController extends \Illuminate\Routing\Controller
{

    private ApiBindsService $QueryBuilderByRequest;
    private Target $targetModel;

    public function __construct(ApiBindsService $apiHandler, Target $targetEntity)    {
        $this->QueryBuilderByRequest = $apiHandler;
        $this->targetModel = $targetEntity;
    }

    /**
     * Display a listing of the resource.
     * @param int $review_id
     * @param ApiDataTableRequest $request
     */
    public function index(int $review_id, ApiDataTableRequest $request)
    {
//        return $review_id;
        $messages = ReviewMessage::query();

        $messages = $this->QueryBuilderByRequest->build( $messages, $request );
        $messages->where('review_id', $review_id);

        //necessarily models to collection must get with pagination data:  collection($model->paginate())
        //ReviewResource
        return response()->apiCollection( $messages );
    }

    /**
     * Store a newly created resource in storage.
     * @param int $review_id
     * @param  StoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(int $review_id, StoreRequest $request)
    {
        $requestData = $request->validated();
        $message = new ReviewMessage($requestData + ['review_id' => $review_id]);
        if(!Review::where('id', $review_id)->first()){
            return response()->error('Not found target review', 400);
        }

        $message->save();

        return response()->okMessage('Save new message.', 200);
    }

    /**
     * Show the specified resource.
     * @param  int  $review_id
     * @param  int  $id
     * @return
     */
    public function show(int $review_id, int $id)
    {
        return  response()->ok(ReviewMessage::where('review_id', $review_id)->where('id', $id)->first());
    }

    /**
     * Update the specified resource in storage.
     * @param  int  $review_id
     * @param  UpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(int $review_id, UpdateRequest $request, $id)
    {
        $message = ReviewMessage::where('review_id', $review_id)->where('id', $id)->first();
        $message -> update($request->validated());
        return response()->okMessage('Change data.', 200);
    }

    /**
     * Remove the specified resource from storage.
     * @param  int  $review_id
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $review_id, int $id) {
        $message = ReviewMessage::where('review_id', $review_id)->find($id);
        if($message && $message->delete()){
            return response()->okMessage('Removed message of review');
        }else{
            return  response()->error('not found review message', 404);
        }
    }
}

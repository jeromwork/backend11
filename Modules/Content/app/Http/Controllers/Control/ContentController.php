<?php

namespace Modules\Content\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Services\ApiRequestQueryBuilders\ApiListService;
use App\Services\Response\ResponseService;
use Illuminate\Http\JsonResponse;
use Modules\Content\Models\Content;
use Modules\Content\Http\Requests\ListContentRequest;
use Modules\Content\Http\Requests\SaveContentRequest;
use Modules\Content\Http\Requests\StoreContentRequest;
use Modules\Content\Http\Requests\UpdateRequest;
use Modules\Content\Services\ContentService;
use Modules\Content\Transformers\Control\ContentResource;

class ContentController extends Controller
{


    private ContentService $contentService;
    private ApiListService $queryBuilderByRequest;
//
    public function __construct(    ContentService $contentService,
                                    ApiListService $apiHandler
    )    {

        $this->contentService = $contentService;
        $this->queryBuilderByRequest = $apiHandler;
    }

    /**
     * Display a listing of the resource.
     * @param  $request
     * @return array|string
     */
    public function index( ListContentRequest $request)
    {

        $content = Content::where('contentable_type', $request->targetType)
            ->where('contentable_id', $request->targetId)
            ->where('type', 'original');
        $content = $this->queryBuilderByRequest->build( $content, $request );
        return ResponseService::apiCollection( ContentResource::collection($content->paginate()) );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreContentRequest  $request
     * @return JsonResponse
     */
    public function store(StoreContentRequest $request):JsonResponse   {
        try {
            $contentIds = [];
            if ($request->hasFile('files')) {

                $filesInfo = $this->contentService->saveTempFiles($request);
                $contentIds = array_column($filesInfo, 'id');
            }elseif ($request->get('videoLink')){
                $filesInfo = $this->contentService->saveVideoLink($request);
                $contentIds = array_column($filesInfo, 'id');
            }
            if(!$contentIds){
                return response()->error('Error save upload files');
            }


            $contentCollection = Content::whereIN('id', $contentIds);
            return ResponseService::apiCollection( ContentResource::collection($contentCollection->paginate()) );

        }catch ( \Exception $e){
            return response()->error($e->getMessage());
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, $id)
    {
        $content = Content::where('id', $id)->firstOrFail();
        $this->contentService->store($request->validated(), $content->targetClass, $content->contentable_id);
        return response()->okMessage('Content update', 200);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        $this->contentService->removeContentById( $id);

        return response()->okMessage('Файл удален', 200);

    }

    public function save(SaveContentRequest $request){

        $this->contentService->update( $request->attachContent, $request->targetType, $request->targetId);

        return response()->okMessage('saved', 200);
    }

}

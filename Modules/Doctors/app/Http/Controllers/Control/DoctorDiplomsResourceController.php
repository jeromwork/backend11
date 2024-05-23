<?php

namespace Modules\Doctors\Http\Controllers\Control;

use App\Http\Requests\Control\Diploms\CreateRequest;
use App\Http\Requests\Control\Diploms\UpdateRequest;
use Modules\Doctors\Models\DoctorDiplom;
use App\Services\Response\ResponseService;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Modules\Content\Services\ContentConverters\ImageContentConverter;
use Modules\Content\Services\ContentConverters\VideoContentConverter;
use Modules\Content\Services\ContentService;
use Modules\Doctors\Transformers\Control\DiplomResource;

class DoctorDiplomsResourceController extends \Illuminate\Routing\Controller
{

//    private ApiDataTableService $QueryBuilderByRequest;
    private ContentService $contentService;

    public function __construct(
        //ReviewService $reviewService,
//        ApiDataTableService $apiHandler,//,
        ContentService $contentService
    )    {
//        $this->QueryBuilderByRequest = $apiHandler;
        $this->contentService = $this->addPreviewServiceForContent($contentService);

    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        $diploms = DoctorDiplom::query();
        $diploms->with([ 'contentOriginal' ]);
//        $dbconnect = \DB::connection('MODX')->getPDO();
//        $dbname = DB::connection('MODX')->select('SHOW TABLES FROM east_prod');
//        dd($dbname);
        //necessarily models to collection must get with pagination data:  collection($model->paginate())
        //ReviewResource
//        return response()->apiCollection( $reviews );
        return ResponseService::apiCollection( DiplomResource::collection($diploms->paginate()) );
    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(CreateRequest $request)
    {
        $requestData = $request->validated();
        $diplom = DoctorDiplom::create($requestData);

        if(isset($requestData['contentOriginal']) && $requestData['contentOriginal']) {
            $this->contentService->store( $requestData['contentOriginal'], DoctorDiplom::class, $diplom->id  );
        }
        return response()->ok( (new DiplomResource($diplom))->jsonSerialize(), ['message' => 'Diplom created'], 200 );
    }



    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(UpdateRequest $request, $id)
    {
        $requestData = $request->validated();


        if($diplom = DoctorDiplom::where('id', $id)->first()){
            $diplom -> update($requestData);
            if(isset($requestData['contentOriginal']) && $requestData['contentOriginal']) {
                $this->contentService->store( $requestData['contentOriginal'], DoctorDiplom::class, $id  );
            }
            return response()->ok( new DiplomResource($diplom), ['message' => 'Diplom updated'], 200 );
        }

        ResponseService::error('Do not find doctor');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        if(DoctorDiplom::where('id', $id)->first()){
            DoctorDiplom::destroy($id);
            return ResponseService::okMessage('Removed diplom');
        }else{
            return  ResponseService::error('Failed to remove diplom');
        }
    }

    protected function addPreviewServiceForContent( ContentService $contentService ):ContentService{
        $contentService->addContentConverter( (new ImageContentConverter())
            ->withKey('300x300')
            ->withExtension('webp')
            ->withSize(300, 300)) ;


        $contentService->addContentConverter( (new VideoContentConverter())
            ->withKey('300x300')
            ->withExtension('webm')
            ->withSize(300, 300)) ;

        return $contentService;
    }


}

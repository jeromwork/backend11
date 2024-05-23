<?php

namespace Modules\Health\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Modules\Health\Http\Requests\Control\ApiBindsRequest;
use Modules\Health\Services\ApiRequestQueryBuilders\ApiBindsService;
use Modules\Health\Services\GraphRelations;

class HealthBindsController extends \Illuminate\Routing\Controller
{
    private ApiBindsService $QueryBuilderByRequest;

    public function __construct( ApiBindsService $apiHandler )    {
        $this->QueryBuilderByRequest = $apiHandler;


    }

    /**
     * Display a listing of the resource.
     * @param ApiBindsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ApiBindsRequest $request)
    {

        $requestData = $request->validated();
        $sd = $request->getBaseModel();
        $sd2 = $request->getTargetMethod();
        $sd22 = method_exists($request->getBaseModel(), $request->getTargetMethod());

        if ($request->getBaseModel() && method_exists($request->getBaseModel(), $request->getTargetMethod())) {
//            $doctors = Doctor::whereIn('id', $request->input('baseIds'))->with($request->getTargetMethod())->get();
            $items = $request->getBaseModel()::whereIn('id',[3, 12])
                ->with([$request->getTargetMethod().':id']) // Specify 'id' to select only the ID column for variations
                ->get(['id']); // Select only the ID column for doctors
        }
        return response()->json([ 'data' => $items->toArray(), 'code' => 200, 'ok' => true],200);

        //$this->QueryBuilderByRequest внутри себя выбирает класс который будет обрабатывать реквест, и добавлять условия к запросу
        //у $request же можно спросить какой формат ответа выбрать - какой response

        //get models and relations data
        $modelAlias = new GraphRelations();


        $baseModel = $request->getBaseModel();
        //вообще построитель запроса будет в QueryBuilderByRequest, но и здесь, или в дочерних классах можно добавить запрос
        $queryBinds = $baseModel::query()->whereIn('id', [1, 2, 3, 4]);

        //attempt get special handler
        $responseHandlerClass = $this->getResponseHandlerClass($request);
        if($responseHandlerClass) {
            $response = ( new $responseHandlerClass )->forRequest($request)->withBindsQuery($queryBinds)->answer();
        }else{
            //or default response
            $response = [];
        }

        return response()->json([ 'data' => $response, 'code' => 200, 'ok' => true],200);
    }

    public function binds($baseModel, $secondModel){
        Log::info(print_r($baseModel));
        Log::info(print_r($secondModel));
        return 1;
    }



    protected function getResponseHandlerClass($request):?string {
        $baseClassName = $request->getBaseClassName();
        $targetClassName = $request->getTargetClassName();
        if(!$baseClassName || !$targetClassName) return null;
        $className = [$baseClassName, $targetClassName];
        sort($className);
        $className = 'Modules\Health\Services\ApiResponse\BindsResponses\\'.implode('', $className);
        return (class_exists($className)) ? $className : null;
    }


}

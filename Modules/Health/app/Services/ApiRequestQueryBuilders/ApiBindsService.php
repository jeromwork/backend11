<?php


namespace Modules\Health\Services\ApiRequestQueryBuilders;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use App\Services\ApiRequestQueryBuilders\ApiRequestQueryBuilderAbstractService;

class ApiBindsService extends ApiRequestQueryBuilderAbstractService
{
    public function build( $query, FormRequest $request )  {


        $requestData = $request->validated();

        $baseIds = ( isset($requestData['baseIds']) && $requestData['baseIds'] ) ? $requestData['baseIds'] : [];
        $secondIds = ( isset($requestData['secondIds']) && $requestData['secondIds'] ) ? $requestData['secondIds'] : [];

        if(!$baseIds ||! $secondIds){
            return $query;
        }




//        //пагинация
//        $offset = (isset($requestData['page']) && isset($requestData['per_page'])) ? $requestData['page'] * $requestData['per_page']: 0;
//
//        $limit = (isset($requestData['per_page'])) ? $requestData['per_page']: 10;

//
//        $query->offset($offset)->limit($limit);
//        //sort
//        if(isset($requestData['sort'])){
//            if(is_array($requestData['sort'])){
//                foreach ($requestData['sort'] as $field => $trend){
//                    $query->orderBy($field, ( $trend > 0 ) ? 'asc' : 'desc');
//                }
//            }
//        }
//
//
//        //if(isset($requestData['sort']) && isset($requestData['sortDesc'])){
//
//        //сортировка
//        if(isset($requestData['sortBy']) && isset($requestData['sortDesc'])){
//            foreach ($requestData['sortBy'] as $f => $field){
//                $query->orderBy($field, ($requestData['sortDesc'][$f] === 'false') ? 'asc' : 'desc');
//            }
//        }
//
//
//        $countItemsOnPage = (isset($requestData['per_page'])) ? $requestData['per_page']*1 : 10;
//        $query->getModel()->setPerPage( $countItemsOnPage );
//

        return $query;
    }

}

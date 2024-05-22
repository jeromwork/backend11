<?php


namespace App\Services\ApiRequestQueryBuilders;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class ApiListService extends ApiRequestQueryBuilderAbstractService
{
    use Paginable;


    public function build( $query, FormRequest $request )  {


        $requestData = $request->validated();
        //$query = $model::query();
        //если нужно выдать только заданные сущности
        if(isset($requestData['ids']) && is_array($requestData['ids'])){
            return $query->whereIn('id', $query['ids']);
        }
        //handle pagination from request
        $query = $this->pagination($query, $request);


        //сортировка
        if(isset($requestData['sortBy']) && isset($requestData['sortDesc'])){
            foreach ($requestData['sortBy'] as $f => $field){
                $query->orderBy($field, ($requestData['sortDesc'][$f] === 'false') ? 'asc' : 'desc');
            }
        }





        return $query;
    }

}

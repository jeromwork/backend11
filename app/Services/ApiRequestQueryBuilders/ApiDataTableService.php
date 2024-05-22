<?php


namespace App\Services\ApiRequestQueryBuilders;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class ApiDataTableService extends ApiRequestQueryBuilderAbstractService
{

    use Paginable;
    protected array $searchFieldsGlobal = ['id'];
    public function build( $query, FormRequest $request )  {


        $requestData = $request->validated();
        //$query = $model::query();
        //если нужно выдать только заданные сущности
        $ids = ( isset($requestData['id']) && $requestData['id'] ) ? [ $requestData['id'] ] : [];
        if(!$ids && isset($requestData['ids']) && $requestData['ids']) $ids = $requestData['ids'];
        if($ids){
            return $query->whereIn('id', $ids);
        }

        //handle pagination from request
        $query = $this->pagination($query, $request);

        //sort
        if(isset($requestData['sort'])){
            if(is_array($requestData['sort'])){
                foreach ($requestData['sort'] as $field => $trend){
                    $query->orderBy($field, ( $trend > 0 ) ? 'asc' : 'desc');
                }
            }
        }

        if($request->filters ){
            if(isset($request->filters['global']) && $request->filters['global'] && $request->filters['global']['value'] && $this->searchFieldsGlobal ){
                $value = $request->filters['global']['value'];
                $searchFields = $this->searchFieldsGlobal;
                $query->where(function (Builder $query) use ($value, $searchFields) {
                    foreach ($searchFields as $field){
                        if(is_string($field)){
                            $query->orWhereRaw("LOWER($field) LIKE ?", ['%'.strtolower($value).'%']);
                        }elseif (is_callable($field)) { // Check if $field is callable

                            $query->orWhere(function (Builder $query) use ($field) {
                                $field($query); // Execute the callback to build the dynamic condition
                            });
//                            $query->orWhere($field($query)); // Call the callback with the $query
                        }
                    }

                });
            }
        }

        //if(isset($requestData['sort']) && isset($requestData['sortDesc'])){

        //сортировка
        if(isset($requestData['sortBy']) && isset($requestData['sortDesc'])){
            foreach ($requestData['sortBy'] as $f => $field){
                $query->orderBy($field, ($requestData['sortDesc'][$f] === 'false') ? 'asc' : 'desc');
            }
        }


        $countItemsOnPage = (isset($requestData['per_page'])) ? $requestData['per_page']*1 : 10;
        $query->getModel()->setPerPage( $countItemsOnPage );


        return $query;
    }

    public function withGlobalSearchByFields(array $searchFieldsGlobal):self    {
        $this->searchFieldsGlobal = $searchFieldsGlobal;
        return $this;
    }

}

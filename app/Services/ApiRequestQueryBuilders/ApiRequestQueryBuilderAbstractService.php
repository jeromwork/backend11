<?php


namespace App\Services\ApiRequestQueryBuilders;
use Illuminate\Foundation\Http\FormRequest;
use \Illuminate\Database\Eloquent\Builder;

abstract class ApiRequestQueryBuilderAbstractService
{

    protected int $perPage = 10;
    //todo add type parameter $query
    abstract public function build(  $query, FormRequest $request ) ;

    public function defaultPerPage(int $perPage):self   {
        $this->perPage = $perPage;
        return $this;
    }
}

<?php


namespace Modules\Health\Services\ApiResponse;


use Illuminate\Database\Eloquent\Builder;
use Modules\Health\Http\Requests\ApiBindsRequest;

abstract class ApiBindsResponseAbstract
{
    protected ?ApiBindsRequest $request = null;
    protected ?Builder $query = null;
    protected string $baseClassName = '';
    protected string $targetClassName = '';


    public function forRequest(ApiBindsRequest $request):self {
        $this->request = $request;
        $this->baseClassName = $this->request->getBaseClassName();
        $this->targetClassName = $this->request->getTargetClassName();
        return $this;
    }

    public function withBindsQuery(Builder $query):self {
        $this->query = $query;
        return $this;
    }

    public abstract function answer();


    protected function getResponseClass():?string {
        if( !$this->baseClassName ) return null;
        $className = 'Modules\Health\Transformers\Binds\\'.$this->baseClassName.'Resource';
        return (class_exists($className)) ? $className : null;
    }
}

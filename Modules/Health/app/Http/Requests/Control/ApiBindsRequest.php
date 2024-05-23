<?php

namespace Modules\Health\Http\Requests\Control;

use App\Http\Requests\ApiAbstractRequest;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Health\Services\GraphRelations;


class ApiBindsRequest extends ApiAbstractRequest
{
    protected GraphRelations $graphRelations;
    protected string $baseModel = '';
    protected string $targetModel = '';
    protected string $baseClass = '';
    protected string $targetClass = '';
    protected string $baseClassName = '';
    protected string $targetClassName = '';

    public function __construct(GraphRelations $graphRelations) {
        $this->graphRelations = $graphRelations;
        //parent::__construct();
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return parent::rules() + [
                'baseModel'=>['required'],
                'secondModel'=>['required'],
                'baseIds' => ['nullable'],
                'secondIds' => ['nullable'],
//                'sort.*' =>['nullable', 'string'],
            ];
    }

    public function getBaseModel() {
        if(!$this->baseModel)   $this->baseModel = $this->graphRelations->getModelByAlias($this->input('baseModel'));
        return $this->baseModel;
    }

    public function getTargetModel() {
        if(!$this->targetModel) $this->targetModel = $this->graphRelations->getModelByAlias($this->input('secondModel'));
        return $this->targetModel;
    }

    public function getTargetMethod():string {
        return $this->graphRelations->getRelationsMethod($this->getTargetModel());
    }

    public function getBaseClassName():string {
        if(!$this->baseClassName) $this->baseClassName = $this->graphRelations->getClassNameByModel($this->getBaseModel());
        return $this->baseClassName;
    }

    public function getTargetClassName():string {
        if(!$this->targetClassName) $this->targetClassName = $this->graphRelations->getClassNameByModel($this->getTargetModel());
        return $this->targetClassName;
    }



    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}

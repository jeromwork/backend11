<?php

namespace Modules\Reviews\Http\Requests\Control\Reviews;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Reviews\Services\Target;


class UpdateRequest extends FormRequest
{
    private Target $targetModel;
    public function __construct(Target $targetModel)
    {
        $this->targetModel = $targetModel;
        parent::__construct();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        //Не забываем что этот метод вызывается на get запрос, и все параметры передаются в виде строки
        return [ //пока напрямую задаем, потом можно будет брать из объекта Access
            'author' =>['nullable'],
            'text' =>'nullable',
            //'author_id' => 'nullable',
            'reviewable_type' => ['nullable', Rule::in($this->targetModel->getNameList())],
            'reviewable_id' => 'nullable',
            'rating' => ['nullable', 'numeric'],
            'published' => ['nullable', 'boolean'],
            'published_at' => 'nullable',
            'is_new' => ['nullable', 'boolean'],
            'content' => ['nullable', 'array'],
            'source' => ['nullable']
        ];
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

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'reviewable_id.required' => 'A title is required',
            'rating.numeric' => 'Рейтинг должен быть числом'
        ];
    }
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, response()->json([
            'errors' => $validator->errors(), 'ok' => false
        ], 422));
    }
}

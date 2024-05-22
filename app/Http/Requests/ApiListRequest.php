<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiListRequest extends ApiAbstractRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        //Не забываем что этот метод вызывается на get запрос, и все параметры передаются в виде строки
        return [ //пока напрямую задаем, потом можно будет брать из объекта Access
            'limit' =>'nullable',
            'offset' =>'nullable',
            'page' =>'nullable',
            'all' =>'boolean|nullable',
            'itemsPerPage' =>'nullable',
            'sortBy.*' =>'string|nullable',
            'sortDesc.*' =>'string|nullable',

        ];
    }
//use it, because get parameters as string
    protected function prepareForValidation()
    {
        $this->castQueryParameters([
            'limit' => 'integer',
            'offset' => 'integer',
            'page' => 'integer',
            'all' => 'boolean',
            'itemsPerPage' => 'integer',
        ]);
    }

    protected function castQueryParameters(array $casts)
    {
        foreach ($casts as $parameter => $type) {
            if ($this->has($parameter)) {
                switch ($type) {
                    case 'integer':
                        $this->merge([$parameter => (int)$this->input($parameter)]);
                        break;
                    case 'boolean':
                        $this->merge([$parameter => filter_var($this->input($parameter), FILTER_VALIDATE_BOOLEAN)]);
                        break;
                    // Add more cases for other data types as needed
                }
            }
        }
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

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, response()->json([
            'errors' => $validator->errors(), 'ok' => false
        ], 422));
    }
}

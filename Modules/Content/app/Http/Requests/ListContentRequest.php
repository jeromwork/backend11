<?php

namespace Modules\Content\Http\Requests;

use App\Http\Requests\ApiListRequest;
use Illuminate\Validation\Factory as ValidationFactory;
use Illuminate\Foundation\Http\FormRequest;

class ListContentRequest extends ApiListRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return parent::rules() + [ //пока напрямую задаем, потом можно будет брать из объекта Access
                'targetId' => ['required'],
                'targetType' => ['required', 'string'],
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
            //'files.*.mimes' => 'Неправильный тип изображение. Возможно jpg,jpeg,png',
        ];
    }

//    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
//    {
//        throw new \Illuminate\Validation\ValidationException($validator, response()->json([
//            'errors' => $validator->errors(), 'ok' => false
//        ], 422));
//    }

}
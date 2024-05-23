<?php

namespace Modules\Doctors\Http\Requests\Control\DoctorInfo;

use Illuminate\Foundation\Http\FormRequest;


class UpdateRequest extends FormRequest
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
            'surname' =>['nullable'],
            'name' =>['nullable'],
            'middlename' =>['nullable'],

            'content' => ['nullable', 'array'],
            'diploms' => ['nullable', 'array'],
            'contentOriginal' => ['nullable', 'array'],
            'diplomsOriginal' => ['nullable', 'array']
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
            'reviewable_type.required' => 'Be sure to specify type of review target',
            'reviewable_id.required' => 'Be sure to specify id of review target',
//            'rating.required' => 'Be sure to specify id of review target',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, response()->json([
            'errors' => $validator->errors(), 'ok' => false
        ], 422));
    }
}

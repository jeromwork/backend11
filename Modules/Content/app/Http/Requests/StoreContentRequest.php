<?php

namespace Modules\Content\Http\Requests;

use Illuminate\Validation\Factory as ValidationFactory;
use Illuminate\Foundation\Http\FormRequest;

class StoreContentRequest extends FormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [ //пока напрямую задаем, потом можно будет брать из объекта Access
            'files.*' => 'file|mimes:jpg,jpeg,png,mp4,mov,quicktime,webm,txt|max:409600000',
            'contentable_type' => ['required', 'string'],
            'contentable_id' => ['required', ],
            'original_file_name' => [ 'string'],
            'size' => ['integer'],
            'videoLink' => [ 'string'],
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
            'files.*.mimes' => 'Неправильный тип изображение. Возможно jpg,jpeg,png',
            'contentable_id' => ['required', 'numeric'],
            'contentable_type' => ['required', 'string'],
            'title' => ['nullable', 'string'],
            'is_preview_for' => ['nullable', 'string'],
        ];
    }

//    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
//    {
//        throw new \Illuminate\Validation\ValidationException($validator, response()->json([
//            'errors' => $validator->errors(), 'ok' => false
//        ], 422));
//    }

}

<?php

namespace Modules\Reviews\Http\Requests\Control\Messages;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            //'parent_id' =>['nullable', 'numeric'],
            'message' => 'nullable',
            'author_id' =>['nullable', 'numeric'],
            'author' => 'nullable',
            'published' =>['nullable', Rule::in([0, 1])],
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
}

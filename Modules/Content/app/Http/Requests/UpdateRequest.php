<?php

namespace Modules\Content\Http\Requests;

use App\Services\ApiRequestQueryBuilders\ApiDataTableService;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
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

        return [ //пока напрямую задаем, потом можно будет брать из объекта Access
            'id' => [ 'string'],
            'confirm' => ['nullable', 'boolean'],
            'published'=> ['nullable', 'boolean'],
            'targetClass'=> ['nullable', 'string'],
            'previewOriginal' => ['nullable', 'array'],
            'alt' => ['nullable', 'string'],
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

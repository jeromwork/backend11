<?php

namespace Modules\Orders\Http\Requests\Control;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Orders\Http\Requests\Rules\CheckCartFields;

class StoreOrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        return [
            'fio'=> ['required', 'string'],
            'phone'=> ['required', 'string'],
            'email'=> ['string'],
            'cart'=> ['required', new CheckCartFields],
            'note'=> ['string'],
//            'payment'=> ['required', 'string', Rule::in(['online',  /* 'sberPay', .... Add other payment types here */])],

            //
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

    public function messages():array  {
        return [
            'fio' => 'require',
            'phone' => 'Phone is require',
            //'cart' => 'Cart is required',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'phone' => preg_replace('/[^0-9+]+/', '', $this->input('phone')),
        ]);
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, response()->json([
            'errors' => $validator->errors(), 'ok' => false
        ], 422));
    }
}

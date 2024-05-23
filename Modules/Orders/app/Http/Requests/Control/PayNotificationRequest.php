<?php

namespace Modules\Orders\Http\Requests\Control;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class PayNotificationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        return [
            'id'=> ['required', 'string'],
            'sum'=> ['required', ],
            'clientid'=> ['string'],
            'orderid'=> ['string'],
            'key'=> ['required','string'],
            'ps_id'=> ['required','string'],
            'fop_receipt_key'=> ['string'],

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



    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        Log::channel('payment')->info('Received pay callback: ' . $validator->errors());
        throw new \Illuminate\Validation\ValidationException($validator, response()->json([
            'errors' => $validator->errors(), 'ok' => false
        ], 422));
    }
}

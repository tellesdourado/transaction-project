<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'value' => ['required', ['is_currency']],
            'payee' => 'required|exists:users,id',
            'payer' => 'required|exists:users,id',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }

    public function withValidator($validator)
    {
        $validator->addExtension(
            'is_currency',
            function ($attribute, $value, $parameters, $validator) {
                return preg_match("/^-?[0-9]+(?:\.[0-9]{1,2})?$/", $value);
            }
        );
        $validator->addReplacer(
            'is_currency',
            function ($message, $attribute, $rule, $parameters, $validator) {
                return __("this is not a real value.", compact('attribute'));
            }
        );
    }
}

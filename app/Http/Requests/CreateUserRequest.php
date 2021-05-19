<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cpf'        => 'required|unique:users,cpf|max:11',
            'email'      => 'required|unique:users,email|max:50|email:rfc',
            'password'   => 'required',
            'user_type_id' => 'required|exists:user_types,id',
            'full_name'  => 'required'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}

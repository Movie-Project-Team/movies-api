<?php

namespace App\Http\Requests\Client;

use App\Http\Requests\ApiRequest;
use App\Rules\client\verifyPasswordUser;

class VerifyOTPUserRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'userId' => 'required|exists:users,id',
            'otp' => ['required', 'max:6', new verifyPasswordUser()],
        ];
    }

    public function messages()
    {
        return [
            'userId.required'   => 'The :attribute field is required.',
            'password.required' => 'The :attribute field is required.',
            'password.string'   => 'The :attribute field is must string.',
        ];
    }
}

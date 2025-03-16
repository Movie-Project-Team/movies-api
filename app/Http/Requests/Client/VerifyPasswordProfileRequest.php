<?php

namespace App\Http\Requests\Client;

use App\Http\Requests\ApiRequest;
use App\Rules\client\verifyPasswordProfile;

class VerifyPasswordProfileRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'profileId' => 'required',
            'password' => ['required', 'string', new verifyPasswordProfile()],
        ];
    }

    public function messages()
    {
        return [
            'profileId.required' => 'The :attribute field is required.',
            'password.required' => 'The :attribute field is required.',
            'password.string' => 'The :attribute field is must string.',
        ];
    }
}

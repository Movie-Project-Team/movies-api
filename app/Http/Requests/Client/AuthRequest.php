<?php

namespace App\Http\Requests\Client;

use App\Http\Requests\ApiRequest;

class AuthRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'email' => 'required|email|max:255',
            'password' => 'required',
        ];

        if($this->route()->getName() == 'auth.register') {
            $rules['email'] .= '|unique:users,email';
            $rules['password'] .= '|min:6';
        }

        return $rules;

    }

    public function messages()
    {
        return [
            'email.required'    => 'The :attribute field is required.',
            'email.email'       => 'The :attribute field must be a valid email address.',
            'email.max'         => 'The :attribute field must be less than 255 characters.',
            'email.unique'      => 'The :attribute field has already been taken.',
            'password.required' => 'The :attribute field is required.',
            'password.min'      => 'The :attribute field must be at least 6 characters.',
        ];
    }
}

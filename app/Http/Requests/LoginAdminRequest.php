<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginAdminRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required','email'],
            'password' => ['required','min:4'],
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'The :attribute field is required.',
            'password.required' => 'The :attribute field is required.',
            'email.email' => 'The :attribute field must be a valid email address.',
            'password.min' => 'The :attribute field must be at least 4 characters.',
        ];
    }
}

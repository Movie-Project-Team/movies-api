<?php

namespace App\Http\Requests\Client;

use App\Http\Requests\ApiRequest;

class GetProfileRequest extends ApiRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'profileId' => $this->route('id')
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'profileId' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'profileId.required' => 'The :attribute field is required.',
        ];
    }
}

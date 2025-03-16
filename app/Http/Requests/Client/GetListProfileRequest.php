<?php

namespace App\Http\Requests\Client;

use App\Http\Requests\ApiRequest;

class GetListProfileRequest extends ApiRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'userId' => $this->route('id')
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
            'userId' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'userId.required' => 'The :attribute field is required.',
        ];
    }


}

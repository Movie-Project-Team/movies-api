<?php

namespace App\Rules\client;

use App\Services\CommonService;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class verifyPasswordRoom implements ValidationRule,DataAwareRule
{
    protected $data = [];
 
    // ...
 
    /**
     * Set the data under validation.
     *
     * @param  array<string, mixed>  $data
     */
    public function setData(array $data): static
    {
        $this->data = $data;
 
        return $this;
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $profile = CommonService::getModel('Room')->getDetail($this->data['roomId']);
        if($profile->password !== $value){
            $fail("The {$attribute} is incorrect.");
        }
    }
}

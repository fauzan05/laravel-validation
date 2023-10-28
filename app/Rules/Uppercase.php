<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Uppercase implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // if($value !== strtoupper($value)){
        //     $fail("The attribute $attribute must be Uppercase");
        // }
        if($value !== strtoupper($value)){
            $fail("validation.custom.uppercase")->translate([
                "attribute" => $attribute,
                "value" => $value
            ]);
        }
    }
}

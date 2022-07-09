<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\InvokableRule;

class HexColor implements InvokableRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail)
    {
        if(!preg_match('/^#([\da-fA-F]{6}|[\da-fA-F]{3})$/', $value)) {
            $fail('validation.color')->translate();
        }
    }
}

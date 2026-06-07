<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NoHtml implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            return;
        }

        // Check for HTML tag characters (< or >) or strip_tags differences
        if (str_contains($value, '<') || str_contains($value, '>') || strip_tags($value) !== $value) {
            $fail('The :attribute field cannot contain HTML tags or special formatting characters like < or >.');
        }
    }
}

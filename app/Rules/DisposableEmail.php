<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class DisposableEmail implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) {
            $fail('The :attribute is invalid.');
            return;
        }

        $email = strtolower(trim($value));
        $parts = explode('@', $email);
        if (count($parts) < 2) {
            $fail('The :attribute is invalid.');
            return;
        }

        $domain = $parts[1];

        // Ensure domain has a dot and a valid TLD of at least 2 characters
        if (!str_contains($domain, '.') || strlen(substr(strrchr($domain, '.'), 1)) < 2) {
            $fail('The email address domain is invalid.');
            return;
        }

        // 1. Block known disposable, temporary and Tor domains
        $blockedDomains = [
            'mailinator.com',
            'trashmail.com',
            'yopmail.com',
            'guerrillamail.com',
            'guerrillamail.net',
            'guerrillamail.org',
            'guerrillamail.biz',
            'guerrillamailblock.com',
            'dispostable.com',
            'tempmail.com',
            'temp-mail.org',
            'temp-mail.ru',
            'generator.email',
            'getairmail.com',
            '10minutemail.com',
            'sharklasers.com',
            'grr.la',
            'duck.com',
            'onion',
            'onion.ly',
            'onion.sh',
            'tor.onion',
        ];

        // Exact match or sub-domain check
        foreach ($blockedDomains as $blocked) {
            if ($domain === $blocked || str_ends_with($domain, '.' . $blocked)) {
                $fail('Registration from temporary, disposable, or Tor-related email addresses is not permitted.');
                return;
            }
        }

        // 2. Perform MX Record verification to ensure the domain actually exists and can receive email.
        // This is a powerful mechanism to block auto-generated/fake domain spam bots.
        if (function_exists('checkdnsrr')) {
            if (!checkdnsrr($domain, 'MX')) {
                $fail('The email address domain does not appear to have valid mail servers.');
                return;
            }
        }
    }
}

<?php

namespace App\Rules;

use Closure;
use Illuminate\Support\Arr;
use App\Enums\NotificationResource;
use App\Enums\NotificationAddressType;
use App\Enums\NotificationContentType;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidNotificationConfig implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $configs, Closure $fail): void
    {
        if (!is_array($configs) || !$this->isIndexedArray($configs)) {
            $fail("The resource in :attribute must be an array of arrays.");
            return;
        }

        foreach ($configs as $config) {
            foreach($config as $index => $value) {
                // Validate 'resource'
                if (!isset($config['resource']) || !in_array($config['resource'], NotificationResource::values(), true)) {
                    $fail("The resource in :attribute at index {$index} is invalid.");
                    return;
                }

                // Validate 'content_type'
                if (!isset($config['content_type']) || !in_array($config['content_type'], NotificationContentType::values(), true)) {
                    $fail("The content_type in :attribute at index {$index} is invalid.");
                    return;
                }

                // Validate 'addresses'
                if (!isset($config['addresses']) || !is_array($config['addresses'])) {
                    $fail("The addresses in :attribute at index {$index} must be an array.");
                    return;
                }

                if (!isset($config['addresses']['type']) || !in_array($config['addresses']['type'], NotificationAddressType::values(), true)) {
                    $fail("The address type in :attribute at index {$index} is invalid.");
                    return;
                }

                if (isset($config['addresses']['values']) && !is_array($config['addresses']['values'])) {
                    $fail("The values in addresses in :attribute at index {$index} must be an array.");
                    return;
                }

                if (isset($config['addresses']['values'])) {
                    foreach ($config['addresses']['values'] as $value) {
                        if(!NotificationAddressType::isValidValue($config['addresses']['type'], $value)) {
                            $fail("The values in addresses in :attribute at index {$index} contain an invalid input.");
                            return;
                        }

                    }
                }

                if (!isset($config['enable']) || !is_bool($config['enable'])) {
                    $fail("The enable in :attribute at index {$index} must be true or false.");
                    return;
                }

            }
        }
    }

    function isIndexedArray(array $array): bool {
        return array_keys($array) === range(0, count($array) - 1);
    }
}

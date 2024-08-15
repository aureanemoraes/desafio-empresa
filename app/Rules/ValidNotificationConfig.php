<?php

namespace App\Rules;

use Closure;
use App\Enums\NotificationResourceType;
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
            if (!isset($config['content_type'])) {
                $fail("The resource in :attribute at index content_type is required.");
                return;
            }

            if (!isset($config['resources'])) {
                $fail("The resource in :attribute at index resources is required.");
                return;
            }

            if (!is_array($config['resources'])) {
                $fail("The resource in :attribute at index resources must be an array.");
                return;
            }

            foreach ($config['resources'] as $resource) {
                if (!in_array($resource['type'], NotificationResourceType::values())) {
                    $fail("The :attribute at index resources.*.type contain an invalid input.");
                    return;
                }

                if (isset($resource['values']) && !is_array($resource['values'])) {
                    $fail("The enable in :attribute at index resources.*.values must be an array.");
                    return;
                }


                if (isset($resource['enable']) && !is_bool($resource['enable'])) {
                    $fail("The enable in :attribute at index resources.*.enable must be true or false.");
                    return;
                }

                if (isset($resource['aditional_config']) && !is_array($resource['aditional_config'])) {
                    $fail("The enable in :attribute at index resources.*.aditional_config must be an array.");
                    return;
                }
            }
        }
    }

    function isIndexedArray(array $array): bool {
        return array_keys($array) === range(0, count($array) - 1);
    }
}

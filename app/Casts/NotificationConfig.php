<?php

namespace App\Casts;

use InvalidArgumentException;
use App\Enums\NotificationContentType;
use App\Enums\NotificationResourceType;
use Illuminate\Database\Eloquent\Model;
use App\ValueObjects\NotificationResource;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use App\ValueObjects\NotificationConfig as NotificationConfigValueObject;

class NotificationConfig implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): array
    {
        $value = json_decode($value, true); // o valor vem em stringfy do db

        if (empty($value)) {
            return [];
        }

        // instanciando utilizando as classes que garantem a estruta do notifications_config
        return array_map(function ($item) {
            return new NotificationConfigValueObject(
                array_map(fn ($resource) => new NotificationResource(
                    NotificationResourceType::from($resource['type']),
                    $resource['enable'],
                    $resource['values'],
                    $resource['aditionalInfo']
                ), $item['resources']),
                NotificationContentType::from($item['content_type']),
            );
        }, $value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): array
    {
        if (is_null($value)) {
            return [];
        }

        if (!is_array($value)) {
            throw new InvalidArgumentException('The given value is not an array.', 422);
        }


        $encodedValue = json_encode(array_map(function ($item) {
            if (!($item instanceof NotificationConfigValueObject)) {
                $item = new NotificationConfigValueObject(
                    array_map(fn ($resource) => new NotificationResource(
                        NotificationResourceType::from($resource['type']),
                        $resource['enable'] ?? false,
                        $resource['values'] ?? [],
                        $resource['aditional_info'] ?? []
                    ), $item['resources']),
                    NotificationContentType::from($item['content_type']),
                );
            }

            return [
                'resources' => $item->resources,
                'content_type' => $item->contentType,
            ];
        }, $value));


        return [$key => $encodedValue];
    }
}

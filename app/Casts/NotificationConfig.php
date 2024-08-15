<?php

namespace App\Casts;

use InvalidArgumentException;
use App\Enums\NotificationResource;
use App\Enums\NotificationAddressType;
use App\Enums\NotificationContentType;
use Illuminate\Database\Eloquent\Model;
use App\ValueObjects\NotificationAddress;
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
                NotificationResource::from($item['resource']),
                NotificationContentType::from($item['content_type']),
                new NotificationAddress(
                    NotificationAddressType::from($item['addresses']['type']),
                    $item['addresses']['values'] ?? []
                ),
                $item['enable'],
                $item['aditional_info']
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
                    NotificationResource::from($item['resource']),
                    NotificationContentType::from($item['content_type']),
                    new NotificationAddress(
                        NotificationAddressType::from($item['addresses']['type']),
                        $item['addresses']['values'] ?? []
                    ),
                    $item['enable'] ?? false,
                    $item['aditional_info'] ?? []
                );
            }

            return [
                'resource' => $item->resource,
                'content_type' => $item->contentType,
                'addresses' => [
                    'type' => $item->addresses->type,
                    'values' => $item->addresses->values
                ],
                'enable' => $item->enable,
                'aditional_info' => $item->aditionalInfo,
            ];
        }, $value));

        return [$key => $encodedValue];
    }
}

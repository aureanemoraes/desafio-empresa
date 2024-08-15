<?php

namespace App\ValueObjects;

use App\Enums\NotificationResource;
use App\Enums\NotificationAddressType;
use App\Enums\NotificationContentType;
use Illuminate\Contracts\Database\Eloquent\Castable;
use App\Casts\NotificationConfig as NotificationConfigCast;

class NotificationConfig implements Castable
{
    function __construct(
        public array|null $resources = [],
        public NotificationContentType $contentType,
    )
    {
    }

    /**
     * Get the name of the caster class to use when casting from / to this cast target.
     *
     * @param  array<string, mixed>  $arguments
     */
    public static function castUsing(array $arguments): string
    {
        return NotificationConfigCast::class;
    }
}


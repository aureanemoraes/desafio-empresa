<?php

namespace App\ValueObjects;

use App\Enums\NotificationContentType;
use Illuminate\Contracts\Database\Eloquent\Castable;
use App\Casts\NotificationConfig as NotificationConfigCast;

class NotificationConfig implements Castable
{
    public function __construct(
        /**
         * @var NotificationResource[]
         */
        public array|null $resources = [],

        public NotificationContentType $contentType,
    ) {
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

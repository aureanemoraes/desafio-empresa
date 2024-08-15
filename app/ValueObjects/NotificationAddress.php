<?php

namespace App\ValueObjects;


use App\Enums\NotificationAddressType;

class NotificationAddress {
    function __construct(
        public NotificationAddressType $type,

         /**
         * @var string[] $values
         */
        public array|null $values = [],
    )
    {
    }
}

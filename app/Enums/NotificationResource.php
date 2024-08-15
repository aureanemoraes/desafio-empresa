<?php

namespace App\Enums;

enum NotificationResource: string
{
    case ZAP = 'zap';
    case EMAIL = 'email';
    CASE WEBHOOK = 'webhook';

    public static function values(): array
    {
        return array_map(fn($enum) => $enum->value, self::cases());
    }
}

<?php

namespace App\Enums;

/**
 * via por onde será enviada a notificação
 */
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

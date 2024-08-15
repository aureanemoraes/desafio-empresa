<?php

namespace App\Enums;

use App\Models\User;
use App\Models\Respondent;

// Tipo de destinatário
enum NotificationAddressType: string
{
    case EMAIL = 'email';
    case PHONE_NUMBER = 'phone_number';
    case URL = 'url';

    public static function values(): array
    {
        return array_map(fn($enum) => $enum->value, self::cases());
    }

    // Validar diferentes tipos de valores | utilizada no Rule de validação do campo notifications_config
    public static function isValidValue($validationType, $value): bool
    {
        switch (strtolower($validationType)) {
            case 'email':
                return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;

            case 'phone_number':
                // validação no formato de número do whatsapp para números do Brasil
                $phonePattern = '/^\+55\s\d{2}\s9\d{4}-\d{4}$/';

                return preg_match($phonePattern, $value) === 1;
            case 'url':
                $validUrlPattern = '/^(https?:\/\/)?([a-zA-Z0-9.-]+)(:[0-9]{2,5})?(\/[^\s]*)?$/';
                $maliciousScriptPattern = '/<(?:script|iframe|object|embed|applet|link|style|meta|form|input|textarea|button|select|option|datalist|keygen|output|base|canvas|svg|math|audio|video|source|track|frame|frameset|noframes|noscript|style|base|head|body|html)[^>]*>/i';
                $sqlInjectionPattern = '/(union\s+.*\s+select\s+\()|(select\s+.*\s+from\s+information_schema\.tables)|(select\s+.*\s+from\s+mysql\.user)|(select\s+.*\s+from\s+.*\s+where\s+.*\s+group\s+by\s+.*)|(delete\s+from\s+\w+)|(update\s+\w+\s+set\s+.*)|(insert\s+into\s+\w+\s+\(.*\)\s+values\s+\(.*\))|(drop\s+(table|column)\s+\w+)/i';

                return preg_match($validUrlPattern, $value)
                    && !preg_match($maliciousScriptPattern, $value)
                    && !preg_match($sqlInjectionPattern, $value);

            default:
                return false;
        }
    }

}

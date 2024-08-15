<?php

namespace App\Enums;

/**
 * Tipo de conteúdo que a notificação deverá enviar
 */
enum NotificationContentType: string
{
    case FORMULARIO_FINALIZADO = 'formulario_finalizado';
    case COPIA_RESPOSTAS_FORMULARIO = 'copia_respostas_formulário';

    public static function values(): array
    {
        return array_map(fn($enum) => $enum->value, self::cases());
    }
}

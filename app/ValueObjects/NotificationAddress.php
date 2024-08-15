<?php

namespace App\ValueObjects;


use App\Enums\NotificationAddressType;

// Informações dos destinatários da notificação
class NotificationAddress {
    function __construct(
        public NotificationAddressType $type,

         /**
         * @var string[] $values
         *
         * na configuração do formulário poderão ser definidas as informações do destinatário, como por exemplo, no caso do webhook que será configurado juntamente o formulário.
         * o campo não será utilizado para os casos de email e numero de telefone, pois virão de entidades dentro do sistema
         */
        public array|null $values = [],

    )
    {
    }
}

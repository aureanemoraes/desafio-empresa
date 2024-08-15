<?php

namespace App\ValueObjects;

use App\Enums\NotificationResourceType;

class NotificationResource {
    function __construct(
        public NotificationResourceType $type,
        public bool $enable = false,

         /**
         * @var string[] $values
         *
         * na configuração do formulário poderão ser definidas as informações do destinatário, como por exemplo, no caso do webhook que será configurado juntamente o formulário.
         * o campo não será utilizado para os casos de email e numero de telefone, pois virão de entidades dentro do sistema
         */
        public array|null $values = [],
        public array|null $aditionalInfo = [],
    )
    {

    }
}

<?php

namespace App\Services;

use App\Models\Answer;
use App\Enums\NotificationContentType;
use App\Services\Notifications\SendFormAnswers;
use App\Services\Notifications\SendNewFormFormFilling;

class NotificationService
{
    /**
     * receberá a answer de parametro, pois a última resposta é a definição do momento em que deverá ser enviado a notificação e através dela é possivel resgatar todos os dados necessários para o envio das notificações
     */
    public static function notify(Answer $answer)
    {
        $answer->load(['form', 'respondent']);
        $respondent = $answer?->respondent;
        $form = $answer?->form;

        if (!empty($form?->notifications_config) && isset($respondent) && isset($form)) {
            foreach($form?->notifications_config as $notificationConfig) {
                switch($notificationConfig?->contentType) {
                    case NotificationContentType::COPIA_RESPOSTAS_FORMULARIO:
                        $sendFormAnswers = new SendFormAnswers();
                        $sendFormAnswers->send($notificationConfig, $respondent);

                        break;
                    case NotificationContentType::FORMULARIO_FINALIZADO:
                        $sendNewFormFormFilling = new SendNewFormFormFilling();
                        $sendNewFormFormFilling->send($notificationConfig, $respondent);
                        break;
                }
            }
        }
    }
}


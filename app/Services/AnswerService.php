<?php

namespace App\Services;

use App\Models\Answer;
use App\Mail\SimpleMailNotification;
use Illuminate\Support\Facades\Mail;
use App\Enums\NotificationContentType;
use App\Services\DataTreater\EmailData;
use App\Services\Notifications\SendFormAnswers;
use App\Services\Notifications\SendNewFormFormFilling;

class AnswerService
{
    public static function notify(Answer $answer)
    {
        if (!empty($answer->form?->notifications_config)) {
            foreach($answer->form?->notifications_config as $notificationConfig) {
                if ($notificationConfig->enable) {
                    switch($notificationConfig->contentType) {
                        case NotificationContentType::COPIA_RESPOSTAS_FORMULARIO:
                            $sendFormAnswers = new SendFormAnswers();
                            $sendFormAnswers->send($notificationConfig, $answer?->respondent);


                        case NotificationContentType::FORMULARIO_FINALIZADO:
                            $sendNewFormFormFilling = new SendNewFormFormFilling();
                            $sendNewFormFormFilling->send($notificationConfig, $answer?->respondent);
                    }
                }
            }
        }
    }
}


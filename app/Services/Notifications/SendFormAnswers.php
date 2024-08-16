<?php

namespace App\Services\Notifications;

use App\Models\Respondent;
use App\Jobs\SendDataViaWebhook;
use App\Enums\NotificationResource;
use App\Mail\SimpleMailNotification;
use Illuminate\Support\Facades\Mail;
use App\Enums\NotificationResourceType;
use App\Services\DataTreater\EmailData;
use App\ValueObjects\NotificationConfig;
use App\Services\DataTreater\AnswersWebhookData;

class SendFormAnswers
{
    public function send(
        NotificationConfig $notificationConfig,
        Respondent $respondent
    ) {
        $respondent->load(['form.user']);

        foreach($notificationConfig->resources as $resource) {
            if ($resource->enable) {
                switch($resource->type) {
                    case NotificationResourceType::WEBHOOK:
                        $this->viaWebhook($respondent, $resource->values);
                        break;
                    case NotificationResourceType::EMAIL:
                        $this->viaEmail($respondent);
                        break;
                }
            }
        }
    }

    private function viaWebhook(Respondent $respondent, array $urls)
    {
        $data = new AnswersWebhookData($respondent);

        foreach ($urls as $url) {
            SendDataViaWebhook::dispatch($url, $data->treat());
        }
    }

    private function viaEmail(Respondent $respondent)
    {
        $data = new EmailData($respondent);

        Mail::to($respondent->email)
        ->queue(
            new SimpleMailNotification(
                "Cópia Respostas Formulário - Respondent",
                "mails.generic-mail-content",
                $data->treat()
            )
        );
    }
}

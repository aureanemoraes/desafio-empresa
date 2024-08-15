<?php

namespace App\Services\Notifications;


use App\Models\Respondent;
use App\Jobs\SendZapMessage;
use Illuminate\Support\Facades\DB;
use App\Enums\NotificationResource;
use Illuminate\Support\Facades\Log;
use App\Mail\SimpleMailNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use App\Services\DataTreater\EmailData;
use App\ValueObjects\NotificationConfig;

class SendNewFormFormFilling
{
    public function send(
        NotificationConfig $notificationConfig,
        Respondent $respondent
    )
    {
        $respondent->load(['form.user']);

        switch($notificationConfig->resource) {
            case NotificationResource::ZAP:
                $this->viaZap($respondent);
                break;
            case NotificationResource::EMAIL:
                $this->viaEmail($respondent);
                break;
        }

    }

    private function viaZap(Respondent $respondent)
    {
        $form = $respondent?->form;
        $accountUser = $respondent?->form?->user;

         /**
         * Optei por não retornar nenhum erro, apenas registrar via log pois acredito a falha nas notificaçõesnão deva influenciar em nenhum fluxo, deve haver uma tratativa mais secundária somente com o objetivo de informar que não foi realizado o envio e o motivo
         */
        if (isset($accountUser)) {
            if ($accountUser->canSendZapMessage()) {
                if (Schema::hasColumn('users', 'phone')) {
                    if (isset($accountUser->phone)) {
                        SendZapMessage::dispatch(
                            $accountUser?->phone,
                            "Novo preenchimento no {$form?->title} recebido.\nConfira no link: https://teste.com/{$respondent?->public_id}",
                        );

                        $accountUser->incrementAmountZapMessagesSentPerMonth();
                    }
                } else {
                    Log::error("The attribute: phone does not exists in users table.");
                }
            } else {
                Log::error("Current user has exceeded the limit of monthly zap messages.");
            }
        } else {
            Log::error("accountUser variable is null.");
        }
    }

    private function viaEmail(Respondent $respondent)
    {
        $data = new EmailData($respondent);

        Mail::to($respondent->form?->user?->email)
        ->queue(
            new SimpleMailNotification(
                "Formulário preenchido - Owner",
                "mails.generic-mail-content",
               $data->treat()
            )
        );


    }
}


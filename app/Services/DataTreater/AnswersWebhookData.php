<?php

namespace App\Services\DataTreater;


use App\Models\Respondent;


class AnswersWebhookData
{
    function __construct(
        public Respondent $respondent
    )
    {

    }
    public function treat()
    {
        $this->respondent->load(['answers']);

        $data = [];

        foreach($this->respondent?->answers as $answer) {
            /**
             * tratando os dados para não enviar o user_id
             */
            $data[] = [
                'respondent_id' => $answer->respondent_id ?? '',
                'form_id' => $answer->form_id ?? '',
                'question' => $answer->question ?? '',
                'value' => $answer->value ?? '',
                'type' => $answer->type ?? '',
                'field_id' => $answer->field_id ?? '',
            ];
        }

        return $data;
    }
}


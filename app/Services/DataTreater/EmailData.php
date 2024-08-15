<?php

namespace App\Services\DataTreater;


use App\Models\Answer;
use App\Models\Respondent;
use App\Mail\SimpleMailNotification;
use Illuminate\Support\Facades\Mail;


class EmailData
{
    function __construct(
        public Respondent $respondent
    )
    {

    }
    public function treat()
    {
        return [
            'form' => [
                'title' => $this->respondent->form?->title,
            ],
            'respondent' => $this->respondent?->public_id
        ];
    }
}


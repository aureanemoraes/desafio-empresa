<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Form;
use App\Enums\NotificationResource;
use App\Mail\SimpleMailNotification;
use Illuminate\Support\Facades\Mail;
use App\Enums\NotificationAddressType;
use App\Enums\NotificationContentType;
use App\ValueObjects\NotificationConfig;
use App\ValueObjects\NotificationAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SimpleMailNotificationUnitTest extends TestCase
{

	use RefreshDatabase;

	/** @test */
    public function test_email_is_sent_via_send()
    {
        // Fake the mailer
        Mail::fake();

        // Dados de exemplo
        $subject = 'Test Subject';
        $view = 'mails.generic-mail-content';
        $data = ['key' => 'value'];

        // Envia o e-mail
        Mail::to('recipient@example.com')->send(
            new SimpleMailNotification($subject, $view, $data)
        );

        // Verifica se o e-mail foi enviado
        Mail::assertSent(SimpleMailNotification::class, function ($mail) use ($subject, $view, $data) {
            return $mail->subjectData === $subject &&
                   $mail->viewRelativePath === $view &&
                   $mail->contentData === $data;
        });
    }

    public function test_email_is_sent_via_queue()
    {
        // Fake the mailer
        Mail::fake();

        // Dados de exemplo
        $subject = 'Test Subject';
        $view = 'mails.generic-mail-content';
        $data = ['key' => 'value'];

        // Envia o e-mail via queue
        Mail::to('recipient@example.com')->queue(
            new SimpleMailNotification($subject, $view, $data)
        );

        // Verifica se o e-mail foi enfileirado
        Mail::assertQueued(SimpleMailNotification::class, function ($mail) use ($subject, $view, $data) {
            return $mail->subjectData === $subject &&
                   $mail->viewRelativePath === $view &&
                   $mail->contentData === $data;
        });
    }
}

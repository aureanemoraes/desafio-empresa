<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Form;
use App\Models\User;
use App\Models\Answer;
use App\Models\Respondent;
use App\Jobs\SendZapMessage;
use Illuminate\Support\Carbon;
use App\Jobs\SendDataViaWebhook;
use App\Enums\NotificationResource;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use App\Mail\SimpleMailNotification;
use Illuminate\Support\Facades\Mail;
use App\Enums\NotificationAddressType;
use App\Enums\NotificationContentType;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use App\ValueObjects\NotificationConfig;
use App\ValueObjects\NotificationAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AnswerTest extends TestCase
{

	use RefreshDatabase;

	/** @test */
	public function test_respondent_can_store_answers()
	{
		$form = Form::factory()->create();
		$respondent = Respondent::factory()->for($form)->create();
		$answer = Answer::factory()->for($form)->for($respondent)->make();
		$post = $this->post('/api/answers', $answer->toArray());

		$post->assertStatus(201);
		$post->assertJsonPath('data.question', $answer->question);
	}

	/** @test */
	public function test_respondent_can_complete_a_session()
	{
        $user = User::factory()->create();
		$form = Form::factory()->for($user)->create();
        unset($form['notifications_config']);
		$answer = Answer::factory()->for($form)->make(["respondent_id" => null]);
		$answer->is_last = true;

		$post = $this->post('/api/answers', $answer->toArray());

		$this->assertDatabaseHas("respondents", [
			"public_id" => $post['data']['respondent'],
		]);

		$this->assertNotNull($post['data']['respondent']);
	}

	/** @test */
    public function test_respondent_cannot_complete_a_session_without_respondent_email()
	{
		$form = Form::factory()->state([
            'notifications_config' => [
                new NotificationConfig(
                    NotificationResource::EMAIL,
                    NotificationContentType::COPIA_RESPOSTAS_FORMULARIO,
                    new NotificationAddress(
                        NotificationAddressType::EMAIL,

                    ),
                    true
                )
            ]
        ])->create();

		$answer = Answer::factory()->for($form)->make(["respondent_id" => null]);

        unset($answer['respondent_email']);

		$answer->is_last = true;

		$response = $this->post('/api/answers', $answer->toArray());

		$response->assertStatus(422);
	}

    public function test_respondent_can_complete_a_session_with_respondent_email()
	{
        Mail::fake();

        $user = User::factory()->create();
		$form = Form::factory()->for($user)->state([
            'notifications_config' => [
                new NotificationConfig(
                    NotificationResource::EMAIL,
                    NotificationContentType::COPIA_RESPOSTAS_FORMULARIO,
                    new NotificationAddress(
                        NotificationAddressType::EMAIL,

                    ),
                    true
                )
            ]
        ])->create();
		$answer = Answer::factory()->for($form)->make(["respondent_id" => null]);
		$answer->is_last = true;

		$post = $this->post('/api/answers', $answer->toArray());

		$this->assertDatabaseHas("respondents", [
			"public_id" => $post['data']['respondent'],
		]);

		$this->assertNotNull($post['data']['respondent']);

        Mail::assertQueued(SimpleMailNotification::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email) &&
                   $mail->subjectData === "Formulário preenchido - Owner" &&
                   $mail->viewRelativePath === "mails.generic-mail-content";
        });
	}

    public function test_respondent_can_complete_a_session_and_send_finished_form_email_to_owner()
	{
        $user = User::factory()->create();
		$form = Form::factory()->for($user)->state([
            'notifications_config' => [
                new NotificationConfig(
                    NotificationResource::EMAIL,
                    NotificationContentType::FORMULARIO_FINALIZADO,
                    new NotificationAddress(
                        NotificationAddressType::EMAIL,

                    ),
                    true
                )
            ]
        ])->create();
		$answer = Answer::factory()->for($form)->make(["respondent_id" => null]);
		$answer->is_last = true;

		$post = $this->post('/api/answers', $answer->toArray());

		$this->assertDatabaseHas("respondents", [
			"public_id" => $post['data']['respondent'],
		]);

		$this->assertNotNull($post['data']['respondent']);
	}

    public function test_respondent_can_complete_a_session_with_web_hook_queued_job()
    {
        Bus::fake();

        $user = User::factory()->create();
        $form = Form::factory()->for($user)->state([
            'notifications_config' => [
                new NotificationConfig(
                    NotificationResource::WEBHOOK,
                    NotificationContentType::COPIA_RESPOSTAS_FORMULARIO,
                    new NotificationAddress(
                        NotificationAddressType::URL,
                        [
                            'http://webhookaleatorio1.com.br',
                            'http://webhookaleatorio2.com.br',
                        ]
                    ),
                    true
                )
            ]
        ])->create();
        $answer = Answer::factory()->for($form)->make(["respondent_id" => null]);
        $answer->is_last = true;

        $post = $this->post('/api/answers', $answer->toArray());

        Bus::assertDispatched(SendDataViaWebhook::class, function ($job) {
            return $job->url === 'http://webhookaleatorio1.com.br';
        });

        $this->assertDatabaseHas("respondents", [
            "public_id" => $post['data']['respondent'],
        ]);

        $this->assertNotNull($post['data']['respondent']);
    }

    public function test_respondent_can_complete_a_session_with_zap_message_queued_job()
    {
        Bus::fake();

        Schema::table('users', function ($table) {
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable();
            }
        });

        $user = User::factory()->create(['phone' => '11987654321']);

        $form = Form::factory()->for($user)->state([
            'notifications_config' => [
                new NotificationConfig(
                    NotificationResource::ZAP,
                    NotificationContentType::COPIA_RESPOSTAS_FORMULARIO,
                    new NotificationAddress(
                        NotificationAddressType::PHONE_NUMBER
                    ),
                    true
                )
            ]
        ])->create();
        $answer = Answer::factory()->for($form)->make(["respondent_id" => null]);
        $answer->is_last = true;

        $post = $this->post('/api/answers', $answer->toArray());

        Bus::assertDispatched(SendZapMessage::class, function ($job) use ($user) {
            return $job->number === $user->phone;
        });

        $this->assertDatabaseHas("respondents", [
            "public_id" => $post['data']['respondent'],
        ]);

        $this->assertNotNull($post['data']['respondent']);
    }

    public function test_respondent_can_complete_a_session_with_zap_message_logged_error()
    {
        /**
         * teste utilizando a fila real da aplicação
         */

        config()->set('queue.default', 'sync');

        /**
         * limpando log
        */

        $logFilePath = storage_path('logs/laravel.log');

        file_put_contents($logFilePath, '');

        Schema::table('users', function ($table) {
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable();
            }
        });

        $user = User::factory()->create(['phone' => '11987654321']);

        $form = Form::factory()->for($user)->state([
            'notifications_config' => [
                new NotificationConfig(
                    NotificationResource::ZAP,
                    NotificationContentType::COPIA_RESPOSTAS_FORMULARIO,
                    new NotificationAddress(
                        NotificationAddressType::PHONE_NUMBER
                    ),
                    true
                )
            ]
        ])->create();

        $answer = Answer::factory()->for($form)->make(["respondent_id" => null]);
        $answer->is_last = true;

        Storage::disk('local')->put('laravel.log', '');

        $post = $this->post('/api/answers', $answer->toArray());

        $this->assertDatabaseHas("respondents", [
            "public_id" => $post['data']['respondent'],
        ]);

        $this->assertNotNull($post['data']['respondent']);

        /**
         * log após a execução da rota
         */
        $logContents = file_get_contents($logFilePath);

        $this->assertStringContainsString('Request to zap service failed', $logContents);
    }

    public function test_respondent_can_complete_a_session_with_failed_zap_owner_notification()
	{
        $user = User::factory()->create();
		$form = Form::factory()->for($user)->state([
            'notifications_config' => [
                new NotificationConfig(
                    NotificationResource::ZAP,
                    NotificationContentType::FORMULARIO_FINALIZADO,
                    new NotificationAddress(
                        NotificationAddressType::PHONE_NUMBER,
                    ),
                    true
                ),
            ]
        ])->create();
		$answer = Answer::factory()->for($form)->make(["respondent_id" => null]);
		$answer->is_last = true;

		$post = $this->post('/api/answers', $answer->toArray());

		$this->assertDatabaseHas("respondents", [
			"public_id" => $post['data']['respondent'],
		]);

		$this->assertNotNull($post['data']['respondent']);
	}
}

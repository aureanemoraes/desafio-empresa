<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Form;
use App\Models\User;
use App\Enums\NotificationContentType;
use App\Enums\NotificationResourceType;
use App\ValueObjects\NotificationConfig;
use App\ValueObjects\NotificationResource;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserUnitTest extends TestCase
{
	use RefreshDatabase;

	/** @test */
    public function test_increment_amount_zap_messages_sent_per_month()
    {
        $notificationConfig = new NotificationConfig(
            [
                new NotificationResource(NotificationResourceType::EMAIL, true)
            ],
            NotificationContentType::FORMULARIO_FINALIZADO
        );

        $user = User::factory()->create();

        $form = Form::factory()->for($user)->create([
            'notifications_config' => [$notificationConfig],
        ]);

        $user->incrementAmountZapMessagesSentPerMonth($form);


        $this->assertEquals(
            1,
            $user->zap_message_counter[now()->format('Y-m')]
        );
    }
}

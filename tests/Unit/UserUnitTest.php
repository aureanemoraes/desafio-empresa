<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Form;
use App\Models\User;
use App\Services\FormService;
use App\Enums\NotificationResource;
use App\Enums\NotificationAddressType;
use App\Enums\NotificationContentType;
use App\ValueObjects\NotificationConfig;
use App\ValueObjects\NotificationAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserUnitTest extends TestCase
{
	use RefreshDatabase;

	/** @test */
    public function test_increment_amount_zap_messages_sent_per_month()
    {
        $notificationConfig = new NotificationConfig(
            NotificationResource::ZAP,
            NotificationContentType::FORMULARIO_FINALIZADO,
            new NotificationAddress(
                NotificationAddressType::PHONE_NUMBER
            ),
            true
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

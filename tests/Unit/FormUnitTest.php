<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Form;
use App\Enums\NotificationResource;
use App\Enums\NotificationAddressType;
use App\Enums\NotificationContentType;
use App\ValueObjects\NotificationConfig;
use App\ValueObjects\NotificationAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FormUnitTest extends TestCase
{

	use RefreshDatabase;

	/** @test */
    public function test_cast_of_notification_config()
    {
        $notificationConfig = new NotificationConfig(
            NotificationResource::EMAIL,
            NotificationContentType::FORMULARIO_FINALIZADO,
            new NotificationAddress(
                NotificationAddressType::EMAIL,
                []
            ),
            true,
            []
        );

        $form = Form::factory()->create([
            'notifications_config' => [$notificationConfig], // Aqui garantimos que está sendo passado um array de NotificationConfig
        ]);

        $this->assertEquals(
            [$notificationConfig], // O valor que esperamos encontrar
            $form->notifications_config // O valor recuperado do modelo (depois do cast)
        );
    }
}

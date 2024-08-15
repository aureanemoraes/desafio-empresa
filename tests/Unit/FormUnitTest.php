<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Form;
use App\Enums\NotificationContentType;
use App\Enums\NotificationResourceType;
use App\ValueObjects\NotificationConfig;
use App\ValueObjects\NotificationResource;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FormUnitTest extends TestCase
{

	use RefreshDatabase;

	/** @test */
    public function test_cast_of_notification_config()
    {
        $notificationConfig = new NotificationConfig(
            [
                new NotificationResource(NotificationResourceType::EMAIL, true)
            ],
            NotificationContentType::FORMULARIO_FINALIZADO
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

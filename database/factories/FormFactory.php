<?php

namespace Database\Factories;

use App\Enums\IdType;
use App\Services\IdService;
use Illuminate\Support\Str;
use App\Enums\NotificationResource;
use App\Enums\NotificationAddressType;
use App\Enums\NotificationContentType;
use App\ValueObjects\NotificationConfig;
use App\ValueObjects\NotificationAddress;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Form>
 */
class FormFactory extends Factory
{
	/**
	 * Define the model's default state.
	 *
	 * @return array<string, mixed>
	 */
	public function definition()
	{
		return [
			'slug' => IdService::create(IdType::FORM),
			'user_id' => IdService::create(IdType::USER),
			'title' => $this->faker->sentence,
			'fields' => [
				[
					'type' => 'text', 'label' => 'Name', 'required' => true, 'field_id' => IdService::create(IdType::FIELD),
				],
				[
					'type' => 'email', 'label' => 'Email', 'required' => true, 'field_id' => IdService::create(IdType::FIELD),
				]
			],
            'notifications_config' => null
		];
	}
}

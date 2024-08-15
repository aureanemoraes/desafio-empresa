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

class NotificationAddressTypeUnitTest extends TestCase
{

	use RefreshDatabase;

	/** @test */
    public function test_function_is_valid_value()
    {
        $validEmails = [
            'test@example.com',
            'user.name+tag+sorting@example.com',
            'user+mailbox/department=shipping@example.com',
            'customer/department=shipping@example.co.uk',
        ];

        $invalidEmails = [
            'plainaddress',
            '@missingusername.com',
            'user@.com',
            'user@domain',
        ];

        $validPhoneNumbers = [
            '+55 21 91234-5678',
            '+55 11 99876-5432',
            '+55 62 98765-4321'
        ];

        $invalidPhoneNumbers = [
            '55 96 9888-2272',
            '+55968882272',
            '+55 21 1234-5678',
            '+55 96 9888 2272',
            '+55 1109876-5432',
            '96 9888-2272',
        ];

        $validUrls = [
            'https://www.example.com',
            'http://example.com',
            'https://example.com/path?query=123'
        ];

        $invalidUrls = [
            'http://<script>alert(1)</script>',
            'https://example.com/drop table users',
            'ftp://example.com;select * from users',
            'not_a_url'
        ];

        foreach($validEmails as $validEmail) {
            $this->assertTrue(NotificationAddressType::isValidValue('email', $validEmail));
        }

        foreach($invalidEmails as $invalidEmail) {
            $this->assertFalse(NotificationAddressType::isValidValue('email', $invalidEmail));
        }

        foreach($validPhoneNumbers as $validPhoneNumber) {
            $this->assertTrue(NotificationAddressType::isValidValue('phone_number', $validPhoneNumber));
        }

        foreach($invalidPhoneNumbers as $invalidPhoneNumber) {
            $this->assertFalse(NotificationAddressType::isValidValue('phone_number', $invalidPhoneNumber));
        }

        foreach($validUrls as $validUrl) {
            $this->assertTrue(NotificationAddressType::isValidValue('url', $validUrl));
        }

        foreach($invalidUrls as $invalidUrl) {
            $this->assertFalse(NotificationAddressType::isValidValue('url', $invalidUrl));
        }

    }
}

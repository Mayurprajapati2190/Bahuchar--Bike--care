<?php

namespace Tests\Feature;

use App\Models\Bike;
use App\Models\Customer;
use App\Models\ServiceRecord;
use App\Services\Messaging\WhatsAppLinkBuilder;
use Tests\TestCase;

class WhatsAppLinkBuilderTest extends TestCase
{
    public function test_builds_wa_me_url_for_ten_digit_indian_number(): void
    {
        $builder = new WhatsAppLinkBuilder;

        $url = $builder->build('9824799203', 'Hello customer');

        $this->assertStringStartsWith('https://wa.me/919824799203?', $url);
        $this->assertStringContainsString('text=Hello', $url);
    }
}

<?php

namespace Tests\Feature;

use App\Jobs\SendServiceConfirmationSms;
use App\Models\Bike;
use App\Models\Customer;
use App\Models\ServiceItem;
use App\Models\ServiceRecord;
use App\Models\SmsMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FreeModeSmsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.msg91.enabled' => false,
            'services.msg91.confirmation_template_id' => '',
            'services.msg91.shop_phone' => '+91 98765 43210',
        ]);
    }

    public function test_free_mode_records_confirmation_sms_without_msg91(): void
    {
        $customer = Customer::factory()->create(['phone' => '9876543210']);
        $bike = Bike::factory()->create(['customer_id' => $customer->id]);

        $service = ServiceRecord::factory()->completed()->create([
            'customer_id' => $customer->id,
            'bike_id' => $bike->id,
            'confirmation_sms_sent_at' => null,
        ]);

        ServiceItem::query()->create([
            'service_record_id' => $service->id,
            'description' => 'Oil change',
            'quantity' => 1,
            'unit_price' => 500,
            'amount' => 500,
        ]);

        $job = new SendServiceConfirmationSms($service->id);
        $job->handle(app(\App\Services\Sms\ServiceSmsComposer::class));

        $service->refresh();

        $this->assertNotNull($service->confirmation_sms_sent_at);

        $sms = SmsMessage::query()->where('service_record_id', $service->id)->first();

        $this->assertNotNull($sms);
        $this->assertSame(SmsMessage::STATUS_SENT, $sms->status);
        $this->assertSame(SmsMessage::TYPE_CONFIRMATION, $sms->type);
        $this->assertStringContainsString($customer->name, $sms->body);
        $this->assertStringStartsWith('free-', $sms->provider_message_id);
    }

    public function test_service_completion_sends_sms_immediately_with_sync_queue(): void
    {
        config(['queue.default' => 'sync']);

        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $customer = Customer::factory()->create(['phone' => '9876543210']);
        $bike = Bike::factory()->create(['customer_id' => $customer->id]);

        $service = ServiceRecord::query()->create([
            'customer_id' => $customer->id,
            'bike_id' => $bike->id,
            'service_date' => now()->toDateString(),
            'status' => ServiceRecord::STATUS_IN_PROGRESS,
            'total_amount' => 500,
        ]);

        ServiceItem::query()->create([
            'service_record_id' => $service->id,
            'description' => 'Oil change',
            'quantity' => 1,
            'unit_price' => 500,
            'amount' => 500,
        ]);

        $this->actingAs($user)->post(route('services.complete', $service), [
            'payment_status' => 'paid',
            'payment_method' => 'cash',
        ])->assertRedirect();

        $service->refresh();

        $this->assertNotNull($service->confirmation_sms_sent_at);
        $this->assertDatabaseHas('sms_messages', [
            'service_record_id' => $service->id,
            'type' => SmsMessage::TYPE_CONFIRMATION,
            'status' => SmsMessage::STATUS_SENT,
        ]);
    }
}

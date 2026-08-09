<?php

namespace Tests\Feature;

use App\Jobs\SendServiceConfirmationSms;
use App\Jobs\SendServiceReminderSms;
use App\Models\Bike;
use App\Models\Customer;
use App\Models\ServiceItem;
use App\Models\ServiceRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ServiceRecordTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_completion_creates_bill_and_queues_confirmation_sms(): void
    {
        Queue::fake();

        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $customer = Customer::factory()->create();
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

        $response = $this->actingAs($user)->post(route('services.complete', $service), [
            'payment_status' => 'paid',
            'payment_method' => 'cash',
        ]);

        $service->refresh();

        $response->assertRedirect(route('bills.show', $service->bill));
        $this->assertTrue($service->isCompleted());
        $this->assertNotNull($service->bill);
        $this->assertNotNull($service->bill->bill_number);
        $this->assertEquals(500.0, (float) $service->bill->total_amount);
        $this->assertEquals('paid', $service->bill->payment_status);

        Queue::assertPushed(SendServiceConfirmationSms::class, fn ($job) => $job->serviceRecordId === $service->id);
    }

    public function test_reminder_command_queues_due_services(): void
    {
        Queue::fake();

        $customer = Customer::factory()->create();
        $bike = Bike::factory()->create(['customer_id' => $customer->id]);

        $dueService = ServiceRecord::factory()->completed()->create([
            'customer_id' => $customer->id,
            'bike_id' => $bike->id,
            'next_service_due_at' => now()->subDay()->toDateString(),
            'reminder_sms_sent_at' => null,
        ]);

        ServiceRecord::factory()->completed()->create([
            'customer_id' => $customer->id,
            'bike_id' => $bike->id,
            'next_service_due_at' => now()->addMonth()->toDateString(),
            'reminder_sms_sent_at' => null,
        ]);

        $this->artisan('services:send-reminders')->assertSuccessful();

        Queue::assertPushed(SendServiceReminderSms::class, 1);
        Queue::assertPushed(SendServiceReminderSms::class, fn ($job) => $job->serviceRecordId === $dueService->id);
    }
}

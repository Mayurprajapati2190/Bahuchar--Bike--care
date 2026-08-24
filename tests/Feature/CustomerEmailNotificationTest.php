<?php

namespace Tests\Feature;

use App\Jobs\SendServiceConfirmationEmail;
use App\Mail\ServiceConfirmationMail;
use App\Models\Bike;
use App\Models\Customer;
use App\Models\ServiceItem;
use App\Models\ServiceRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CustomerEmailNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'messaging.email.enabled' => true,
            'mail.default' => 'array',
        ]);
    }

    public function test_confirmation_email_sent_when_customer_has_email(): void
    {
        Mail::fake();

        $customer = Customer::factory()->create([
            'phone' => '9876543210',
            'email' => 'customer@example.com',
        ]);
        $bike = Bike::factory()->create(['customer_id' => $customer->id]);

        $service = ServiceRecord::factory()->completed()->create([
            'customer_id' => $customer->id,
            'bike_id' => $bike->id,
            'confirmation_email_sent_at' => null,
        ]);

        ServiceItem::query()->create([
            'service_record_id' => $service->id,
            'description' => 'Oil change',
            'quantity' => 1,
            'unit_price' => 500,
            'amount' => 500,
        ]);

        $job = new SendServiceConfirmationEmail($service->id);
        $job->handle(app(\App\Services\Messaging\CustomerEmailNotifier::class));

        $service->refresh();

        $this->assertNotNull($service->confirmation_email_sent_at);

        Mail::assertSent(ServiceConfirmationMail::class, function (ServiceConfirmationMail $mail) use ($customer) {
            return $mail->hasTo($customer->email);
        });
    }

    public function test_service_completion_dispatches_confirmation_email(): void
    {
        Mail::fake();
        config(['queue.default' => 'sync']);

        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $customer = Customer::factory()->create([
            'phone' => '9876543210',
            'email' => 'customer@example.com',
        ]);
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

        Mail::assertSent(ServiceConfirmationMail::class);
    }
}

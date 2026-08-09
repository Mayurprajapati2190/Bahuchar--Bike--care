<?php

namespace Tests\Feature\Api;

use App\Jobs\SendServiceConfirmationSms;
use App\Models\Bike;
use App\Models\Customer;
use App\Models\ServiceItem;
use App\Models\ServiceRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class StaffApiTest extends TestCase
{
    use RefreshDatabase;

    private function staffToken(User $user): string
    {
        return $user->createToken('test-device', ['staff'])->plainTextToken;
    }

    public function test_staff_can_login_and_access_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'password' => Hash::make('password'),
        ]);

        $login = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $login->assertOk()
            ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email']]);

        $token = $login->json('token');

        $this->withToken($token)
            ->getJson('/api/v1/dashboard')
            ->assertOk()
            ->assertJsonStructure([
                'stats' => [
                    'total_customers',
                    'services_this_month',
                    'in_progress',
                    'due_reminders',
                    'pending_payments',
                    'pending_amount',
                ],
            ]);
    }

    public function test_staff_can_create_customer_with_service_via_api(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->withToken($this->staffToken($user))
            ->postJson('/api/v1/customers', [
                'name' => 'Raj Patel',
                'phone' => '9876543210',
                'bike' => [
                    'brand' => 'Honda',
                    'model' => 'Activa',
                    'registration_number' => 'GJ01AB1234',
                ],
                'add_service' => true,
                'service_date' => now()->toDateString(),
                'items' => [
                    ['description' => 'General service', 'quantity' => 1, 'unit_price' => 500],
                ],
            ]);

        $response->assertCreated()
            ->assertJsonPath('customer.phone', '9876543210');

        $this->assertDatabaseHas('customers', ['phone' => '9876543210']);
        $this->assertDatabaseHas('service_records', ['status' => ServiceRecord::STATUS_IN_PROGRESS]);
    }

    public function test_service_completion_via_api_creates_bill(): void
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

        $response = $this->withToken($this->staffToken($user))
            ->postJson("/api/v1/services/{$service->id}/complete", [
                'payment_status' => 'paid',
                'payment_method' => 'cash',
            ]);

        $response->assertOk()
            ->assertJsonPath('bill.payment_status', 'paid');

        Queue::assertPushed(SendServiceConfirmationSms::class);
    }

    public function test_unauthenticated_api_requests_are_rejected(): void
    {
        $this->getJson('/api/v1/dashboard')->assertUnauthorized();
    }
}

<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\ServiceRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_create_with_bike_and_service(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($user)->post(route('customers.store'), [
            'name' => 'Raj Patel',
            'phone' => '9876543210',
            'email' => 'raj@example.com',
            'bike' => [
                'brand' => 'Honda',
                'model' => 'Activa',
                'registration_number' => 'GJ01AB1234',
            ],
            'add_service' => true,
            'service_date' => now()->toDateString(),
            'work_done' => 'Oil change',
            'items' => [
                ['description' => 'General service', 'quantity' => 1, 'unit_price' => 500],
            ],
        ]);

        $customer = Customer::query()->where('phone', '9876543210')->first();
        $service = ServiceRecord::query()->where('customer_id', $customer->id)->first();

        $this->assertNotNull($customer);
        $this->assertCount(1, $customer->bikes);
        $this->assertNotNull($service);
        $this->assertSame(ServiceRecord::STATUS_IN_PROGRESS, $service->status);
        $this->assertEquals(500.0, (float) $service->total_amount);

        $response->assertRedirect(route('services.show', $service));
    }

    public function test_customer_create_with_bike_only(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($user)->post(route('customers.store'), [
            'name' => 'Amit Shah',
            'phone' => '9123456789',
            'bike' => [
                'brand' => 'Hero',
                'model' => 'Splendor',
                'registration_number' => 'GJ01CD5678',
            ],
            'add_service' => false,
        ]);

        $customer = Customer::query()->where('phone', '9123456789')->first();

        $this->assertNotNull($customer);
        $this->assertCount(1, $customer->bikes);
        $this->assertSame(0, ServiceRecord::query()->where('customer_id', $customer->id)->count());

        $response->assertRedirect(route('customers.show', $customer));
    }

    public function test_bike_registration_number_is_saved_in_uppercase(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($user)->post(route('customers.store'), [
            'name' => 'Lowercase Reg Test',
            'phone' => '9988776655',
            'bike' => [
                'brand' => 'Honda',
                'model' => 'Activa',
                'registration_number' => 'gj01ab1234',
            ],
            'add_service' => false,
        ]);

        $customer = Customer::query()->where('phone', '9988776655')->first();

        $this->assertNotNull($customer);
        $this->assertSame('GJ01AB1234', $customer->bikes->first()->registration_number);
    }

    public function test_customer_create_requires_bike_registration_number(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($user)
            ->from(route('customers.create'))
            ->post(route('customers.store'), [
                'name' => 'Nikhil Joshi',
                'phone' => '9812345678',
                'bike' => [
                    'brand' => 'TVS',
                    'model' => 'Jupiter',
                ],
                'add_service' => false,
            ]);

        $response->assertSessionHasErrors('bike.registration_number');
        $this->assertSame(0, Customer::query()->where('phone', '9812345678')->count());
    }

    public function test_marketing_home_is_public(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Marketing/Home'));
    }

    public function test_marketing_home_stays_public_when_logged_in(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($user)
            ->get('/')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Marketing/Home'));
    }
}

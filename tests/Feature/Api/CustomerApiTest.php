<?php

namespace Tests\Feature\Api;

use App\Models\Customer;
use App\Models\CustomerOtp;
use App\Models\ServiceRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class CustomerApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_request_and_verify_otp(): void
    {
        Log::spy();

        $customer = Customer::factory()->create(['phone' => '9876543210']);

        $this->postJson('/api/v1/customer/auth/request-otp', [
            'phone' => $customer->phone,
        ])->assertOk();

        $otp = CustomerOtp::query()->where('phone', $customer->phone)->first();
        $this->assertNotNull($otp);

        $verify = $this->postJson('/api/v1/customer/auth/verify-otp', [
            'phone' => $customer->phone,
            'code' => $otp->code,
        ]);

        $verify->assertOk()
            ->assertJsonStructure(['token', 'customer' => ['id', 'name', 'phone']]);
    }

    public function test_customer_can_view_service_history(): void
    {
        $customer = Customer::factory()->create(['phone' => '9123456789']);
        $token = $customer->createToken('test', ['customer'])->plainTextToken;

        ServiceRecord::factory()->completed()->create([
            'customer_id' => $customer->id,
            'bike_id' => \App\Models\Bike::factory()->create(['customer_id' => $customer->id])->id,
        ]);

        $this->withToken($token)
            ->getJson('/api/v1/customer/services')
            ->assertOk()
            ->assertJsonStructure(['data']);
    }

    public function test_staff_token_cannot_access_customer_routes(): void
    {
        $user = \App\Models\User::factory()->create();
        $token = $user->createToken('staff', ['staff'])->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/v1/customer/profile')
            ->assertForbidden();
    }
}

<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminAccessTest extends TestCase
{
    use RefreshDatabase;

    private function superAdmin(): User
    {
        return User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_platform_admin' => true,
        ]);
    }

    private function staff(): User
    {
        return User::factory()->create([
            'role' => User::ROLE_STAFF,
            'is_platform_admin' => false,
        ]);
    }

    public function test_super_admin_can_delete_a_customer(): void
    {
        $admin = $this->superAdmin();
        $customer = Customer::factory()->create();

        $this->actingAs($admin)
            ->delete(route('customers.destroy', $customer))
            ->assertRedirect(route('customers.index'));

        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
    }

    public function test_staff_cannot_delete_a_customer(): void
    {
        $staff = $this->staff();
        $customer = Customer::factory()->create();

        $this->actingAs($staff)
            ->delete(route('customers.destroy', $customer))
            ->assertForbidden();

        $this->assertDatabaseHas('customers', ['id' => $customer->id]);
    }

    public function test_staff_can_open_the_dashboard(): void
    {
        $this->actingAs($this->staff())
            ->get(route('dashboard'))
            ->assertOk();
    }

    public function test_super_admin_can_open_staff_and_backup_pages(): void
    {
        $admin = $this->superAdmin();

        $this->actingAs($admin)->get(route('staff.index'))->assertOk();
        $this->actingAs($admin)->get(route('backups.index'))->assertOk();
    }

    public function test_staff_cannot_open_staff_or_backup_pages(): void
    {
        $staff = $this->staff();

        $this->actingAs($staff)->get(route('staff.index'))->assertForbidden();
        $this->actingAs($staff)->get(route('backups.index'))->assertForbidden();
    }

    public function test_super_admin_can_create_a_staff_login(): void
    {
        $this->actingAs($this->superAdmin())
            ->post(route('staff.store'), [
                'name' => 'Counter Staff',
                'email' => 'counter@bahuchar.test',
                'password' => 'Staff@1234',
            ])
            ->assertRedirect(route('staff.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'counter@bahuchar.test',
            'role' => User::ROLE_STAFF,
            'is_platform_admin' => false,
        ]);
    }
}

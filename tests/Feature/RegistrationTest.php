<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_page_is_public(): void
    {
        $this->get('/register')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Auth/Register'));
    }

    public function test_guest_can_register_as_staff(): void
    {
        $this->post('/register', [
            'name' => 'New Staff',
            'email' => 'newstaff@example.com',
            'password' => 'StaffPass@123',
            'password_confirmation' => 'StaffPass@123',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'newstaff@example.com',
            'role' => User::ROLE_STAFF,
            'is_platform_admin' => 0,
        ]);
    }
}

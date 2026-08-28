<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketingPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_marketing_pages_are_public(): void
    {
        $this->get('/')->assertOk()->assertInertia(fn ($page) => $page->component('Marketing/Home'));
        $this->get('/our-services')->assertOk()->assertInertia(fn ($page) => $page->component('Marketing/Services'));
        $this->get('/why-us')->assertOk()->assertInertia(fn ($page) => $page->component('Marketing/WhyUs'));
        $this->get('/contact')->assertOk()->assertInertia(fn ($page) => $page->component('Marketing/Contact'));
    }

    public function test_marketing_pages_stay_public_when_logged_in(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($user)
            ->get('/')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Marketing/Home'));

        $this->actingAs($user)
            ->get(route('marketing.services'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Marketing/Services'));
    }

    public function test_authenticated_service_records_route_is_not_replaced(): void
    {
        $this->get('/services')->assertRedirect('/login');
    }
}

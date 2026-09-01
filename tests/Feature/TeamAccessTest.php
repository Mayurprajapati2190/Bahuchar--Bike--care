<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamAccessTest extends TestCase
{
    use RefreshDatabase;

    private function superAdmin(): User
    {
        return User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_platform_admin' => true,
        ]);
    }

    public function test_last_selected_team_is_remembered_on_next_visit(): void
    {
        $admin = $this->superAdmin();
        $default = Team::query()->orderBy('id')->first();
        $other = Team::factory()->create(['name' => 'Gota Branch']);

        $this->actingAs($admin)
            ->from(route('dashboard'))
            ->put(route('current-team.update'), ['team_id' => $other->id])
            ->assertRedirect(route('dashboard'));

        $this->assertSame($other->id, $admin->fresh()->current_team_id);

        $this->actingAs($admin->fresh())
            ->get(route('dashboard'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->where('teams.current.id', $other->id)
                ->where('teams.current.name', 'Gota Branch')
            );

        $this->assertSame($other->id, $admin->fresh()->current_team_id);
        $this->assertNotSame($default?->id, $admin->fresh()->current_team_id);
    }

    public function test_staff_only_see_customers_from_their_selected_team(): void
    {
        $teamA = Team::query()->orderBy('id')->first() ?? Team::factory()->create();
        $teamB = Team::factory()->create();

        $staff = User::factory()->create(['role' => User::ROLE_STAFF]);
        $staff->teams()->sync([$teamA->id]);
        $staff->forceFill(['current_team_id' => $teamA->id])->save();

        $visible = Customer::factory()->create([
            'team_id' => $teamA->id,
            'name' => 'Visible Customer',
            'phone' => '9876543210',
        ]);
        $hidden = Customer::factory()->create([
            'team_id' => $teamB->id,
            'name' => 'Hidden Customer',
            'phone' => '9123456789',
        ]);

        $this->actingAs($staff)
            ->get(route('customers.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->has('customers.data', 1)
                ->where('customers.data.0.id', $visible->id)
            );

        $this->actingAs($staff)
            ->get(route('customers.show', $hidden))
            ->assertNotFound();
    }

    public function test_super_admin_can_switch_into_any_team_and_see_its_data(): void
    {
        $admin = $this->superAdmin();
        $teamA = Team::query()->orderBy('id')->first() ?? Team::factory()->create();
        $teamB = Team::factory()->create();

        Customer::factory()->create([
            'team_id' => $teamA->id,
            'name' => 'Team A Customer',
            'phone' => '9876543210',
        ]);
        $teamBCustomer = Customer::factory()->create([
            'team_id' => $teamB->id,
            'name' => 'Team B Customer',
            'phone' => '9123456789',
        ]);

        $this->actingAs($admin)
            ->from(route('dashboard'))
            ->put(route('current-team.update'), ['team_id' => $teamB->id])
            ->assertRedirect(route('dashboard'));

        $this->actingAs($admin->fresh())
            ->get(route('customers.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->has('customers.data', 1)
                ->where('customers.data.0.id', $teamBCustomer->id)
            );
    }

    public function test_super_admin_keeps_staff_and_backup_access_from_any_team(): void
    {
        $admin = $this->superAdmin();
        $other = Team::factory()->create();
        $admin->switchToTeam($other);

        $this->actingAs($admin->fresh())->get(route('staff.index'))->assertOk();
        $this->actingAs($admin->fresh())->get(route('backups.index'))->assertOk();
        $this->actingAs($admin->fresh())->get(route('teams.index'))->assertOk();
    }

    public function test_staff_cannot_switch_to_an_unassigned_team(): void
    {
        $assigned = Team::query()->orderBy('id')->first() ?? Team::factory()->create();
        $other = Team::factory()->create();

        $staff = User::factory()->create(['role' => User::ROLE_STAFF]);
        $staff->teams()->sync([$assigned->id]);
        $staff->forceFill(['current_team_id' => $assigned->id])->save();

        $this->actingAs($staff)
            ->put(route('current-team.update'), ['team_id' => $other->id])
            ->assertForbidden();

        $this->assertSame($assigned->id, $staff->fresh()->current_team_id);
    }

    public function test_staff_cannot_open_teams_page(): void
    {
        $staff = User::factory()->create(['role' => User::ROLE_STAFF]);

        $this->actingAs($staff)
            ->get(route('teams.index'))
            ->assertForbidden();
    }

    public function test_same_phone_can_exist_on_different_teams(): void
    {
        $admin = $this->superAdmin();
        $teamA = Team::query()->orderBy('id')->first() ?? Team::factory()->create();
        $teamB = Team::factory()->create();

        Customer::factory()->create([
            'team_id' => $teamA->id,
            'phone' => '9876543210',
        ]);

        $admin->switchToTeam($teamB);

        $this->actingAs($admin->fresh())
            ->post(route('customers.store'), [
                'name' => 'Other Shop Customer',
                'phone' => '9876543210',
                'bike' => [
                    'brand' => 'Honda',
                    'model' => 'Activa',
                    'registration_number' => 'GJ01XY9999',
                ],
                'add_service' => false,
            ])
            ->assertRedirect();

        $this->assertSame(2, Customer::withoutGlobalScopes()->where('phone', '9876543210')->count());
    }
}

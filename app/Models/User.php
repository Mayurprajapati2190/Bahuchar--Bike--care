<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'role', 'is_platform_admin', 'current_team_id'])]
#[Hidden(['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';

    public const ROLE_STAFF = 'staff';

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_platform_admin' => 'boolean',
        ];
    }

    public function serviceRecords(): HasMany
    {
        return $this->hasMany(ServiceRecord::class, 'created_by');
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)->withTimestamps();
    }

    public function currentTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'current_team_id');
    }

    /**
     * @return Collection<int, Team>
     */
    public function availableTeams(): Collection
    {
        if ($this->isSuperAdmin()) {
            return Team::query()->orderBy('name')->get();
        }

        return $this->teams()->orderBy('name')->get();
    }

    public function belongsToTeam(int|Team $team): bool
    {
        $teamId = $team instanceof Team ? $team->id : $team;

        if ($this->isSuperAdmin()) {
            return Team::query()->whereKey($teamId)->exists();
        }

        return $this->teams()->whereKey($teamId)->exists();
    }

    public function ensureTeamMembership(?Team $team = null): void
    {
        $team ??= Team::ensureDefault();

        $this->teams()->syncWithoutDetaching([$team->id]);

        if (! $this->current_team_id) {
            $this->forceFill(['current_team_id' => $team->id])->save();
        }
    }

    public function switchToTeam(Team $team): void
    {
        abort_unless($this->belongsToTeam($team), 403, 'You do not have access to this shop team.');

        $this->forceFill(['current_team_id' => $team->id])->save();
        $this->setRelation('currentTeam', $team);
    }

    public function resolveCurrentTeam(): Team
    {
        $teams = $this->availableTeams();

        if ($teams->isEmpty()) {
            abort_unless($this->isSuperAdmin(), 403, 'You are not assigned to a shop team. Ask the super admin to add you.');

            $teams = collect([Team::ensureDefault()]);
        }

        $current = $teams->firstWhere('id', $this->current_team_id) ?? $teams->first();

        if ($this->current_team_id !== $current->id) {
            $this->forceFill(['current_team_id' => $current->id])->save();
        }

        $this->setRelation('currentTeam', $current);

        return $current;
    }

    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_platform_admin;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN || $this->isSuperAdmin();
    }

    public function isPlatformAdmin(): bool
    {
        return $this->isSuperAdmin();
    }
}

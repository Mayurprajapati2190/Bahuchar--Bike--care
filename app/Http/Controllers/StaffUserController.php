<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class StaffUserController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Staff/Index', [
            'users' => User::query()
                ->with('teams:id,name')
                ->orderByDesc('is_platform_admin')
                ->orderBy('name')
                ->get(['id', 'name', 'email', 'role', 'is_platform_admin', 'current_team_id', 'created_at']),
            'teams' => Team::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', Password::min(8)],
            'team_ids' => ['nullable', 'array'],
            'team_ids.*' => ['integer', Rule::exists('teams', 'id')],
        ]);

        $teamIds = collect($data['team_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        if ($teamIds->isEmpty() && $request->user()?->current_team_id) {
            $teamIds = collect([(int) $request->user()->current_team_id]);
        }

        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'email_verified_at' => now(),
            'role' => User::ROLE_STAFF,
            'is_platform_admin' => false,
            'current_team_id' => $teamIds->first(),
        ]);

        if ($teamIds->isNotEmpty()) {
            $user->teams()->sync($teamIds->all());
        } else {
            $user->ensureTeamMembership();
        }

        return redirect()
            ->route('staff.index')
            ->with('status', 'Staff login created successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_if($user->isSuperAdmin(), 403, 'The super admin account cannot be deleted.');
        abort_if($user->is(auth()->user()), 403, 'You cannot delete your own account.');

        $user->delete();

        return redirect()
            ->route('staff.index')
            ->with('status', 'Staff login removed.');
    }
}

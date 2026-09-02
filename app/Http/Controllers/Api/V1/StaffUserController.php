<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeamResource;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StaffUserController extends Controller
{
    public function index(): JsonResponse
    {
        $users = User::query()
            ->with('teams:id,name')
            ->orderByDesc('is_platform_admin')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role', 'is_platform_admin', 'current_team_id', 'created_at']);

        $teams = Team::query()->orderBy('name')->get(['id', 'name']);

        return response()->json([
            'users' => $users->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'is_super_admin' => $user->isSuperAdmin(),
                'current_team_id' => $user->current_team_id,
                'teams' => $user->teams->map(fn (Team $team) => [
                    'id' => $team->id,
                    'name' => $team->name,
                ])->values(),
                'created_at' => $user->created_at?->toIso8601String(),
            ]),
            'teams' => TeamResource::collection($teams),
        ]);
    }

    public function store(Request $request): JsonResponse
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

        $user->load('teams:id,name');

        return response()->json([
            'message' => 'Staff login created successfully.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'is_super_admin' => false,
                'teams' => $user->teams->map(fn (Team $team) => [
                    'id' => $team->id,
                    'name' => $team->name,
                ])->values(),
            ],
        ], 201);
    }

    public function destroy(User $user): JsonResponse
    {
        abort_if($user->isSuperAdmin(), 403, 'The super admin account cannot be deleted.');
        abort_if($user->is(auth()->user()), 403, 'You cannot delete your own account.');

        $user->delete();

        return response()->json([
            'message' => 'Staff login removed.',
        ]);
    }
}

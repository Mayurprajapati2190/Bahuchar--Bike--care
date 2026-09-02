<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeamResource;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index(): JsonResponse
    {
        $teams = Team::query()
            ->withCount(['users', 'customers'])
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => TeamResource::collection($teams),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $team = Team::query()->create($this->validated($request));

        return response()->json([
            'message' => 'Shop team created.',
            'team' => new TeamResource($team),
        ], 201);
    }

    public function update(Request $request, Team $team): JsonResponse
    {
        $team->update($this->validated($request, $team));

        return response()->json([
            'message' => 'Shop team updated.',
            'team' => new TeamResource($team->fresh()),
        ]);
    }

    public function destroy(Team $team): JsonResponse
    {
        abort_if(Team::query()->count() <= 1, 403, 'The last shop team cannot be deleted.');
        abort_if($team->customers()->exists(), 403, 'Move or delete this shop’s customers before removing the team.');

        if (auth()->user()?->current_team_id === $team->id) {
            $fallback = Team::query()->whereKeyNot($team->id)->orderBy('name')->first();
            auth()->user()?->forceFill(['current_team_id' => $fallback?->id])->save();
        }

        $team->delete();

        return response()->json([
            'message' => 'Shop team removed.',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Team $team = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:20'],
            'hours' => ['nullable', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'gstin' => ['nullable', 'string', 'max:32'],
            'bill_prefix' => ['nullable', 'string', 'max:20'],
        ]);
    }
}

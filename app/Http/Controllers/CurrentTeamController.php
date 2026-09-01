<?php

namespace App\Http\Controllers;

use App\Http\Resources\TeamResource;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CurrentTeamController extends Controller
{
    public function update(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'team_id' => ['required', 'integer', 'exists:teams,id'],
        ]);

        $team = Team::query()->findOrFail($data['team_id']);
        $request->user()->switchToTeam($team);

        if ($request->expectsJson()) {
            return response()->json([
                'current_team' => new TeamResource($team),
                'message' => 'Switched to '.$team->name.'.',
            ]);
        }

        return redirect()
            ->back()
            ->with('status', 'Now working in '.$team->name.'.');
    }
}

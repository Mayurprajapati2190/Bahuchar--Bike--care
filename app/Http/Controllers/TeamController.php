<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TeamController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Teams/Index', [
            'teams' => Team::query()
                ->withCount(['users', 'customers'])
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        Team::query()->create($data);

        return redirect()
            ->route('teams.index')
            ->with('status', 'Shop team created.');
    }

    public function update(Request $request, Team $team): RedirectResponse
    {
        $team->update($this->validated($request, $team));

        return redirect()
            ->route('teams.index')
            ->with('status', 'Shop team updated.');
    }

    public function destroy(Team $team): RedirectResponse
    {
        abort_if(Team::query()->count() <= 1, 403, 'The last shop team cannot be deleted.');
        abort_if($team->customers()->exists(), 403, 'Move or delete this shop’s customers before removing the team.');

        if (auth()->user()?->current_team_id === $team->id) {
            $fallback = Team::query()->whereKeyNot($team->id)->orderBy('name')->first();
            auth()->user()?->forceFill(['current_team_id' => $fallback?->id])->save();
        }

        $team->delete();

        return redirect()
            ->route('teams.index')
            ->with('status', 'Shop team removed.');
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

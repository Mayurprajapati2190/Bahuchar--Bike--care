<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin User */
class UserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'is_super_admin' => $this->isSuperAdmin(),
            'current_team_id' => $this->current_team_id,
            'current_team' => $this->whenLoaded('currentTeam', fn () => new TeamResource($this->currentTeam)),
            'teams' => TeamResource::collection($this->whenLoaded('teams')),
        ];
    }
}

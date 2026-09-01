<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Support\CurrentTeam;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCurrentTeam
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return $next($request);
        }

        $current = $user->resolveCurrentTeam();

        app(CurrentTeam::class)->set($current->id);

        try {
            return $next($request);
        } finally {
            app(CurrentTeam::class)->clear();
        }
    }
}

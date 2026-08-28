<?php

namespace App\Http\Controllers;

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
                ->orderByDesc('is_platform_admin')
                ->orderBy('name')
                ->get(['id', 'name', 'email', 'role', 'is_platform_admin', 'created_at']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', Password::min(8)],
        ]);

        User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'email_verified_at' => now(),
            'role' => User::ROLE_STAFF,
            'is_platform_admin' => false,
        ]);

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

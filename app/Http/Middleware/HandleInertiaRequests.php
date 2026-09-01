<?php

namespace App\Http\Middleware;

use App\Models\Bill;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => fn () => $user ? [
                    ...$user->only([
                        'id',
                        'name',
                        'email',
                        'role',
                        'is_platform_admin',
                        'current_team_id',
                    ]),
                    'is_super_admin' => $user->isSuperAdmin(),
                ] : null,
            ],
            'teams' => [
                'current' => fn () => $this->currentTeamPayload($user),
                'available' => fn () => $user
                    ? $user->availableTeams()->map(fn ($team) => [
                        'id' => $team->id,
                        'name' => $team->name,
                    ])->values()
                    : collect(),
            ],
            'flash' => [
                'status' => fn () => $request->session()->get('status'),
            ],
            'pendingPayments' => fn () => $request->user() ? [
                'count' => Bill::query()
                    ->whereIn('payment_status', [Bill::PAYMENT_UNPAID, Bill::PAYMENT_PARTIAL])
                    ->count(),
                'amount' => (float) (Bill::query()
                    ->whereIn('payment_status', [Bill::PAYMENT_UNPAID, Bill::PAYMENT_PARTIAL])
                    ->selectRaw('COALESCE(SUM(total_amount - amount_paid), 0) as balance')
                    ->value('balance') ?? 0),
            ] : ['count' => 0, 'amount' => 0],
            'notifications' => [
                'smsLive' => (bool) config('services.msg91.enabled'),
                'smsMode' => config('services.msg91.enabled') ? 'live' : 'free',
                'emailEnabled' => (bool) config('messaging.email.enabled'),
                'emailLive' => config('mail.default') !== 'log',
                'whatsappEnabled' => (bool) config('messaging.whatsapp.enabled'),
            ],
            'shop' => fn () => $user?->currentTeam?->shopPayload() ?? [
                'name' => config('shop.name'),
                'tagline' => config('shop.tagline'),
                'address' => config('shop.address'),
                'phone' => config('shop.phone'),
                'hours' => config('shop.hours'),
            ],
        ];
    }

    /**
     * @return array{id: int, name: string}|null
     */
    private function currentTeamPayload(?User $user): ?array
    {
        $currentTeam = $user?->currentTeam;

        if (! $currentTeam) {
            return null;
        }

        return [
            'id' => $currentTeam->id,
            'name' => $currentTeam->name,
        ];
    }
}

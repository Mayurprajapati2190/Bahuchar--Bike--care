<?php

namespace App\Http\Middleware;

use App\Models\Bill;
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
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user()?->only([
                    'id',
                    'name',
                    'email',
                    'role',
                    'is_platform_admin',
                ]),
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
            ],
            'shop' => fn () => [
                'name' => config('shop.name'),
                'tagline' => config('shop.tagline'),
                'address' => config('shop.address'),
                'phone' => config('shop.phone'),
                'hours' => config('shop.hours'),
            ],
        ];
    }
}

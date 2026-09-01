<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected function authorizeSuperAdmin(): void
    {
        abort_unless(auth()->user()?->isSuperAdmin(), 403, 'Super admin access required.');
    }

    /**
     * @return array<string, mixed>
     */
    protected function shopPayload(): array
    {
        return auth()->user()?->currentTeam?->shopPayload() ?? [
            'name' => config('shop.name'),
            'tagline' => config('shop.tagline'),
            'address' => config('shop.address'),
            'phone' => config('shop.phone'),
            'hours' => config('shop.hours'),
            'gstin' => config('shop.gstin'),
            'bill_prefix' => config('shop.bill_prefix', 'BBC'),
        ];
    }
}

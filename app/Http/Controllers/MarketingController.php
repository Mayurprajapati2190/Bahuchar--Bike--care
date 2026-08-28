<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class MarketingController extends Controller
{
    public function home(): Response
    {
        return Inertia::render('Marketing/Home', [
            'services' => array_slice($this->serviceCatalog(), 0, 4),
        ]);
    }

    public function services(): Response
    {
        return Inertia::render('Marketing/Services', [
            'services' => $this->serviceCatalog(),
        ]);
    }

    public function whyUs(): Response
    {
        return Inertia::render('Marketing/WhyUs');
    }

    public function contact(): Response
    {
        return Inertia::render('Marketing/Contact');
    }

    /**
     * @return list<array{title: string, description: string, icon: string}>
     */
    private function serviceCatalog(): array
    {
        return [
            [
                'title' => 'General Service',
                'description' => 'Complete check-up — oil, filters, chain, brakes, and tuning for smooth rides.',
                'icon' => '🔧',
            ],
            [
                'title' => 'Engine & Oil Care',
                'description' => 'Premium engine oil change, air filter cleaning, and spark plug service.',
                'icon' => '⚙️',
            ],
            [
                'title' => 'Brake & Tyre',
                'description' => 'Brake pad replacement, cable adjustment, tyre pressure, and wheel alignment.',
                'icon' => '🛞',
            ],
            [
                'title' => 'Electrical & Battery',
                'description' => 'Headlight, horn, wiring checks, and battery testing or replacement.',
                'icon' => '⚡',
            ],
            [
                'title' => 'Wash & Polish',
                'description' => 'Deep cleaning, polish, and detailing to keep your bike looking new.',
                'icon' => '✨',
            ],
            [
                'title' => 'Repair & Parts',
                'description' => 'Genuine parts, clutch work, carburettor tuning, and breakdown repairs.',
                'icon' => '🏍️',
            ],
        ];
    }
}

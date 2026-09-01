<?php

namespace Database\Factories;

use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Team>
 */
class TeamFactory extends Factory
{
    protected $model = Team::class;

    public function definition(): array
    {
        $name = fake()->unique()->company().' Bike Care';

        return [
            'name' => $name,
            'address' => fake()->address(),
            'phone' => fake()->numerify('98########'),
            'hours' => 'Mon – Sat: 8:30 AM – 8:30 PM',
            'tagline' => 'Trusted bike service & care',
            'gstin' => '',
            'bill_prefix' => strtoupper(fake()->lexify('??')),
        ];
    }
}

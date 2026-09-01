<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'team_id' => fn () => Team::query()->orderBy('id')->value('id') ?? Team::factory(),
            'name' => fake()->name(),
            'phone' => fake()->unique()->numerify('9#########'),
            'email' => fake()->optional()->safeEmail(),
            'address' => fake()->optional()->address(),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Bike;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bike>
 */
class BikeFactory extends Factory
{
    protected $model = Bike::class;

    public function definition(): array
    {
        $brands = ['Hero', 'Honda', 'Bajaj', 'TVS', 'Yamaha', 'Royal Enfield', 'Suzuki'];

        return [
            'customer_id' => Customer::factory(),
            'brand' => fake()->randomElement($brands),
            'model' => fake()->randomElement(['Splendor', 'Activa', 'Pulsar', 'Apache', 'FZ', 'Classic 350']),
            'registration_number' => strtoupper(fake()->bothify('GJ##??####')),
        ];
    }
}

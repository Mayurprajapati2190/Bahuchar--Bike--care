<?php

namespace Database\Seeders;

use App\Models\Bike;
use App\Models\Customer;
use App\Models\ServiceRecord;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->where('role', User::ROLE_ADMIN)->first();

        $customers = Customer::factory()->count(8)->create();

        foreach ($customers as $customer) {
            $bikes = Bike::factory()->count(fake()->numberBetween(1, 2))->create([
                'customer_id' => $customer->id,
            ]);

            foreach ($bikes as $bike) {
                ServiceRecord::factory()
                    ->count(fake()->numberBetween(1, 3))
                    ->create([
                        'customer_id' => $customer->id,
                        'bike_id' => $bike->id,
                        'created_by' => $admin?->id,
                    ]);
            }
        }
    }
}

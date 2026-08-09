<?php



namespace Database\Factories;



use App\Models\Bike;

use App\Models\Customer;

use App\Models\ServiceItem;

use App\Models\ServiceRecord;

use App\Models\User;

use App\Services\Billing\BillGenerator;

use Illuminate\Database\Eloquent\Factories\Factory;



/**

 * @extends Factory<ServiceRecord>

 */

class ServiceRecordFactory extends Factory

{

    protected $model = ServiceRecord::class;



    public function definition(): array

    {

        $completed = fake()->boolean(60);

        $serviceDate = fake()->dateTimeBetween('-6 months', 'now');

        $total = fake()->randomFloat(2, 200, 3500);



        return [

            'customer_id' => Customer::factory(),

            'bike_id' => Bike::factory(),

            'created_by' => User::factory(),

            'service_date' => $serviceDate,

            'total_amount' => $total,

            'work_done' => fake()->randomElement([

                'General service, oil change, chain lubrication',

                'Full service, brake pads replaced, air filter cleaned',

                'Engine oil change, spark plug replacement',

                'Clutch cable adjustment, tyre pressure check',

            ]),

            'status' => $completed ? ServiceRecord::STATUS_COMPLETED : ServiceRecord::STATUS_IN_PROGRESS,

            'completed_at' => $completed ? $serviceDate : null,

            'next_service_due_at' => $completed

                ? (clone $serviceDate)->modify('+2 months')->format('Y-m-d')

                : null,

        ];

    }



    public function configure(): static

    {

        return $this->afterCreating(function (ServiceRecord $service) {

            ServiceItem::query()->create([

                'service_record_id' => $service->id,

                'description' => 'General service charges',

                'quantity' => 1,

                'unit_price' => $service->total_amount,

                'amount' => $service->total_amount,

                'sort_order' => 0,

            ]);



            if ($service->isCompleted() && ! $service->bill()->exists()) {

                app(BillGenerator::class)->createForService($service, 'cash');

            }

        });

    }



    public function completed(): static

    {

        return $this->state(function (array $attributes) {

            $completedAt = now();



            return [

                'status' => ServiceRecord::STATUS_COMPLETED,

                'completed_at' => $completedAt,

                'next_service_due_at' => $completedAt->copy()->addMonths(2)->toDateString(),

            ];

        });

    }

}



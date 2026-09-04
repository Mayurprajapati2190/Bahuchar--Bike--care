<?php

namespace Database\Seeders;

use App\Models\Bike;
use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->clearDemoCustomers();

        $customer = Customer::query()->create([
            'name' => 'Mayur Prajapati',
            'phone' => '8469842190',
            'email' => null,
            'address' => null,
        ]);

        Bike::query()->create([
            'customer_id' => $customer->id,
            'brand' => 'Honda',
            'model' => 'Activa',
            'registration_number' => 'GJ01UN9358',
        ]);
    }

    private function clearDemoCustomers(): void
    {
        Schema::disableForeignKeyConstraints();

        if (Schema::hasTable('sms_messages')) {
            DB::table('sms_messages')->delete();
        }

        if (Schema::hasTable('bills')) {
            DB::table('bills')->delete();
        }

        if (Schema::hasTable('service_items')) {
            DB::table('service_items')->delete();
        }

        if (Schema::hasTable('service_records')) {
            DB::table('service_records')->delete();
        }

        if (Schema::hasTable('personal_access_tokens')) {
            DB::table('personal_access_tokens')
                ->where('tokenable_type', Customer::class)
                ->delete();
        }

        DB::table('bikes')->delete();
        DB::table('customers')->delete();

        Schema::enableForeignKeyConstraints();
    }
}

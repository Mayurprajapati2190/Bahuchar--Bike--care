<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StaffUserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'staff@bahuchar.test'],
            [
                'name' => 'Shop Staff',
                'password' => Hash::make('Staff@123'),
                'email_verified_at' => now(),
                'role' => User::ROLE_STAFF,
                'is_platform_admin' => false,
            ],
        );
    }
}

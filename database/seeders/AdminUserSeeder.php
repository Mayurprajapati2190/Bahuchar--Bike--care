<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->updateOrCreate(
            ['email' => 'mayurprajapati2190@gmail.com'],
            [
                'name' => 'Mayur Prajapati',
                'password' => Hash::make('Mayur@2190'),
                'email_verified_at' => now(),
                'role' => User::ROLE_ADMIN,
                'is_platform_admin' => true,
            ],
        );

        $admin->ensureTeamMembership();
    }
}

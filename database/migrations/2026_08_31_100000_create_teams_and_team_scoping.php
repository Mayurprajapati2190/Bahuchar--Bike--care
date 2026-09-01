<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('address')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('hours')->nullable();
            $table->string('tagline')->nullable();
            $table->string('gstin')->nullable();
            $table->string('bill_prefix', 20)->default('BBC');
            $table->timestamps();
        });

        $now = now();
        $teamId = DB::table('teams')->insertGetId([
            'name' => config('shop.name') ?: 'Bahuchar Bike Care',
            'slug' => Str::slug(config('shop.name') ?: 'bahuchar-bike-care'),
            'address' => config('shop.address'),
            'phone' => config('shop.phone'),
            'hours' => config('shop.hours'),
            'tagline' => config('shop.tagline'),
            'gstin' => config('shop.gstin'),
            'bill_prefix' => config('shop.bill_prefix', 'BBC'),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Schema::create('team_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['team_id', 'user_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('current_team_id')
                ->nullable()
                ->after('is_platform_admin')
                ->constrained('teams')
                ->nullOnDelete();
        });

        $userIds = DB::table('users')->pluck('id');
        foreach ($userIds as $userId) {
            DB::table('team_user')->insert([
                'team_id' => $teamId,
                'user_id' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        DB::table('users')->update(['current_team_id' => $teamId]);

        foreach (['customers', 'service_records', 'bills', 'sms_messages'] as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->foreignId('team_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('teams')
                    ->restrictOnDelete();
            });

            DB::table($table)->update(['team_id' => $teamId]);
        }

        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique(['phone']);
            $table->unique(['team_id', 'phone']);
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique(['team_id', 'phone']);
            $table->unique('phone');
        });

        foreach (['sms_messages', 'bills', 'service_records', 'customers'] as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->dropConstrainedForeignId('team_id');
            });
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('current_team_id');
        });

        Schema::dropIfExists('team_user');
        Schema::dropIfExists('teams');
    }
};

<?php

use App\Console\Commands\BackupShopData;
use App\Console\Commands\SendServiceReminders;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(SendServiceReminders::class)
    ->dailyAt('09:00')
    ->timezone('Asia/Kolkata');

Schedule::command(BackupShopData::class)
    ->monthlyOn(1, '02:00')
    ->timezone('Asia/Kolkata');

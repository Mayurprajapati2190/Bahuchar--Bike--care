<?php

namespace App\Console\Commands;

use App\Jobs\SendServiceReminderEmail;
use App\Jobs\SendServiceReminderSms;
use App\Models\ServiceRecord;
use Illuminate\Console\Command;

class SendServiceReminders extends Command
{
    protected $signature = 'services:send-reminders';

    protected $description = 'Queue reminder SMS for services due today or overdue';

    public function handle(): int
    {
        $today = now()->toDateString();

        $dueServices = ServiceRecord::query()
            ->where('status', ServiceRecord::STATUS_COMPLETED)
            ->whereNull('reminder_sms_sent_at')
            ->whereNotNull('next_service_due_at')
            ->whereDate('next_service_due_at', '<=', $today)
            ->pluck('id');

        foreach ($dueServices as $serviceId) {
            SendServiceReminderSms::dispatch($serviceId);
            SendServiceReminderEmail::dispatch($serviceId);
        }

        $this->info("Queued {$dueServices->count()} reminder SMS job(s).");

        return self::SUCCESS;
    }
}

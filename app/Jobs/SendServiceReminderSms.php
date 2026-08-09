<?php

namespace App\Jobs;

use App\Models\ServiceRecord;
use App\Services\Sms\ServiceSmsComposer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendServiceReminderSms implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(
        public int $serviceRecordId,
        public bool $force = false,
    ) {}

    public function handle(ServiceSmsComposer $composer): void
    {
        $service = ServiceRecord::query()
            ->with('customer')
            ->find($this->serviceRecordId);

        if ($service === null || ! $service->isCompleted()) {
            return;
        }

        if (! $this->force && $service->reminder_sms_sent_at !== null) {
            return;
        }

        if (! $this->force && $service->next_service_due_at !== null && $service->next_service_due_at->isFuture()) {
            return;
        }

        $composer->sendReminder($service);
    }
}

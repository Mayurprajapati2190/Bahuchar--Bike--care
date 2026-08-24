<?php

namespace App\Jobs;

use App\Models\ServiceRecord;
use App\Services\Messaging\CustomerEmailNotifier;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendServiceReminderEmail implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(
        public int $serviceRecordId,
        public bool $force = false,
    ) {}

    public function handle(CustomerEmailNotifier $notifier): void
    {
        $service = ServiceRecord::query()
            ->with(['customer', 'bike'])
            ->find($this->serviceRecordId);

        if ($service === null || ! $service->isCompleted()) {
            return;
        }

        if (! $this->force && $service->reminder_email_sent_at !== null) {
            return;
        }

        $notifier->sendReminder($service, $this->force);
    }
}

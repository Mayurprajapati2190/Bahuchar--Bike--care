<?php

namespace App\Jobs;

use App\Models\ServiceRecord;
use App\Services\Messaging\CustomerEmailNotifier;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendServiceConfirmationEmail implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(public int $serviceRecordId) {}

    public function handle(CustomerEmailNotifier $notifier): void
    {
        $service = ServiceRecord::query()
            ->with(['customer', 'bike'])
            ->find($this->serviceRecordId);

        if ($service === null || ! $service->isCompleted()) {
            return;
        }

        if ($service->confirmation_email_sent_at !== null) {
            return;
        }

        $notifier->sendConfirmation($service);
    }
}

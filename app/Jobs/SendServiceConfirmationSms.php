<?php

namespace App\Jobs;

use App\Models\ServiceRecord;
use App\Services\Sms\ServiceSmsComposer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendServiceConfirmationSms implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(public int $serviceRecordId) {}

    public function handle(ServiceSmsComposer $composer): void
    {
        $service = ServiceRecord::query()
            ->with('customer')
            ->find($this->serviceRecordId);

        if ($service === null || ! $service->isCompleted()) {
            return;
        }

        if ($service->confirmation_sms_sent_at !== null) {
            return;
        }

        $composer->sendConfirmation($service);
    }
}

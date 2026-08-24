<?php

namespace App\Services\Messaging;

use App\Mail\ServiceConfirmationMail;
use App\Mail\ServiceReminderMail;
use App\Models\ServiceRecord;
use App\Services\Sms\ServiceSmsComposer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CustomerEmailNotifier
{
    public function __construct(private ServiceSmsComposer $smsComposer) {}

    public function sendConfirmation(ServiceRecord $service): bool
    {
        if (! config('messaging.email.enabled')) {
            return false;
        }

        $service->loadMissing('customer');

        if ($service->confirmation_email_sent_at !== null || blank($service->customer?->email)) {
            return false;
        }

        $payload = $this->smsComposer->confirmationPayload($service);

        try {
            Mail::to($service->customer->email)->send(
                new ServiceConfirmationMail($service, $payload['body']),
            );

            $service->update(['confirmation_email_sent_at' => now()]);

            Log::info('Customer confirmation email sent', [
                'service_id' => $service->id,
                'email' => $service->customer->email,
                'mailer' => config('mail.default'),
            ]);

            return true;
        } catch (\Throwable $exception) {
            Log::error('Customer confirmation email failed', [
                'service_id' => $service->id,
                'email' => $service->customer->email,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    public function sendReminder(ServiceRecord $service, bool $force = false): bool
    {
        if (! config('messaging.email.enabled')) {
            return false;
        }

        $service->loadMissing('customer');

        if (blank($service->customer?->email)) {
            return false;
        }

        if (! $force && $service->reminder_email_sent_at !== null) {
            return false;
        }

        $payload = $this->smsComposer->reminderPayload($service);

        try {
            Mail::to($service->customer->email)->send(
                new ServiceReminderMail($service, $payload['body']),
            );

            $service->update(['reminder_email_sent_at' => now()]);

            Log::info('Customer reminder email sent', [
                'service_id' => $service->id,
                'email' => $service->customer->email,
                'mailer' => config('mail.default'),
            ]);

            return true;
        } catch (\Throwable $exception) {
            Log::error('Customer reminder email failed', [
                'service_id' => $service->id,
                'email' => $service->customer->email,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }
}

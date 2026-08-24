<?php

namespace App\Services\Messaging;

use App\Models\ServiceRecord;
use App\Services\Sms\ServiceSmsComposer;

class CustomerMessagePresenter
{
    public function __construct(
        private ServiceSmsComposer $smsComposer,
        private WhatsAppLinkBuilder $whatsApp,
    ) {}

    /**
     * @return array<string, mixed>|null
     */
    public function forService(ServiceRecord $service): ?array
    {
        if (! $service->isCompleted()) {
            return null;
        }

        $service->loadMissing(['customer', 'bike']);

        $confirmation = $this->smsComposer->confirmationPayload($service);
        $reminder = $this->smsComposer->reminderPayload($service);
        $phone = $service->customer->phone;

        return [
            'customer_has_email' => filled($service->customer->email),
            'customer_email' => $service->customer->email,
            'confirmation' => [
                'body' => $confirmation['body'],
                'whatsapp_url' => config('messaging.whatsapp.enabled')
                    ? $this->whatsApp->build($phone, $confirmation['body'])
                    : null,
            ],
            'reminder' => [
                'body' => $reminder['body'],
                'whatsapp_url' => config('messaging.whatsapp.enabled')
                    ? $this->whatsApp->build($phone, $reminder['body'])
                    : null,
            ],
        ];
    }
}

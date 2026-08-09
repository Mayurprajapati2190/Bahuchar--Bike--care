<?php

namespace App\Services\Sms;

use App\Models\ServiceRecord;
use App\Models\SmsMessage;

class ServiceSmsComposer
{
    public function __construct(private Msg91Client $client) {}

    /**
     * @return array{body: string, variables: array<int, string>, template_id: string}
     */
    public function confirmationPayload(ServiceRecord $service): array
    {
        $service->loadMissing(['customer', 'bike']);

        $variables = [
            $service->customer->name,
            $service->bike->displayName(),
            number_format((float) $service->total_amount, 2),
            $service->next_service_due_at?->format('d M Y') ?? 'N/A',
            config('services.msg91.shop_phone'),
        ];

        $body = sprintf(
            'Dear %s, your bike (%s) service at Bahuchar Bike Care is complete. Amount: Rs %s. Next service due: %s. Call %s.',
            ...$variables,
        );

        return [
            'body' => $body,
            'variables' => $variables,
            'template_id' => config('services.msg91.confirmation_template_id'),
        ];
    }

    /**
     * @return array{body: string, variables: array<int, string>, template_id: string}
     */
    public function reminderPayload(ServiceRecord $service): array
    {
        $service->loadMissing(['customer', 'bike']);

        $variables = [
            $service->customer->name,
            $service->bike->displayName(),
            config('services.msg91.shop_phone'),
        ];

        $body = sprintf(
            'Dear %s, your bike (%s) service is due at Bahuchar Bike Care. Book now: %s.',
            ...$variables,
        );

        return [
            'body' => $body,
            'variables' => $variables,
            'template_id' => config('services.msg91.reminder_template_id'),
        ];
    }

    public function sendConfirmation(ServiceRecord $service): SmsMessage
    {
        $payload = $this->confirmationPayload($service);

        return $this->dispatch(
            service: $service,
            type: SmsMessage::TYPE_CONFIRMATION,
            payload: $payload,
            sentAtColumn: 'confirmation_sms_sent_at',
        );
    }

    public function sendReminder(ServiceRecord $service): SmsMessage
    {
        $payload = $this->reminderPayload($service);

        return $this->dispatch(
            service: $service,
            type: SmsMessage::TYPE_REMINDER,
            payload: $payload,
            sentAtColumn: 'reminder_sms_sent_at',
        );
    }

    /**
     * @param  array{body: string, variables: array<int, string>, template_id: string}  $payload
     */
    private function dispatch(ServiceRecord $service, string $type, array $payload, string $sentAtColumn): SmsMessage
    {
        $sms = SmsMessage::query()->create([
            'service_record_id' => $service->id,
            'phone' => $service->customer->phone,
            'type' => $type,
            'status' => SmsMessage::STATUS_PENDING,
            'body' => $payload['body'],
        ]);

        $response = $this->client->sendTemplate(
            phone: $service->customer->phone,
            templateId: $payload['template_id'] ?? '',
            variables: $payload['variables'],
            body: $payload['body'],
        );

        if ($response->success) {
            $sms->update([
                'status' => SmsMessage::STATUS_SENT,
                'provider_message_id' => $response->messageId,
                'sent_at' => now(),
            ]);

            $service->update([$sentAtColumn => now()]);
        } else {
            $sms->update([
                'status' => SmsMessage::STATUS_FAILED,
                'error_message' => $response->message,
            ]);
        }

        return $sms->fresh();
    }
}

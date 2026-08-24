<?php

namespace App\Support;

class CustomerMessagingStatus
{
    public static function completionNote(bool $customerHasEmail): string
    {
        $parts = [];

        if (config('services.msg91.enabled')) {
            $parts[] = 'SMS queued';
        } else {
            $parts[] = 'SMS logged (free mode)';
        }

        if (config('messaging.email.enabled') && $customerHasEmail) {
            $parts[] = self::emailDeliveryNote();
        }

        if (config('messaging.whatsapp.enabled')) {
            $parts[] = 'WhatsApp ready to send';
        }

        return implode(' · ', $parts).'.';
    }

    public static function reminderNote(bool $customerHasEmail): string
    {
        return self::completionNote($customerHasEmail);
    }

    public static function emailDeliveryNote(): string
    {
        return config('mail.default') === 'log'
            ? 'email logged (set SMTP for delivery)'
            : 'email queued';
    }
}

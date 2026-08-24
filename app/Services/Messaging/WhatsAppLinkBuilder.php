<?php

namespace App\Services\Messaging;

class WhatsAppLinkBuilder
{
    public function build(string $phone, string $message): string
    {
        $digits = preg_replace('/\D/', '', $phone) ?? '';

        if (strlen($digits) === 10) {
            $digits = '91'.$digits;
        } elseif (str_starts_with($digits, '0') && strlen($digits) === 11) {
            $digits = '91'.substr($digits, 1);
        }

        return 'https://wa.me/'.$digits.'?text='.rawurlencode($message);
    }
}

<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Msg91Client
{
    /**
     * @param  array<int, string>  $variables
     */
    public function sendTemplate(string $phone, string $templateId, array $variables, ?string $body = null): Msg91Response
    {
        $phone = $this->normalizePhone($phone);

        if (! config('services.msg91.enabled')) {
            Log::info('SMS free mode — message logged (not sent to phone)', [
                'phone' => $phone,
                'template_id' => $templateId ?: 'none',
                'body' => $body ?? implode(' | ', $variables),
            ]);

            return new Msg91Response(
                success: true,
                messageId: 'free-'.uniqid(),
                message: 'SMS logged (free mode — MSG91 disabled)',
            );
        }

        $response = Http::withHeaders([
            'authkey' => config('services.msg91.auth_key'),
            'Content-Type' => 'application/json',
        ])->post('https://control.msg91.com/api/v5/flow/', [
            'template_id' => $templateId,
            'short_url' => '0',
            'recipients' => [
                [
                    'mobiles' => '91'.$phone,
                    'var1' => $variables[0] ?? '',
                    'var2' => $variables[1] ?? '',
                    'var3' => $variables[2] ?? '',
                    'var4' => $variables[3] ?? '',
                    'var5' => $variables[4] ?? '',
                ],
            ],
        ]);

        if ($response->successful()) {
            $payload = $response->json();
            $messageId = is_array($payload) ? ($payload['message'] ?? $payload['request_id'] ?? null) : null;

            return new Msg91Response(
                success: true,
                messageId: is_string($messageId) ? $messageId : json_encode($payload),
            );
        }

        return new Msg91Response(
            success: false,
            message: $response->body(),
        );
    }

    public function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone) ?? '';

        if (str_starts_with($digits, '91') && strlen($digits) === 12) {
            $digits = substr($digits, 2);
        }

        return $digits;
    }
}

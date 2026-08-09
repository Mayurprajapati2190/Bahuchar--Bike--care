<?php

namespace App\Services\Sms;

class Msg91Response
{
    public function __construct(
        public bool $success,
        public ?string $messageId = null,
        public ?string $message = null,
    ) {}
}

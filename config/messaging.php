<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Customer email notifications
    |--------------------------------------------------------------------------
    |
    | When enabled, confirmation and reminder emails are sent when the customer
    | has an email address. Use MAIL_MAILER=log for free testing (writes to log).
    | Use smtp/gmail for real delivery.
    |
    */

    'email' => [
        'enabled' => env('MESSAGING_EMAIL_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | WhatsApp (free — opens wa.me with pre-filled message)
    |--------------------------------------------------------------------------
    */

    'whatsapp' => [
        'enabled' => env('MESSAGING_WHATSAPP_ENABLED', true),
    ],

];

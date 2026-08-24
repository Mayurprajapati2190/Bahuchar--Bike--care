<?php

namespace App\Mail;

use App\Models\ServiceRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ServiceConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ServiceRecord $service,
        public string $messageBody,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: config('shop.name').' — Service Complete',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.service-confirmation',
            with: [
                'customerName' => $this->service->customer->name,
                'bikeName' => $this->service->bike->displayName(),
                'messageBody' => $this->messageBody,
                'shopName' => config('shop.name'),
                'shopPhone' => config('shop.phone'),
                'shopAddress' => config('shop.address'),
            ],
        );
    }
}

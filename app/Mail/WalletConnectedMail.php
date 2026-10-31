<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WalletConnectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🔐 New Wallet Connection - ' . ($this->data['date'] ?? now()->toIso8601String()),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.wallet-connected',
        );
    }
}
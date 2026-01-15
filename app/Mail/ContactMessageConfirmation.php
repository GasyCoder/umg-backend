<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMessageConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $name,
        public string $subjectLine
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre message a bien été reçu',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact.confirmation',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

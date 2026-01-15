<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMessageToUniversity extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $name,
        public string $email,
        public string $subjectLine,
        public string $messageBody
    ) {
        $this->replyTo($this->email, $this->name);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nouveau message de contact - ' . $this->subjectLine,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact.admin',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

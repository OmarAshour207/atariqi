<?php

namespace App\Mail;

use App\Models\Document;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DocumentUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Document $document,
        public User $user
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Document Updated') . ' - ' . ($this->document->{'title-ar'} ?? $this->document->{'title-eng'}),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.document_updated',
            with: [
                'document' => $this->document,
                'user' => $this->user,
            ],
        );
    }
}

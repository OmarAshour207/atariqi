<?php

namespace App\Mail;

use App\Models\Document;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;

class DocumentUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Document $document,
        public User $user,
        public ?string $attachmentPath = null
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

    public function attachments(): array
    {
        $path = $this->attachmentPath
            ?? public_path($this->document->getRawOriginal('file-link'));

        if (! $path || ! File::exists($path)) {
            return [];
        }

        $filename = basename($path);

        return [
            Attachment::fromPath($path)
                ->as($filename)
                ->withMime('application/pdf'),
        ];
    }
}

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
        $title = (string) ($this->document->{'title-ar'} ?? $this->document->{'title-eng'} ?? '');
        $title = $this->normalizeDocumentTitle($title);

        return new Envelope(
            subject: __('Document Updated') . ' - ' . $title,
        );
    }

    private function normalizeDocumentTitle(string $title): string
    {
        $normalized = preg_replace('/\s+/u', ' ', trim($title)) ?? $title;

        // Fix known Arabic title typo: "شروطو الأحكام" / extra spaces.
        if (preg_match('/شروط\s*و\s*الأ?حكام/u', $normalized)
            || preg_match('/^شروطو\s*الأ?حكام$/u', $normalized)
        ) {
            return 'الشروط و الأحكام';
        }

        return $normalized;
    }

    public function content(): Content
    {
        $title = $this->normalizeDocumentTitle(
            (string) ($this->document->{'title-ar'} ?? $this->document->{'title-eng'} ?? '')
        );

        return new Content(
            view: 'mail.document_updated',
            with: [
                'document' => $this->document,
                'user' => $this->user,
                'documentTitleAr' => $title,
                'documentTitleEn' => (string) ($this->document->{'title-eng'} ?? $title),
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

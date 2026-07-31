<?php

namespace App\Mail;

use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Ticket $ticket,
        public TicketReply $reply
    ) {
        $this->reply->loadMissing('attachments');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Support Ticket Reply') . ' - ' . $this->ticket->ticket_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.ticket_reply',
            with: [
                'ticket' => $this->ticket,
                'reply' => $this->reply,
            ],
        );
    }

    public function attachments(): array
    {
        return $this->reply->attachments
            ->map(function ($attachment) {
                $path = public_path($attachment->file_path);

                if (!is_file($path)) {
                    return null;
                }

                return Attachment::fromPath($path);
            })
            ->filter()
            ->values()
            ->all();
    }
}

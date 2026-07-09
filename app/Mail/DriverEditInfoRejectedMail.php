<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DriverEditInfoRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $driver,
        public string $reason
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Driver Info Update Rejected'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.driver_edit_info_rejected',
            with: [
                'driver' => $this->driver,
                'reason' => $this->reason,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

<?php

namespace App\Mail;

use App\Models\Package;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PackageCancellationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $driver,
        public ?Package $oldPackage,
        public Package $freePackage
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Package Cancellation Notification'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.package_cancellation',
            with: [
                'driver' => $this->driver,
                'oldPackage' => $this->oldPackage,
                'freePackage' => $this->freePackage,
            ],
        );
    }
}

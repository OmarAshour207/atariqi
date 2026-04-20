<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class DriverRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $driver;
    public $reason;

    public function __construct(User $driver, $reason)
    {
        $this->driver = $driver;
        $this->reason = $reason;
    }

    public function envelope()
    {
        return new Envelope(
            subject: __('Driver Application Rejected'),
        );
    }

    public function content()
    {
        return new Content(
            view: 'mail.driver_rejected',
            with: [
                'driver' => $this->driver,
                'reason' => $this->reason,
            ],
        );
    }

    public function attachments()
    {
        return [];
    }
}

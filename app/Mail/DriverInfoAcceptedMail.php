<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class DriverInfoAcceptedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $driver;

    public function __construct(User $driver)
    {
        $this->driver = $driver;
    }

    public function envelope()
    {
        return new Envelope(
            subject: __('Driver Information Update Accepted'),
        );
    }

    public function content()
    {
        return new Content(
            view: 'mail.driver_info_accepted',
            with: [
                'driver' => $this->driver,
            ],
        );
    }

    public function attachments()
    {
        return [];
    }
}

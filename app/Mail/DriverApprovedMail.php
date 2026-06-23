<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DriverApprovedMail extends Mailable
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
            subject: __('Captain Application Approved'),
        );
    }

    public function content()
    {
        return new Content(
            view: 'mail.driver_approved',
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

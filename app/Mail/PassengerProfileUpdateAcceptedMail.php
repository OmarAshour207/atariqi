<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PassengerProfileUpdateAcceptedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $passenger;

    public function __construct(User $passenger)
    {
        $this->passenger = $passenger;
    }

    public function envelope()
    {
        return new Envelope(
            subject: __('Profile Update Accepted'),
        );
    }

    public function content()
    {
        return new Content(
            view: 'mail.passenger_profile_update_accepted',
            with: [
                'passenger' => $this->passenger,
            ],
        );
    }

    public function attachments()
    {
        return [];
    }
}

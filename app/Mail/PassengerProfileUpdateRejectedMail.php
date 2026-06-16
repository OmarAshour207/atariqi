<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PassengerProfileUpdateRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $passenger;
    public $reason;

    public function __construct(User $passenger, $reason)
    {
        $this->passenger = $passenger;
        $this->reason = $reason;
    }

    public function envelope()
    {
        return new Envelope(
            subject: __('Profile Update Rejected'),
        );
    }

    public function content()
    {
        return new Content(
            view: 'mail.passenger_profile_update_rejected',
            with: [
                'passenger' => $this->passenger,
                'reason' => $this->reason,
            ],
        );
    }

    public function attachments()
    {
        return [];
    }
}

<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class PassengerBannedMail extends Mailable
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
            subject: __('Passenger Banned'),
        );
    }

    public function content()
    {
        return new Content(
            view: 'mail.passenger_banned',
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

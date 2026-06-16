<?php

namespace App\Mail;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PassengerRequestAssignmentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $admin;
    public $passenger;
    public $note;
    public $assignedBy;

    public function __construct(Admin $admin, User $passenger, string $note, Admin $assignedBy)
    {
        $this->admin = $admin;
        $this->passenger = $passenger;
        $this->note = $note;
        $this->assignedBy = $assignedBy;
    }

    public function envelope()
    {
        return new Envelope(
            subject: __('Passenger Profile Update Assignment'),
        );
    }

    public function content()
    {
        return new Content(
            view: 'mail.passenger_request_assignment',
            with: [
                'admin' => $this->admin,
                'passenger' => $this->passenger,
                'note' => $this->note,
                'assignedBy' => $this->assignedBy,
            ],
        );
    }

    public function attachments()
    {
        return [];
    }
}

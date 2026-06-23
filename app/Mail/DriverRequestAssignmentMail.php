<?php

namespace App\Mail;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DriverRequestAssignmentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $admin;
    public $driver;
    public $note;
    public $assignedBy;

    public function __construct(Admin $admin, User $driver, string $note, Admin $assignedBy)
    {
        $this->admin = $admin;
        $this->driver = $driver;
        $this->note = $note;
        $this->assignedBy = $assignedBy;
    }

    public function envelope()
    {
        return new Envelope(
            subject: __('Driver Request Assignment'),
        );
    }

    public function content()
    {
        return new Content(
            view: 'mail.driver_request_assignment',
            with: [
                'admin' => $this->admin,
                'driver' => $this->driver,
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

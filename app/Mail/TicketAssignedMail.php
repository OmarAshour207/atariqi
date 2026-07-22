<?php

namespace App\Mail;

use App\Models\Admin;
use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketAssignedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Ticket $ticket,
        public Admin $assignee,
        public Admin $assignedBy
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Support Ticket Assigned') . ' - ' . $this->ticket->ticket_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.ticket_assigned',
            with: [
                'ticket' => $this->ticket,
                'assignee' => $this->assignee,
                'assignedBy' => $this->assignedBy,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

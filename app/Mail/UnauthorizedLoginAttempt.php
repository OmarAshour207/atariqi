<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Request;

class UnauthorizedLoginAttempt extends Mailable
{
    use Queueable, SerializesModels;

    public $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Unauthorized Login Attempt Detected',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.unauthorized_login_attempt',
            with: [
                'ip' => $this->request->ip(),
                'userAgent' => $this->request->userAgent(),
                'url' => $this->request->fullUrl(),
                'time' => now(),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Package;

class PackageAssignmentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $driver;
    public $package;
    public $interval;

    public function __construct(User $driver, Package $package, $interval)
    {
        $this->driver = $driver;
        $this->package = $package;
        $this->interval = $interval;
    }

    public function envelope()
    {
        return new Envelope(
            subject: __('Package Assignment Notification'),
        );
    }

    public function content()
    {
        return new Content(
            view: 'mail.package_assignment',
            with: [
                'driver' => $this->driver,
                'package' => $this->package,
                'interval' => $this->interval,
            ],
        );
    }
}

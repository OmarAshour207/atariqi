<?php

namespace App\Mail;

use App\Models\Package;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PackageDeletedNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Package $package;
    public User $customer;

    public function __construct(Package $package, User $customer)
    {
        $this->package = $package;
        $this->customer = $customer;
    }

    public function envelope()
    {
        return new Envelope(
            subject: __('Package Deletion Notification'),
        );
    }

    public function content()
    {
        return new Content(
            view: 'mail.package_deleted_notification',
            with: [
                'package' => $this->package,
                'customer' => $this->customer,
            ],
        );
    }

    public function attachments()
    {
        return [];
    }
}

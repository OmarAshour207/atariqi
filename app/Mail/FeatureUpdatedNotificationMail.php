<?php

namespace App\Mail;

use App\Models\Feature;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FeatureUpdatedNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Feature $feature;
    public User $customer;

    public function __construct(Feature $feature, User $customer)
    {
        $this->feature = $feature;
        $this->customer = $customer;
    }

    public function envelope()
    {
        return new Envelope(
            subject: __('Feature Updated Notification'),
        );
    }

    public function content()
    {
        return new Content(
            view: 'mail.feature_updated_notification',
            with: [
                'feature' => $this->feature,
                'customer' => $this->customer,
            ],
        );
    }

    public function attachments()
    {
        return [];
    }
}

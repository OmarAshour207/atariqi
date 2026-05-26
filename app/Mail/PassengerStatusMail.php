<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PassengerStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $passenger;
    public $status;
    public $info;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($passenger, $status, $info = null)
    {
        $this->passenger = $passenger;
        $this->status = $status;
        $this->info = $info;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(__('Your Account Status Update'))
            ->view('mail.passenger_status');
    }
}

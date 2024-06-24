<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\Driver\Traits\Payment;
use Illuminate\Console\Command;

class PaymentReminder extends Command
{
    use Payment;
    protected $signature = 'payment-reminder';

    protected $description = 'Reminder the driver to pay their Dues';

    public function handle()
    {
        // sms or push notification

        return Command::SUCCESS;
    }
}

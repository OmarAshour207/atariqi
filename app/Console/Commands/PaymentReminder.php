<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\Driver\Traits\Payment;
use App\Models\User;
use Illuminate\Console\Command;

class PaymentReminder extends Command
{
    use Payment;
    protected $signature = 'payment-reminder';

    protected $description = 'Reminder the driver to pay their Dues';

    public function handle()
    {
        // sms or push notification
        $data = User::with('paymentReminders')
            ->where('user-type', 'driver')
            ->where('approval', 1)
            ->where('id', 22)
            ->chunk(100, function ($drivers) {
                foreach ($drivers as $driver ) {
                    $sms = false;
                    $message = __("please pay your due to avoid stop the service");
                    $paymentReminder = $driver->paymentReminders->first();
                    // First time send an sms message
                    if(!$paymentReminder) {
                        $sms = true;
                        sendSMS($driver->phone_number, null, $message);
                    } else {

                    }

                    \App\Models\PaymentReminder::create([
                        'driver-id' => $driver->id,
                        'type'      => $sms ? 'sms' : 'notify'
                    ]);
                }
            });
//        dd($drivers);
        dd('asd');
//        \App\Models\PaymentReminder::whereIn('driver-id', $drivers)
//        return Command::SUCCESS;
    }
}

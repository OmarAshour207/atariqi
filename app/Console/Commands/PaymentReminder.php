<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\Driver\Traits\Payment;
use App\Mail\PaymentReminderMail;
use App\Models\FinancialDue;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class PaymentReminder extends Command
{
    use Payment;

    public $subscriptionCost = 0;

    protected $signature = 'payment-reminder';

    protected $description = 'Reminder the driver to pay their Dues';

    public function __construct($subscriptionCost = 0)
    {
        parent::__construct();
        $this->subscriptionCost = $subscriptionCost;
    }

    public function handle()
    {
        // sms or push notification
        $data = User::with('paymentReminders')
            ->where('user-type', 'driver')
            ->where('approval', 1)
//            ->where('id', 22)
            ->chunk(100, function ($drivers) {
                foreach ($drivers as $driver ) {
                    $details = [];
                    $dates['start_date'] = $this->getLastPayDate($driver->id) ?? $driver->{"date-of-add"};
                    $dates['end_date'] = Carbon::now()->format('Y-m-d'); // $paymentReminder ? Carbon::parse($paymentReminder->created_at)->format('Y-m-d') :

                    $details['amount'] = $this->getDues($driver->id, $dates);

                    $paymentReminder = $driver->paymentReminders->first();

                    if(!$paymentReminder || $details['amount'] < 50) {
                        continue;
                    }

                    if (Carbon::parse($paymentReminder->created_at)->format('Y-m-d') < Carbon::now()->subDays(7)->format('Y-m-d')) {
                        continue;
                    }

                    $sms = false;
                    $message = __("please pay your due to avoid stop the service") . ': ' . $details['amount'] . __('SAR');

                    // First time send an sms message
                    if(!$paymentReminder) {
                        $sms = true;
                        sendSMS($driver->phone_number, null, $message);
                    } else {
                        $details['name'] = $driver->{"user-first-name"} . ' ' . $driver->{"user-last-name"};
                        Mail::to($driver->email)->send(new PaymentReminderMail($details));
                    }

                    \App\Models\PaymentReminder::create([
                        'driver-id' => $driver->id,
                        'type'      => $sms ? 'sms' : 'notify',
                        'amount'    => $details['amount']
                    ]);
                }
            });

        return Command::SUCCESS;
    }

    private function getLastPayDate($driverId)
    {
        $lastPayDate = FinancialDue::select('amount', 'date-of-add')
            ->where('driver-id', $driverId)
            ->orderBy('id', 'desc')
            ->first();

        return $lastPayDate?->{"date-of-add"};
    }

    private function getDues($driverId, $dates)
    {
        $totalRevenue = $this->getRevenue($driverId, $dates)['total'];

        if (!$this->subscriptionCost) {
            $subscription = Subscription::select('cost')->where('id', 4)->first();
            $this->subscriptionCost = $subscription->cost;
        }

        return ($this->subscriptionCost * $totalRevenue) / 100;
    }
}

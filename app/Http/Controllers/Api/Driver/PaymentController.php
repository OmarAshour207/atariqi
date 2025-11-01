<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Api\BaseController;

class PaymentController extends BaseController
{
    public function success()
    {
        return view('payment.success');
    }

    public function failed()
    {
        return view('payment.failed');
    }

    public function declined()
    {
        return view('payment.declined');
    }
}

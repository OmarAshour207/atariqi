<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends BaseController
{
    public function handleWebhook(Request $request)
    {
        Log::channel('payment')->info('Telr Webhook Received:', $request->all());

        return response()->json(['status' => 'success']);
    }
}

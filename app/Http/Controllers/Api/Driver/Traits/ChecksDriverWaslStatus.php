<?php

namespace App\Http\Controllers\Api\Driver\Traits;

use Illuminate\Http\JsonResponse;

trait ChecksDriverWaslStatus
{
    protected function blockIfDriverCannotOperateTrips(): ?JsonResponse
    {
        $driver = auth()->user();

        if ((int) $driver->approval === 4) {
            $reason = $driver->{'reject-reason'} ?: __('Please update your data on the Absher platform as requested by the ministry.');

            return $this->sendError('s_abshir_update_required', [
                __('Your ministry eligibility data requires an update on Absher.'),
                $reason,
            ], 403);
        }

        if ((int) $driver->approval !== 1) {
            return $this->sendError('s_userNotApproved', [
                __('We are checking your registration order, please bear with us and will send on academic email or phone'),
            ], 403);
        }

        return null;
    }
}

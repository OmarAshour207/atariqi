<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Api\BaseController;
use App\Models\DriverAnnounce;
use Symfony\Component\HttpFoundation\JsonResponse;

class AnnouncementController extends BaseController
{
    public function index(): JsonResponse
    {
        $announcements = DriverAnnounce::orderBy('id', 'desc')->get();

        return $this->sendResponse($announcements, __('Data'));
    }
}

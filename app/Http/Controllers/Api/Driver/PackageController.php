<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Driver\PackageResource;
use App\Models\Package;

class PackageController extends BaseController
{
    public function index()
    {
        $packages = Package::with('features')->get();

        return $this->sendResponse(PackageResource::collection($packages), __('Success'));
    }
}

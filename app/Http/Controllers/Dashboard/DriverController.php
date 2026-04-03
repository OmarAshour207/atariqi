<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\DriverNeighborhood;
use App\Models\DriverType;
use App\Models\Neighbour;
use App\Models\Stage;
use App\Models\University;
use App\Models\User;
use App\Services\WaslService;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    private $waslService;

    public function __construct(WaslService $waslService)
    {
        $this->waslService = $waslService;
    }

    public function index(Request $request)
    {
        $drivers = User::with('callingKey')
            ->where('user-type', 'driver')
            ->paginate(20);

        return view('dashboard.drivers.index', compact('drivers'));
    }

    public function show(User $driver)
    {
        try {
            $driver->load('callingKey', 'driverInfo', 'driverCar');
            $universities = University::all();
            $stages = Stage::all();
            $neighborhoods = Neighbour::all();
            $driverTypes = DriverType::all();
            $waslResponse = $this->waslService->checkDriverEligibility($driver->driverInfo->identity_number);
            $waslResponse = $waslResponse ? json_decode($waslResponse, true) : null;
        }
        catch (\Exception $e) {
            \Log::error('Error fetching driver details: ' . $e->getMessage());
        }

        return view('dashboard.drivers.show', compact('driver', 'universities', 'stages', 'neighborhoods', 'driverTypes', 'waslResponse'));
    }

    public function edit(User $driver)
    {
        $universities = University::all();
        $stages = Stage::all();
        $neighborhoods = Neighbour::all();
        $driverTypes = DriverType::all();
        $driver->load(['driverInfo', 'driverCar']);
        return view('dashboard.drivers.edit', compact('driver', 'universities', 'stages', 'neighborhoods', 'driverTypes'));
    }

    public function update(User $driver, Request $request)
    {
        $request->validate([
            'user-first-name' => 'required|string|max:255',
            'user-last-name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $driver->id,
            'phone-no' => 'required|string|max:20',
        ]);

        $driver->update($request->validated());

        return redirect()->route('drivers.index')->with('success', 'Driver updated successfully.');
    }

    public function updateStatus(User $driver, Request $request)
    {
        $request->validate([
            'approval' => 'required:in:0,1,2',
        ]);

        $driver->update(['approval' => $request->approval]);

        return redirect()->route('drivers.index')->with('success', 'Driver status updated successfully.');
    }
}

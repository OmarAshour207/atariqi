<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\DriverNeighborhood;
use App\Models\DriverType;
use App\Models\Neighbour;
use App\Models\Stage;
use App\Models\University;
use App\Models\User;
use App\Models\Package;
use App\Models\UserPackage;
use App\Models\UserPackageHistory;
use App\Services\WaslService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

    public function packages(Request $request)
    {
        $drivers = User::with(['activePackage', 'packages.package'])
            ->where('user-type', 'driver')
            ->paginate(20);

        $packages = Package::where('status', '!=', Package::SOON)->get();

        return view('dashboard.drivers.packages', compact('drivers', 'packages'));
    }

    public function packagePlans(User $driver)
    {
        $driver->load(['packages.package']);
        $packages = Package::where('status', '!=', Package::SOON)->get();

        return view('dashboard.drivers.package-plans', compact('driver', 'packages'));
    }

    public function assignPackage(User $driver, Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'interval' => 'required|in:monthly,yearly',
        ]);

        $package = Package::find($request->package_id);

        if (!$package || $package->status == Package::SOON) {
            return back()->with('error', __('This package is not available for assignment.'));
        }

        DB::transaction(function () use ($driver, $package, $request) {
            $activeUserPackage = UserPackage::where('user_id', $driver->id)
                ->where('status', UserPackage::STATUS_ACTIVE)
                ->where('end_date', '>=', now())
                ->first();

            if ($activeUserPackage) {
                if ($activeUserPackage->package_id == $package->id && $activeUserPackage->interval == $request->interval) {
                    throw new \Exception(__('User already on this package option.'));
                }

                UserPackageHistory::create([
                    'user_id' => $driver->id,
                    'package_id' => $activeUserPackage->package_id,
                    'status' => $activeUserPackage->status,
                    'start_date' => $activeUserPackage->start_date,
                    'end_date' => $activeUserPackage->end_date,
                    'interval' => $activeUserPackage->interval,
                ]);

                $activeUserPackage->delete();
            }

            $duration = $request->interval === 'monthly' ? Carbon::now()->addMonth() : Carbon::now()->addYear();

            UserPackage::create([
                'user_id' => $driver->id,
                'package_id' => $package->id,
                'interval' => $request->interval,
                'start_date' => now(),
                'end_date' => $duration,
                'status' => UserPackage::STATUS_ACTIVE,
            ]);
        });

        return redirect()->route('drivers.packages')->with('success', __('Package assignment updated successfully.'));
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

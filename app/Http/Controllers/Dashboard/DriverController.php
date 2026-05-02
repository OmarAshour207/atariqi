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
use App\Models\DelDailyInfo;
use App\Models\DelWeekInfo;
use App\Models\DelImmediateInfo;
use App\Models\CaptainRequestDecision;
use App\Models\SugDayDriver;
use App\Models\SugWeekDriver;
use App\Models\SuggestionDriver;
use App\Models\FinancialDue;
use App\Services\WaslService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Mail\DriverRejectedMail;
use App\Mail\PaymentReminderMail;
use Illuminate\Support\Facades\Mail;

class DriverController extends Controller
{
    private $waslService;

    public function __construct(WaslService $waslService)
    {
        $this->waslService = $waslService;
    }

    public function index(Request $request)
    {
        $query = User::with(['callingKey', 'university', 'stage'])
            ->where('user-type', 'driver');

        // Filter by name
        if ($request->filled('name')) {
            $query->where(function ($q) use ($request) {
                $q->where('user-first-name', 'like', '%' . $request->name . '%')
                  ->orWhere('user-last-name', 'like', '%' . $request->name . '%')
                  ->orWhere('email', 'like', '%' . $request->name . '%');
            });
        }

        // Filter by email
        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        // Filter by phone
        if ($request->filled('phone')) {
            $query->where('phone-no', 'like', '%' . $request->phone . '%');
        }

        // Filter by approval
        if ($request->filled('approval') && $request->approval !== '') {
            $query->where('approval', $request->approval);
        }

        $drivers = $query->paginate(20)->appends($request->query());

        return view('dashboard.drivers.index', compact('drivers'));
    }

    public function newDrivers(Request $request)
    {
        $query = User::with(['callingKey', 'university', 'stage'])
            ->where('user-type', 'driver')
            ->where('approval', 0);

        // Filter by name
        if ($request->filled('name')) {
            $query->where(function ($q) use ($request) {
                $q->where('user-first-name', 'like', '%' . $request->name . '%')
                  ->orWhere('user-last-name', 'like', '%' . $request->name . '%')
                  ->orWhere('email', 'like', '%' . $request->name . '%');
            });
        }

        // Filter by email
        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        // Filter by phone
        if ($request->filled('phone')) {
            $query->where('phone-no', 'like', '%' . $request->phone . '%');
        }

        $drivers = $query->paginate(20)->appends($request->query());

        return view('dashboard.drivers.new_drivers', compact('drivers'));
    }

    public function show(User $driver)
    {
        $waslResponse = '';
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

    public function driverRates(Request $request)
    {
        // Get all drivers
        $drivers = User::where('user-type', 'driver')
            ->with(['callingKey'])
            ->paginate(20);

        // Calculate ratings for each driver
        $drivers->getCollection()->transform(function ($driver) {
            // Get daily delivery ratings
            $dailyRatings = DelDailyInfo::whereHas('ride', function($query) use ($driver) {
                $query->where('driver-id', $driver->id);
            })->whereNotNull('passenger-rate')->pluck('passenger-rate')->toArray();

            // Get weekly delivery ratings
            $weeklyRatings = DelWeekInfo::whereHas('ride', function($query) use ($driver) {
                $query->where('driver-id', $driver->id);
            })->whereNotNull('passenger-rate')->pluck('passenger-rate')->toArray();

            // Get immediate delivery ratings
            $immediateRatings = DelImmediateInfo::whereHas('ride', function($query) use ($driver) {
                $query->where('driver-id', $driver->id);
            })->whereNotNull('passenger-rate')->pluck('passenger-rate')->toArray();

            // Combine all ratings
            $allRatings = array_merge($dailyRatings, $weeklyRatings, $immediateRatings);

            // Calculate statistics
            $driver->total_ratings = count($allRatings);
            $driver->average_rating = $allRatings ? round(array_sum($allRatings) / count($allRatings), 1) : null;
            $driver->rating_breakdown = [
                'daily' => count($dailyRatings),
                'weekly' => count($weeklyRatings),
                'immediate' => count($immediateRatings)
            ];

            return $driver;
        });

        return view('dashboard.drivers.rates', compact('drivers'));
    }

    public function trips(Request $request)
    {
        $driverId = $request->get('driver_id');
        $tripType = $request->get('trip_type');

        // Build query based on filters
        $trips = collect();

        if (!$tripType || $tripType === 'immediate') {
            $query = SuggestionDriver::with(['driver', 'passenger', 'deliveryInfo', 'booking']);

            if ($driverId) {
                $query->where('driver-id', $driverId);
            }

            $immediateTrips = $query->get()->map(function ($trip) {
                $trip->trip_type = 'immediate';
                $trip->revenue = $trip->deliveryInfo ? $trip->deliveryInfo->passenger_rate : 0;
                $trip->sort_date = $trip->{'date-of-add'};
                return $trip;
            });

            $trips = $trips->concat($immediateTrips);
        }

        if (!$tripType || $tripType === 'daily') {
            $query = SugDayDriver::with(['driver', 'passenger', 'deliveryInfo', 'booking']);

            if ($driverId) {
                $query->where('driver-id', $driverId);
            }

            $dailyTrips = $query->get()->map(function ($trip) {
                $trip->trip_type = 'daily';
                $trip->revenue = $trip->deliveryInfo ? $trip->deliveryInfo->passenger_rate : 0;
                $trip->sort_date = $trip->{'date-of-add'};
                return $trip;
            });

            $trips = $trips->concat($dailyTrips);
        }

        if (!$tripType || $tripType === 'weekly') {
            $query = SugWeekDriver::with(['driver', 'passenger', 'deliveryInfo', 'booking']);

            if ($driverId) {
                $query->where('driver-id', $driverId);
            }

            $weeklyTrips = $query->get()->map(function ($trip) {
                $trip->trip_type = 'weekly';
                $trip->revenue = $trip->deliveryInfo ? $trip->deliveryInfo->passenger_rate : 0;
                $trip->sort_date = $trip->{'date-of-add'};
                return $trip;
            });

            $trips = $trips->concat($weeklyTrips);
        }

        // Sort by date and paginate
        $sortedTrips = $trips->sortByDesc('sort_date')->values();
        $perPage = 20;
        $currentPage = $request->get('page', 1);
        $paginatedTrips = new \Illuminate\Pagination\LengthAwarePaginator(
            $sortedTrips->forPage($currentPage, $perPage),
            $sortedTrips->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'pageName' => 'page']
        );

        // Preserve query parameters in pagination links
        if ($driverId) {
            $paginatedTrips->appends('driver_id', $driverId);
        }
        if ($tripType) {
            $paginatedTrips->appends('trip_type', $tripType);
        }

        // Get all drivers for filter dropdown
        $drivers = User::where('user-type', 'driver')->select('id', 'user-first-name', 'user-last-name')->get();

        return view('dashboard.drivers.trips', compact('paginatedTrips', 'drivers', 'driverId', 'tripType'));
    }

    public function sendPaymentReminder(Request $request, User $driver)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        // Create or update financial due record
        FinancialDue::updateOrCreate(
            ['driver-id' => $driver->id],
            [
                'amount' => $request->amount,
                'date-of-add' => now()
            ]
        );

        // Send reminder email
        $details = [
            'name' => $driver->{'user-first-name'} . ' ' . $driver->{'user-last-name'},
            'amount' => $request->amount
        ];

        Mail::to($driver->email)->send(new PaymentReminderMail($details));

        return redirect()->back()->with('success', __('Payment reminder sent successfully to driver.'));
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
            'approval' => ['required', 'in:0,1,2'],
            'reject-reason' => ['required_if:approval,2', 'nullable', 'string', 'max:1000'],
        ]);

        $oldApproval = $driver->approval;
        $newApproval = (int) $request->approval;

        $updateData = [
            'approval' => $newApproval,
            'reject-reason' => $newApproval === 2 ? $request->input('reject-reason') : null,
        ];

        $driver->update($updateData);

        if (in_array($newApproval, [1, 2], true) && $newApproval !== $oldApproval) {
            $employeeId = auth()->guard('admin')->id();

            CaptainRequestDecision::create([
                'user_id' => $driver->id,
                'action_type' => $newApproval === 1 ? 'approved' : 'rejected',
                'old_approval' => $oldApproval,
                'new_approval' => $newApproval,
                'reasondecided_by_employee_id' => $employeeId,
                'reject_reason' => $newApproval === 2 ? $request->input('reject-reason') : null,
            ]);
        }

        if ($newApproval === 2) {
            Mail::to($driver->email)->send(new DriverRejectedMail($driver, $request->input('reject-reason')));
        }

        return redirect()->route('drivers.index')->with('success', 'Driver status updated successfully.');
    }
}

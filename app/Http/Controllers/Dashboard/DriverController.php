<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Driver\Traits\Payment;
use App\Models\DriverNeighborhood;
use App\Models\DriverType;
use App\Models\Neighbour;
use App\Models\PlatformEmailLog;
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
use App\Models\CaptianRequestAssignment;
use App\Models\EmployeePackageLog;
use App\Mail\PackageAssignmentMail;
use App\Mail\DriverRequestAssignmentMail;
use App\Mail\PackageCancellationMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Models\SugDayDriver;
use App\Models\SugWeekDriver;
use App\Models\SuggestionDriver;
use App\Models\FinancialDue;
use App\Models\Subscription;
use App\Services\WaslService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Mail\DriverRejectedMail;
use App\Mail\DriverApprovedMail;
use App\Mail\PaymentReminderMail;
use App\Models\DriverBanned;
use App\Models\Admin;
use App\Models\NewUserInfo;

class DriverController extends Controller
{
    use Payment;

    private $waslService;

    public function __construct(WaslService $waslService)
    {
        $this->waslService = $waslService;
    }

    public function index(Request $request)
    {
        $query = User::with(['callingKey', 'university', 'stage', 'driverInfo'])
            ->where('user-type', 'driver');

        $this->applyDriverIndexFilters($query, $request);

        $sort = $request->get('sort', 'newest');
        $requiresDuesCalculation = $request->filled('min_dues')
            || in_array($sort, ['highest_dues', 'lowest_dues'], true);

        if (!$requiresDuesCalculation) {
            $this->applyDriverIndexSort($query, $sort);
            $drivers = $query->paginate(20)->appends($request->query());
            $this->attachCurrentDuesToDrivers($drivers->getCollection());
        } else {
            $driversCollection = $query->get();
            $this->attachCurrentDuesToDrivers($driversCollection);

            if ($request->filled('min_dues')) {
                $minDues = (float) $request->min_dues;
                $driversCollection = $driversCollection->filter(
                    fn ($driver) => ($driver->current_dues ?? 0) >= $minDues
                );
            }

            $driversCollection = $this->sortDriversCollection($driversCollection, $sort);

            $page = LengthAwarePaginator::resolveCurrentPage();
            $perPage = 20;
            $drivers = new LengthAwarePaginator(
                $driversCollection->forPage($page, $perPage)->values(),
                $driversCollection->count(),
                $perPage,
                $page,
                [
                    'path' => LengthAwarePaginator::resolveCurrentPath(),
                    'query' => $request->query(),
                ]
            );
        }

        $universities = University::all();
        $stages = Stage::all();

        return view('dashboard.drivers.index', compact('drivers', 'universities', 'stages'));
    }

    private function applyDriverIndexFilters($query, Request $request): void
    {
        if ($request->filled('name')) {
            $query->where(function ($q) use ($request) {
                $q->where('user-first-name', 'like', '%' . $request->name . '%')
                    ->orWhere('user-last-name', 'like', '%' . $request->name . '%')
                    ->orWhere('email', 'like', '%' . $request->name . '%');
            });
        }

        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        if ($request->filled('phone')) {
            $query->where('phone-no', 'like', '%' . $request->phone . '%');
        }

        if ($request->filled('approval') && $request->approval !== '') {
            $query->where('approval', $request->approval);
        }

        if ($request->filled('university-id')) {
            $query->where('university-id', $request->get('university-id'));
        }

        if ($request->filled('user-stage-id')) {
            $query->where('user-stage-id', $request->get('user-stage-id'));
        }

        if ($request->filled('min_rate') || $request->filled('max_rate')) {
            $query->whereHas('driverInfo', function ($q) use ($request) {
                if ($request->filled('min_rate')) {
                    $q->where('driver-rate', '>=', (float) $request->min_rate);
                }
                if ($request->filled('max_rate')) {
                    $q->where('driver-rate', '<=', (float) $request->max_rate);
                }
            });
        }
    }

    private function applyDriverIndexSort($query, string $sort): void
    {
        switch ($sort) {
            case 'oldest':
                $query->orderBy('date-of-add')->orderBy('id');
                break;
            case 'highest_rate':
                $query->leftJoin('driver-info', 'driver-info.driver-id', '=', 'users.id')
                    ->orderByDesc('driver-info.driver-rate')
                    ->select('users.*');
                break;
            case 'lowest_rate':
                $query->leftJoin('driver-info', 'driver-info.driver-id', '=', 'users.id')
                    ->orderByRaw('`driver-info`.`driver-rate` IS NULL, `driver-info`.`driver-rate` ASC')
                    ->select('users.*');
                break;
            case 'newest':
            default:
                $query->orderByDesc('id');
                break;
        }
    }

    private function sortDriversCollection($drivers, string $sort)
    {
        return match ($sort) {
            'highest_dues' => $drivers->sortByDesc(fn ($driver) => $driver->current_dues ?? 0)->values(),
            'lowest_dues' => $drivers->sortBy(fn ($driver) => $driver->current_dues ?? 0)->values(),
            'highest_rate' => $drivers->sortByDesc(fn ($driver) => $driver->driverInfo?->{'driver-rate'} ?? -1)->values(),
            'lowest_rate' => $drivers->sortBy(fn ($driver) => $driver->driverInfo?->{'driver-rate'} ?? PHP_FLOAT_MAX)->values(),
            'oldest' => $drivers->sortBy(fn ($driver) => $driver->{'date-of-add'} ?? $driver->id)->values(),
            default => $drivers->sortByDesc('id')->values(),
        };
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

        // Filter by gender
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // Filter by university-id
        if ($request->filled('university-id')) {
            $query->where('university-id', $request->get('university-id'));
        }

        // Filter by user-stage-id
        if ($request->filled('user-stage-id')) {
            $query->where('user-stage-id', $request->get('user-stage-id'));
        }

        $drivers = $query->paginate(20)->appends($request->query());

        $universities = University::all();
        $stages = Stage::all();

        return view('dashboard.drivers.new_drivers', compact('drivers', 'universities', 'stages'));
    }

    public function show(User $driver)
    {
        if ($driver->{'user-type'} !== 'driver') {
            return redirect()->route('drivers.index')->with('error', __('Invalid driver.'));
        }

        $waslResponse = null;
        $waslEligibility = [
            'is_valid' => null,
            'api_error' => false,
            'reasons' => [],
            'message' => __('Unknown'),
            'display_status' => __('Unknown'),
            'driver_eligibility' => null,
            'vehicle_eligibility' => null,
            'driver_expiry_date' => null,
            'vehicle_plate' => null,
            'vehicle_expiry_date' => null,
            'vehicles' => [],
        ];
        $banned = null;
        $admins = collect();
        $universities = University::all();
        $stages = Stage::all();
        $neighborhoods = Neighbour::all();
        $driverTypes = DriverType::all();

        $driver->load('callingKey', 'university', 'stage', 'driverInfo', 'driverCar', 'driverNeighborhood', 'newUserInfo');

        $currentDues = round($this->calculateCurrentDues($driver), 2);

        $currentUserPackage = UserPackage::with('package')
            ->where('user_id', $driver->id)
            ->where('status', UserPackage::STATUS_ACTIVE)
            ->where('end_date', '>=', now())
            ->latest()
            ->first()
            ?? UserPackage::with('package')->where('user_id', $driver->id)->latest()->first();

        $neighborhoodFromNames = $this->resolveNeighborhoodNames(
            $driver->driverNeighborhood?->{'neighborhoods-from'},
            $neighborhoods
        );

        $neighborhoodToNames = $this->resolveNeighborhoodNames(
            $driver->driverNeighborhood?->{'neighborhoods-to'},
            $neighborhoods
        );

        $driverNeighborhoodName = $this->resolveDriverNeighborhoodName(
            $driver->driverInfo?->{'driver-neighborhood'},
            $neighborhoods
        );

        $hasPendingUpdate = $driver->approval == 2
            || NewUserInfo::where('user-id', $driver->id)->exists();

        if ($driver->driverInfo && filled($driver->driverInfo->identity_number)) {
            $banned = DriverBanned::where('driver_identity', $driver->driverInfo->identity_number)
                ->latest()
                ->first();

            try {
                $eligibilityBody = $this->waslService->buildEligibilityRequestBody($driver);

                if ($eligibilityBody !== null) {
                    $rawWaslResponse = $this->waslService->checkDriverEligibility(
                        $driver->driverInfo->identity_number,
                        $eligibilityBody
                    );
                    $waslEligibility = $this->waslService->parseEligibilityResponse(
                        $rawWaslResponse,
                        $driver->driverInfo->identity_number
                    );
                    $waslResponse = $this->waslService->extractEligibilityItem(
                        $rawWaslResponse,
                        $driver->driverInfo->identity_number
                    );
                }
            } catch (\Exception $e) {
                \Log::error('Error fetching Wasl driver details: ' . $e->getMessage());
                $waslEligibility = array_merge($waslEligibility, [
                    'is_valid' => false,
                    'api_error' => true,
                    'message' => $e->getMessage(),
                    'display_status' => __('Verification Failed'),
                ]);
            }
        }

        try {
            $admins = Admin::where('type', 'admin')->get();
        } catch (\Exception $e) {
            \Log::error('Error fetching admins: ' . $e->getMessage());
        }

        return view('dashboard.drivers.show', compact(
            'driver',
            'universities',
            'stages',
            'neighborhoods',
            'driverTypes',
            'waslResponse',
            'waslEligibility',
            'banned',
            'admins',
            'currentDues',
            'currentUserPackage',
            'neighborhoodFromNames',
            'neighborhoodToNames',
            'driverNeighborhoodName',
            'hasPendingUpdate'
        ));
    }

    public function driverTrips(User $driver)
    {
        if ($driver->{'user-type'} !== 'driver') {
            return redirect()->route('drivers.index')->with('error', __('Invalid driver.'));
        }

        $tripRelations = ['passenger', 'booking', 'booking.university', 'booking.neighborhood', 'booking.service'];

        $immediateTrips = SuggestionDriver::where('driver-id', $driver->id)
            ->with($tripRelations)
            ->orderBy('date-of-add', 'desc')
            ->get();

        $dailyTrips = SugDayDriver::where('driver-id', $driver->id)
            ->with($tripRelations)
            ->orderBy('date-of-add', 'desc')
            ->get();

        $weeklyTrips = SugWeekDriver::where('driver-id', $driver->id)
            ->with($tripRelations)
            ->orderBy('date-of-add', 'desc')
            ->get();

        return view('dashboard.drivers.driver_trips', compact(
            'driver',
            'immediateTrips',
            'dailyTrips',
            'weeklyTrips'
        ));
    }

    public function driverEarnings(User $driver)
    {
        if ($driver->{'user-type'} !== 'driver') {
            return redirect()->route('drivers.index')->with('error', __('Invalid driver.'));
        }

        $lifetimeDates = [
            'start_date' => $driver->{'date-of-add'},
            'end_date' => Carbon::now()->format('Y-m-d'),
        ];

        $revenueBreakdown = $this->getDetailedRevenue($driver->id, $lifetimeDates);
        $duesPercentage = Subscription::generalDuesPercentageValue();

        $totalDues = round(($duesPercentage * $revenueBreakdown['total']) / 100, 2);
        $totalPaid = round((float) FinancialDue::where('driver-id', $driver->id)->sum('amount'), 2);
        $remainingDues = round(max(0, $totalDues - $totalPaid), 2);
        $currentUnpaidDues = round($this->calculateCurrentDues($driver), 2);

        $payments = FinancialDue::where('driver-id', $driver->id)
            ->orderByDesc('id')
            ->get();

        return view('dashboard.drivers.driver_earnings', compact(
            'driver',
            'revenueBreakdown',
            'duesPercentage',
            'totalDues',
            'totalPaid',
            'remainingDues',
            'currentUnpaidDues',
            'payments'
        ));
    }

    private function resolveNeighborhoodNames(?string $idsString, $neighborhoods): string
    {
        if (empty($idsString)) {
            return '-';
        }

        $ids = array_filter(array_map('trim', explode('|', $idsString)));
        $nameKey = app()->getLocale() === 'en' ? 'neighborhood-en' : 'neighborhood-ar';

        $names = $neighborhoods->whereIn('id', $ids)->pluck($nameKey)->filter();

        return $names->isNotEmpty() ? $names->implode(' | ') : '-';
    }

    private function resolveDriverNeighborhoodName($value, $neighborhoods): string
    {
        if (empty($value)) {
            return '-';
        }

        if (is_numeric($value)) {
            $neighborhood = $neighborhoods->firstWhere('id', (int) $value);
            $nameKey = app()->getLocale() === 'en' ? 'neighborhood-en' : 'neighborhood-ar';

            return $neighborhood?->{$nameKey} ?? (string) $value;
        }

        return (string) $value;
    }

    public function packages(Request $request)
    {
        $drivers = User::with(['activePackage', 'packages.package'])
            ->whereIn('approval', [1, 2])
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
                    return redirect()->back()->with('error', __('User already has this package assigned with the same interval.'));
                }

                UserPackageHistory::create([
                    'user_id' => $driver->id,
                    'package_id' => $activeUserPackage->package_id,
                    'status' => $activeUserPackage->status,
                    'start_date' => $activeUserPackage->start_date,
                    'end_date' => $activeUserPackage->end_date,
                    'interval' => $activeUserPackage->interval ?? 'monthly',
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

        EmployeePackageLog::create([
            'assigned_from_employee_id' => auth()->guard('admin')->id(),
            'driver_id' => $driver->id,
            'id_package_old' => $driver->activePackage?->package_id,
            'id_package_new' => $package->id,
            'status' => 'assigned',
        ]);

        // Send email notification to driver
        $this->sendPackageAssignmentEmail($driver, $package, $request->interval);

        return redirect()->route('drivers.packages')->with('success', __('Package assignment updated successfully.'));
    }

    private function sendPackageAssignmentEmail(User $driver, Package $package, string $interval): void
    {
        if (!$driver->email) {
            $this->logPlatformEmail($driver, 'package_assignment', 'failed', __('Driver email is not available.'));

            return;
        }

        try {
            Mail::to($driver->email)->send(new PackageAssignmentMail($driver, $package, $interval));
            $this->logPlatformEmail($driver, 'package_assignment', 'sent');
        } catch (\Throwable $e) {
            \Log::error('Package assignment email failed', [
                'driver_id' => $driver->id,
                'error' => $e->getMessage(),
            ]);
            $this->logPlatformEmail($driver, 'package_assignment', 'failed', $e->getMessage());
        }
    }

    private function logPlatformEmail(User $driver, string $emailType, string $status, ?string $errorMessage = null): void
    {
        try {
            PlatformEmailLog::create([
                'assigned_from_employee_id' => auth()->guard('admin')->id(),
                'driver_id' => $driver->id,
                'driver_email' => $driver->email,
                'email_type' => $emailType,
                'status' => $status,
                'error_message' => $errorMessage,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Failed to write platform email log', [
                'driver_id' => $driver->id,
                'email_type' => $emailType,
                'status' => $status,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function cancelPackage(User $driver)
    {
        if ($driver->{'user-type'} !== 'driver') {
            return redirect()->back()->with('error', __('Invalid driver.'));
        }

        if (!$driver->email) {
            return redirect()->back()->with('error', __('Driver email is not available.'));
        }

        $activeUserPackage = UserPackage::where('user_id', $driver->id)
            ->where('status', UserPackage::STATUS_ACTIVE)
            ->where('end_date', '>=', now())
            ->latest('id')
            ->first();

        if (!$activeUserPackage) {
            return redirect()->back()->with('error', __('No active subscription found for this driver.'));
        }

        $freePackage = Package::where('status', Package::FREE)->first();

        if (!$freePackage) {
            return redirect()->back()->with('error', __('Free package is not configured.'));
        }

        if ($activeUserPackage->package_id == $freePackage->id) {
            return redirect()->back()->with('error', __('Driver is already on the free subscription.'));
        }

        $oldPackageId = $activeUserPackage->package_id;
        $oldPackage = Package::find($oldPackageId);

        DB::transaction(function () use ($driver, $activeUserPackage, $freePackage) {
            UserPackageHistory::create([
                'user_id' => $driver->id,
                'package_id' => $activeUserPackage->package_id,
                'status' => UserPackage::STATUS_CANCELLED,
                'start_date' => $activeUserPackage->start_date,
                'end_date' => $activeUserPackage->end_date,
                'canceled_date' => now(),
                'interval' => $activeUserPackage->interval ?? 'monthly',
            ]);

            $activeUserPackage->delete();

            UserPackage::create([
                'package_id' => $freePackage->id,
                'user_id' => $driver->id,
                'start_date' => now(),
                'end_date' => now()->addYear(),
                'status' => UserPackage::STATUS_ACTIVE,
                'interval' => 'yearly',
            ]);
        });

        EmployeePackageLog::create([
            'assigned_from_employee_id' => auth()->guard('admin')->id(),
            'driver_id' => $driver->id,
            'id_package_old' => $oldPackageId,
            'id_package_new' => $freePackage->id,
            'status' => 'cancelled',
        ]);

        try {
            Mail::to($driver->email)->send(new PackageCancellationMail($driver, $oldPackage, $freePackage));
            $this->logPlatformEmail($driver, 'package_cancellation', 'sent');
        } catch (\Throwable $e) {
            \Log::error('Package cancellation email failed', [
                'driver_id' => $driver->id,
                'error' => $e->getMessage(),
            ]);
            $this->logPlatformEmail($driver, 'package_cancellation', 'failed', $e->getMessage());

            return redirect()->back()
                ->with('error', __('Subscription was cancelled but the notification email could not be sent.'));
        }

        return redirect()->back()->with('success', __('Subscription cancelled and driver moved to free plan successfully.'));
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
            $query = SuggestionDriver::with(['driver', 'passenger', 'deliveryInfo', 'booking', 'booking.university', 'booking.neighborhood', 'booking.service']);

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
            $query = SugDayDriver::with(['driver', 'passenger', 'deliveryInfo', 'booking', 'booking.university', 'booking.neighborhood', 'booking.service']);

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
            $query = SugWeekDriver::with(['driver', 'passenger', 'deliveryInfo', 'booking', 'booking.university', 'booking.neighborhood', 'booking.service']);

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
        if ($driver->{'user-type'} !== 'driver') {
            return redirect()->back()->with('error', __('Invalid driver.'));
        }

        if (empty($driver->email)) {
            return redirect()->back()->with('error', __('Driver email is not available.'));
        }

        try {
            $amount = $this->calculateCurrentDues($driver);

            if ($amount <= 50) {
                return redirect()->back()
                    ->with('error', __('A reminder cannot be sent because dues do not exceed 50 SAR.'));
            }

            $details = [
                'name' => $driver->{'user-first-name'} . ' ' . $driver->{'user-last-name'},
                'amount' => round($amount, 2),
            ];

            Mail::to($driver->email)->send(new PaymentReminderMail($details));

            PlatformEmailLog::create([
                'assigned_from_employee_id' => auth()->guard('admin')->id(),
                'driver_id' => $driver->id,
                'driver_email' => $driver->email,
                'email_type' => 'dues_reminder',
                'status' => 'sent',
                'error_message' => null,
            ]);

            return redirect()->back()
                ->with('success', __('Payment reminder sent successfully to driver.'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Unable to send payment reminder.'));
        }
    }

    private function attachCurrentDuesToDrivers($drivers): void
    {
        if ($drivers->isEmpty()) {
            return;
        }

        $lastPayDates = FinancialDue::whereIn('driver-id', $drivers->pluck('id'))
            ->orderByDesc('id')
            ->get()
            ->unique('driver-id')
            ->keyBy('driver-id');

        $drivers->transform(function ($driver) use ($lastPayDates) {
            $driver->current_dues = round(
                $this->calculateCurrentDues($driver, $lastPayDates->get($driver->id)),
                2
            );

            return $driver;
        });
    }

    private function calculateCurrentDues(User $driver, ?FinancialDue $lastPayDate = null): float
    {
        if ($lastPayDate === null) {
            $lastPayDate = FinancialDue::where('driver-id', $driver->id)
                ->orderByDesc('id')
                ->first();
        }

        $dates = [
            'start_date' => $lastPayDate?->{'date-of-add'} ?? $driver->{'date-of-add'},
            'end_date' => Carbon::now()->format('Y-m-d'),
        ];

        $duesPercentage = Subscription::generalDuesPercentageValue();
        $revenue = $this->getRevenue($driver->id, $dates);

        return ($duesPercentage * $revenue['total']) / 100;
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
            'approval' => ['required', 'in:1,2,3'],
            'reject-reason' => ['required_if:approval,3', 'nullable', 'string', 'max:1000'],
        ]);

        $oldApproval = $driver->approval;
        $newApproval = (int) $request->approval;

        if ($newApproval === 1 && (int) $oldApproval === 0) {
            if (!filled($driver->driverInfo?->identity_number)) {
                return redirect()->back()->with('error', __('Unable to verify ministry eligibility because the driver identity number is missing.'));
            }

            try {
                $driver->loadMissing(['driverInfo', 'callingKey']);
                $body = $this->waslService->buildEligibilityRequestBody($driver);

                if ($body === null) {
                    return redirect()->back()->with('error', __('Unable to verify ministry eligibility because the driver identity number is missing.'));
                }

                $response = $this->waslService->checkDriverEligibility($driver->driverInfo->identity_number, $body);
                $parsed = $this->waslService->parseEligibilityResponse($response, $driver->driverInfo->identity_number);

                if ($parsed['api_error'] ?? false) {
                    return redirect()->back()->with('error', __('Unable to verify ministry eligibility. Please try again.') . ' ' . $parsed['message']);
                }

                if ($parsed['is_valid'] === false) {
                    return redirect()->back()->with('error', __('Cannot approve this driver because ministry eligibility is not valid.') . ' ' . $parsed['message']);
                }
            } catch (\Exception $e) {
                return redirect()->back()->with('error', __('Unable to verify ministry eligibility. Please try again.'));
            }
        }

        $updateData = [
            'approval' => $newApproval,
            'reject-reason' => $newApproval === 3 ? $request->input('reject-reason') : null,
        ];

        if($request->input('approval') == 3) {
            DriverBanned::create([
                'assigned_by_employee_id' => auth()->guard('admin')->id(),
                'driver_identity' => $driver->driverInfo->identity_number,
                'driver_no' => $driver->{"phone-no"},
                'driver_car_no' => $driver->driverInfo->{"car-number"} ?? null,
                'note' => $request->input('reject-reason'),
            ]);
        }

        $driver->update($updateData);

        if (in_array($newApproval, [1, 3], true) && $newApproval !== $oldApproval) {
            $employeeId = auth()->guard('admin')->id();

            CaptainRequestDecision::create([
                'user_id' => $driver->id,
                'action_type' => $newApproval === 1 ? 'approved' : 'rejected',
                'old_approval' => $oldApproval,
                'new_approval' => $newApproval,
                'decided_by_employee_id' => $employeeId,
                'reject_reason' => $newApproval === 3 ? $request->input('reject-reason') : null,
            ]);
        }

        if ($newApproval === 3) {
            Mail::to($driver->email)->send(new DriverRejectedMail($driver, $request->input('reject-reason')));
            PlatformEmailLog::create([
                'assigned_from_employee_id' => auth()->guard('admin')->id(),
                'driver_id' => $driver->id,
                'driver_email' => $driver->email,
                'email_type' => 'driver_banned',
                'status' => 'sent',
                'error_message' => null,
            ]);
        }

        if ($newApproval === 1 && (int) $oldApproval === 0) {
            $this->sendCaptainApprovalEmail($driver);
        }

        return redirect()->route('new-drivers.index')->with('success', __('Driver status updated successfully.'));
    }

    private function sendCaptainApprovalEmail(User $driver): void
    {
        if (!$driver->email) {
            return;
        }

        try {
            Mail::to($driver->email)->send(new DriverApprovedMail($driver));

            PlatformEmailLog::create([
                'assigned_from_employee_id' => auth()->guard('admin')->id(),
                'driver_id' => $driver->id,
                'driver_email' => $driver->email,
                'email_type' => 'captain_approved',
                'status' => 'sent',
                'error_message' => null,
            ]);
        } catch (\Throwable $e) {
            PlatformEmailLog::create([
                'assigned_from_employee_id' => auth()->guard('admin')->id(),
                'driver_id' => $driver->id,
                'driver_email' => $driver->email,
                'email_type' => 'captain_approved',
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }

    public function assignToAdmin(User $driver, Request $request)
    {
        $request->validate([
            'assign_note' => ['required', 'string', 'max:1000'],
            'assigned_admin' => ['required', 'exists:admins,id'],
        ]);

        $assignedAdmin = Admin::findOrFail($request->input('assigned_admin'));
        $assignedBy = auth()->guard('admin')->user();

        CaptianRequestAssignment::create([
            'user_id' => $driver->id,
            'assigned_from_employee_id' => $assignedBy->id,
            'assigned_to_employee_id' => $assignedAdmin->id,
            'note' => $request->input('assign_note'),
            'status' => 'assigned',
        ]);

        try {
            if (!$assignedAdmin->email) {
                throw new \RuntimeException(__('The selected admin does not have an email address.'));
            }

            Mail::to($assignedAdmin->email)->send(
                new DriverRequestAssignmentMail($assignedAdmin, $driver, $request->input('assign_note'), $assignedBy)
            );

            return redirect()->back()->with('success', __('Driver assignment submitted successfully.'));
        } catch (\Throwable $e) {
            \Log::error('Driver assignment email failed', [
                'driver_id' => $driver->id,
                'assigned_admin_id' => $assignedAdmin->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('warning', __('Driver assignment was saved successfully, but the assignment email could not be sent. Please send the assignment email manually to :admin.', [
                'admin' => $assignedAdmin->email
                    ? $assignedAdmin->name . ' (' . $assignedAdmin->email . ')'
                    : $assignedAdmin->name,
            ]));
        }
    }

    public function ban(User $driver, Request $request)
    {
        $request->validate([
            'ban_reason' => ['required', 'string', 'max:1000'],
        ]);

        $driverRate = $driver->driverInfo?->{"driver-rate"};
        if ($driverRate === null || $driverRate >= 1) {
            return redirect()->back()->with('error', __('Driver cannot be banned. Rating is not below 1.'));
        }

        DriverBanned::create([
            'assigned_from_employee_id' => auth()->guard('admin')->id(),
            'driver_identity' => $driver->driverInfo->identity_number,
            'driver_no' => $driver->{"phone-no"},
            'driver_car_no' => $driver->driverInfo->{"car-number"} ?? null,
            'note' => $request->input('ban_reason'),
        ]);

        $driver->update(['approval' => 3, 'reject-reason' => $request->input('ban_reason')]);

        CaptainRequestDecision::create([
            'user_id' => $driver->id,
            'action_type' => 'banned',
            'old_approval' => $driver->approval,
            'new_approval' => 3,
            'decided_by_employee_id' => auth()->guard('admin')->id(),
            'reject_reason' => $request->input('ban_reason'),
        ]);

        return redirect()->route('drivers.index')->with('success', __('Driver has been banned successfully.'));
    }
}

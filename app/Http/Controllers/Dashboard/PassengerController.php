<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PassengerRate;
use App\Models\University;
use App\Models\Stage;
use App\Models\NewUserInfo;

class PassengerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['callingKey', 'university', 'stage', 'passengerRate'])
            ->where('user-type', 'passenger')
            ->where('approval', 1); // Only approved passengers

        // Filter by name
        if ($request->filled('name')) {
            $query->where(function ($q) use ($request) {
                $q->where('user-first-name', 'like', '%' . $request->name . '%')
                    ->orWhere('user-last-name', 'like', '%' . $request->name . '%')
                    ->orWhere('email', 'like', '%' . $request->name . '%');
            });
        }

        // Filter by phone
        if ($request->filled('phone')) {
            $query->where('phone-no', 'like', '%' . $request->phone . '%');
        }

        // Filter by rating
        if ($request->filled('rating_filter')) {
            if ($request->rating_filter == 'warning') {
                // Rating < 2 (warning passengers)
                $query->whereHas('passengerRate', function($q) {
                    $q->where('rate', '<', 2);
                });
            } elseif ($request->rating_filter == 'no_rating') {
                // No rating yet
                $query->doesntHave('passengerRate');
            }
        }

        // Filter by university
        if ($request->filled('university_id')) {
            $query->where('university-id', $request->university_id);
        }

        // Filter by stage
        if ($request->filled('stage_id')) {
            $query->where('user-stage-id', $request->stage_id);
        }

        // Sort options
        $sortBy = $request->get('sort_by', 'id');
        $sortDirection = $request->get('sort_direction', 'desc');

        if ($sortBy === 'rating') {
            $query->leftJoin('passenger-rate', 'users.id', '=', 'passenger-rate.user-id')
                ->select('users.*')
                ->orderBy('passenger-rate.rate', $sortDirection);
        } else {
            $query->orderBy($sortBy, $sortDirection);
        }

        $passengers = $query->paginate(20)->appends($request->query());

        // Attach trip counts
        $passengers->getCollection()->transform(function ($passenger) {
            $passenger->weekly_trips_count = SugWeekDriver::where('passenger-id', $passenger->id)->count();
            $passenger->total_trips = $passenger->immediate_trips_count + $passenger->daily_trips_count + $passenger->weekly_trips_count;
            return $passenger;
        });

        // Get filter options
        $universities = University::all();
        $stages = Stage::all();

        return view('dashboard.passengers.index', compact('passengers', 'universities', 'stages'));
    }

    public function show(User $passenger)
    {
        // Ensure this is a passenger
        if ($passenger->{'user-type'} !== 'passenger') {
            return redirect()->route('passengers.index')->with('error', __('Invalid passenger.'));
        }

        $passenger->load(['callingKey', 'university', 'stage', 'passengerRate', 'newUserInfo']);
        return view('dashboard.passengers.show', compact('passenger'));
    }

    public function ban(User $passenger)
    {
        // Only ban if rating is less than 2
        $rating = PassengerRate::where('user-id', $passenger->id)->first();

        if (!$rating || $rating->rate >= 2) {
            return redirect()->route('passengers.show', $passenger->id)
                ->with('error', __('Passenger cannot be banned. Rating is not below 2.'));
        }

        try {
            $rating = $passenger->passengerRate ?? PassengerRate::where('user-id', $passenger->id)->first();

            $passenger->update(['approval' => 3]);

            return redirect()->route('passengers.index')->with('success', __('Passenger has been banned successfully.'));
        } catch (\Exception $e) {
            return redirect()->route('passengers.show', $passenger->id)
                ->with('error', __('Unable to ban passenger.'));
        }
    }

    public function trips(User $passenger)
    {
        // Ensure this is a passenger
        if ($passenger->{'user-type'} !== 'passenger') {
            return redirect()->route('passengers.index')->with('error', __('Invalid passenger.'));
        }

        $passenger->load(['callingKey', 'university', 'stage']);

        // Get all trips
        $immediateTrips = SuggestionDriver::where('passenger-id', $passenger->id)
            ->with('driver')
            ->orderBy('date-of-add', 'desc')
            ->get();

        $dailyTrips = SugDayDriver::where('passenger-id', $passenger->id)
            ->with('driver')
            ->orderBy('date-of-add', 'desc')
            ->get();

        $weeklyTrips = SugWeekDriver::where('passenger-id', $passenger->id)
            ->with('driver')
            ->orderBy('date-of-add', 'desc')
            ->get();

        return view('dashboard.passengers.trips', compact('passenger', 'immediateTrips', 'dailyTrips', 'weeklyTrips'));
    }

    public function updateApproval(Request $request, User $passenger)
    {
        $request->validate([
            'approval' => 'required|in:1,2,3',
        ]);

        try {
            $passenger->update(['approval' => $request->approval]);

            $approvalText = [
                1 => __('Approved'),
                2 => __('Pending Review'),
                3 => __('Banned')
            ];

            return redirect()->route('passengers.show', $passenger->id)
                ->with('success', __('Passenger status updated to') . ' ' . $approvalText[$request->approval]);
        } catch (\Exception $e) {
            return redirect()->route('passengers.show', $passenger->id)
                ->with('error', __('Unable to update passenger status.'));
        }
    }

    public function allTrips(Request $request)
    {
        // Get immediate trips
        $immediateTrips = SuggestionDriver::with(['passenger', 'driver'])
            ->when($request->filled('passenger_id'), function($q) use ($request) {
                $q->where('passenger-id', $request->passenger_id);
            })
            ->when($request->filled('driver_id'), function($q) use ($request) {
                $q->where('driver-id', $request->driver_id);
            })
            ->when($request->filled('date_from'), function($q) use ($request) {
                $q->where('date-of-add', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function($q) use ($request) {
                $q->where('date-of-add', '<=', $request->date_to . ' 23:59:59');
            })
            ->latest('date-of-add')
            ->paginate(20, ['*'], 'immediate_page')
            ->appends($request->query());

        // Get daily trips
        $dailyTrips = SugDayDriver::with(['passenger', 'driver'])
            ->when($request->filled('passenger_id'), function($q) use ($request) {
                $q->where('passenger-id', $request->passenger_id);
            })
            ->when($request->filled('driver_id'), function($q) use ($request) {
                $q->where('driver-id', $request->driver_id);
            })
            ->when($request->filled('date_from'), function($q) use ($request) {
                $q->where('date-of-add', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function($q) use ($request) {
                $q->where('date-of-add', '<=', $request->date_to . ' 23:59:59');
            })
            ->latest('date-of-add')
            ->paginate(20, ['*'], 'daily_page')
            ->appends($request->query());

        // Get weekly trips
        $weeklyTrips = SugWeekDriver::with(['passenger', 'driver'])
            ->when($request->filled('passenger_id'), function($q) use ($request) {
                $q->where('passenger-id', $request->passenger_id);
            })
            ->when($request->filled('driver_id'), function($q) use ($request) {
                $q->where('driver-id', $request->driver_id);
            })
            ->when($request->filled('date_from'), function($q) use ($request) {
                $q->where('date-of-add', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function($q) use ($request) {
                $q->where('date-of-add', '<=', $request->date_to . ' 23:59:59');
            })
            ->latest('date-of-add')
            ->paginate(20, ['*'], 'weekly_page')
            ->appends($request->query());

        // Get filter options
        $passengers = User::where('user-type', 'passenger')->where('approval', 1)->get();
        $drivers = User::where('user-type', 'driver')->where('approval', 1)->get();

        return view('dashboard.passengers.all-trips', compact(
            'immediateTrips', 'dailyTrips', 'weeklyTrips', 'passengers', 'drivers'
        ));
    }

    public function profileUpdateRequests()
    {
        // Get all passengers with approval = 2 (pending review)
        $passengers = User::with(['callingKey', 'university', 'stage', 'newUserInfo'])
            ->where('user-type', 'passenger')
            ->where('approval', 2)
            ->whereHas('newUserInfo') // Only passengers who have new profile data
            ->paginate(20);

        return view('dashboard.passengers.profile-update-requests', compact('passengers'));
    }

    public function approveProfileUpdate(User $passenger)
    {
        // Check if passenger has pending profile update
        if ($passenger->approval !== 2 || !$passenger->newUserInfo) {
            return redirect()->route('passengers.show', $passenger->id)
                ->with('error', __('No pending profile update found for this passenger.'));
        }

        try {
            // Update passenger with new information
            $newInfo = $passenger->newUserInfo;

            $passenger->update([
                'user-first-name' => $newInfo->{'user-first-name'},
                'user-last-name' => $newInfo->{'user-last-name'},
                'phone-no' => $newInfo->{'phone-no'},
                'email' => $newInfo->email,
                'call-key-id' => $newInfo->{'call-key-id'},
                'university-id' => $newInfo->{'university-id'},
                'user-stage-id' => $newInfo->{'user-stage-id'},
                'approval' => 1, // Set back to approved
                'date-of-edit' => now()
            ]);

            // Delete the new user info record
            $newInfo->delete();

            return redirect()->route('passengers.profile-update-requests')
                ->with('success', __('Profile update approved successfully.'));
        } catch (\Exception $e) {
            return redirect()->route('passengers.show', $passenger->id)
                ->with('error', __('Unable to approve profile update.'));
        }
    }

    public function rejectProfileUpdate(User $passenger)
    {
        // Check if passenger has pending profile update
        if ($passenger->approval !== 2 || !$passenger->newUserInfo) {
            return redirect()->route('passengers.show', $passenger->id)
                ->with('error', __('No pending profile update found for this passenger.'));
        }

        try {
            // Delete the new user info record and set approval back to 1
            $passenger->newUserInfo->delete();

            $passenger->update([
                'approval' => 1, // Set back to approved
                'date-of-edit' => now()
            ]);

            return redirect()->route('passengers.profile-update-requests')
                ->with('success', __('Profile update rejected successfully.'));
        } catch (\Exception $e) {
            return redirect()->route('passengers.show', $passenger->id)
                ->with('error', __('Unable to reject profile update.'));
        }
    }
}

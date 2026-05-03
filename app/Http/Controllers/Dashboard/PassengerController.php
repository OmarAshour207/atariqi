<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PassengerRate;
use App\Models\University;
use App\Models\Stage;
use App\Models\SuggestionDriver;
use App\Models\SugDayDriver;
use App\Models\SugWeekDriver;
use Illuminate\Http\Request;

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

        $passenger->load(['callingKey', 'university', 'stage', 'passengerRate']);
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
            'approval' => 'required|in:0,1,2',
        ]);

        try {
            $passenger->update(['approval' => $request->approval]);

            $approvalText = [
                0 => __('Banned'),
                1 => __('Approved'),
                2 => __('Pending Review')
            ];

            return redirect()->route('passengers.show', $passenger->id)
                ->with('success', __('Passenger status updated to') . ' ' . $approvalText[$request->approval]);
        } catch (\Exception $e) {
            return redirect()->route('passengers.show', $passenger->id)
                ->with('error', __('Unable to update passenger status.'));
        }
    }
}

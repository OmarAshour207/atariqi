<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DelDailyInfo;
use App\Models\DelWeekInfo;
use App\Models\DelImmediateInfo;
use App\Models\SugDayDriver;
use App\Models\SugWeekDriver;
use App\Models\SuggestionDriver;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('callingKey')
            ->where('user-type', 'passenger');

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

        $users = $query->paginate(20)->appends($request->query());

        // Calculate trip counts for each user
        $users->getCollection()->transform(function ($user) {
            // Get immediate trips count
            $user->immediate_trips_count = SuggestionDriver::where('passenger-id', $user->id)->count();

            // Get daily trips count
            $user->daily_trips_count = SugDayDriver::where('passenger-id', $user->id)->count();

            // Get weekly trips count
            $user->weekly_trips_count = SugWeekDriver::where('passenger-id', $user->id)->count();

            // Total trips
            $user->total_trips = $user->immediate_trips_count + $user->daily_trips_count + $user->weekly_trips_count;

            return $user;
        });

        return view('dashboard.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load('callingKey');

        // Calculate trip counts
        $user->immediate_trips_count = SuggestionDriver::where('passenger-id', $user->id)->count();
        $user->daily_trips_count = SugDayDriver::where('passenger-id', $user->id)->count();
        $user->weekly_trips_count = SugWeekDriver::where('passenger-id', $user->id)->count();
        $user->total_trips = $user->immediate_trips_count + $user->daily_trips_count + $user->weekly_trips_count;

        return view('dashboard.users.show', compact('user'));
    }

    public function rates(User $user)
    {
        // Get all ratings given by this user
        $ratings = collect();

        // Get daily delivery ratings given by this user
        $dailyRatings = DelDailyInfo::with(['ride.driver'])
            ->whereHas('ride', function($query) use ($user) {
                $query->where('passenger-id', $user->id);
            })
            ->whereNotNull('passenger-rate')
            ->get()
            ->map(function($rating) {
                return (object) [
                    'rating' => $rating->passenger_rate,
                    'comment' => $rating->passenger_comment,
                    'created_at' => $rating->created_at,
                    'trip_type' => 'daily',
                    'driver' => $rating->ride->driver ?? null
                ];
            });

        // Get weekly delivery ratings given by this user
        $weeklyRatings = DelWeekInfo::with(['ride.driver'])
            ->whereHas('ride', function($query) use ($user) {
                $query->where('passenger-id', $user->id);
            })
            ->whereNotNull('passenger-rate')
            ->get()
            ->map(function($rating) {
                return (object) [
                    'rating' => $rating->passenger_rate,
                    'comment' => $rating->passenger_comment,
                    'created_at' => $rating->created_at,
                    'trip_type' => 'weekly',
                    'driver' => $rating->ride->driver ?? null
                ];
            });

        // Get immediate delivery ratings given by this user
        $immediateRatings = DelImmediateInfo::with(['ride.driver'])
            ->whereHas('ride', function($query) use ($user) {
                $query->where('passenger-id', $user->id);
            })
            ->whereNotNull('passenger-rate')
            ->get()
            ->map(function($rating) {
                return (object) [
                    'rating' => $rating->passenger_rate,
                    'comment' => $rating->passenger_comment,
                    'created_at' => $rating->created_at,
                    'trip_type' => 'immediate',
                    'driver' => $rating->ride->driver ?? null
                ];
            });

        // Combine all ratings
        $allRatings = $dailyRatings->concat($weeklyRatings)->concat($immediateRatings)
            ->sortByDesc('created_at');

        // Calculate statistics
        $stats = [
            'total_ratings' => $allRatings->count(),
            'average_rating' => $allRatings->avg('rating') ?? 0,
            'five_star_ratings' => $allRatings->where('rating', 5)->count(),
            'one_star_ratings' => $allRatings->where('rating', 1)->count(),
        ];

        return view('dashboard.users.rates', compact('user', 'allRatings', 'stats'));
    }
}

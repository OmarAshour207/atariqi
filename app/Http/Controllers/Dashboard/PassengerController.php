<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Mail\PassengerBannedMail;
use App\Mail\PassengerProfileUpdateAcceptedMail;
use App\Mail\PassengerProfileUpdateRejectedMail;
use App\Mail\PassengerRequestAssignmentMail;
use App\Models\Admin;
use App\Models\PassengerBanned;
use App\Models\User;
use App\Models\PassengerRate;
use App\Models\University;
use App\Models\Stage;
use App\Models\NewUserInfo;
use App\Models\PassengerRequestDecision;
use App\Models\PassengerRequestAssignment;
use App\Models\PlatformEmailLog;
use App\Models\SugDayDriver;
use App\Models\SuggestionDriver;
use App\Models\SugWeekDriver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PassengerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['callingKey', 'university', 'stage', 'passengerRate'])
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
        $admins = Admin::where('id', '!=', auth()->guard('admin')->id())->get();

        return view('dashboard.passengers.show', compact('passenger', 'admins'));
    }

    public function ban(User $passenger, Request $request)
    {
        $request->validate([
            'ban_reason' => ['required', 'string', 'max:1000'],
        ]);

        try {
            DB::beginTransaction();
            $oldApproval = $passenger->approval;

            $passenger->update(['approval' => 3]);

            PassengerBanned::create([
                'assigned_from_employee_id' => auth()->guard('admin')->id(),
                'passenger_identity' => $passenger->id,
                'passenger_no' => $passenger->{"phone-no"},
                'note' => $request->input('ban_reason'),
            ]);

            Mail::to($passenger->email)->send(new PassengerBannedMail($passenger, $request->input('ban_reason')));

            PlatformEmailLog::create([
                'assigned_from_employee_id' => auth()->guard('admin')->id(),
                'driver_id' => $passenger->id,
                'driver_email' => $passenger->email,
                'email_type' => 'ban_notification',
                'status' => 'sent',
                'error_message' => null
            ]);

            PassengerRequestDecision::create([
                'user_id' => $passenger->id,
                'action_type' => 'ban_user',
                'old_approval' => $oldApproval,
                'new_approval' => 3,
                'reason' => $request->input('ban_reason'),
                'decided_by_employee_id' => auth()->guard('admin')->id()
            ]);

            DB::commit();

            return redirect()->route('passengers.index')->with('success', __('Passenger has been banned successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
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
            ->with(['driver', 'booking'])
            ->orderBy('date-of-add', 'desc')
            ->get();

        $weeklyTrips = SugWeekDriver::where('passenger-id', $passenger->id)
            ->with(['driver', 'booking'])
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
                1 => __('approved'),
                2 => __('pending review'),
                3 => __('rejected')
            ];

            // Send email notification
            $status = $request->approval == 1 ? 'approved' : ($request->approval == 3 ? 'rejected' : 'pending');
            $info = $request->input('info', null); // Optionally pass extra info from request
            \Mail::to($passenger->email)->send(new \App\Mail\PassengerStatusMail($passenger, $status, $info));

            return redirect()->route('passengers.show', $passenger->id)
                ->with('success', __('Passenger status updated to') . ' ' . __($approvalText[$request->approval]));
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
        // Get all newUserInfo entries for passengers pending review
        $passengers = \App\Models\NewUserInfo::with([
                'user.callingKey',
                'user.university',
                'user.stage'
            ])
            ->whereHas('user', function($q) {
                $q->where('user-type', 'passenger')->where('approval', 2);
            })
            ->paginate(20);

        $admins = Admin::where('id', '!=', auth()->guard('admin')->id())->get();

        return view('dashboard.passengers.profile-update-requests', compact('passengers', 'admins'));
    }

    public function approveProfileUpdate(User $passenger)
    {
        // Check if passenger has pending profile update
        if ($passenger->approval !== 2 || !$passenger->newUserInfo) {
            return redirect()->route('passengers.show', $passenger->id)
                ->with('error', __('No pending profile update found for this passenger.'));
        }

        try {
            DB::beginTransaction();

            $oldApproval = $passenger->approval;
            $newInfo = $passenger->newUserInfo;

            $passenger->update([
                'user-first-name' => $newInfo->{'user-first-name'},
                'user-last-name' => $newInfo->{'user-last-name'},
                'phone-no' => $newInfo->{'phone-no'},
                'email' => $newInfo->email,
                'call-key-id' => $newInfo->{'call-key-id'},
                'university-id' => $newInfo->{'university-id'},
                'user-stage-id' => $newInfo->{'user-stage-id'},
                'approval' => 1,
                'date-of-edit' => now()
            ]);

            $newInfo->delete();

            Mail::to($passenger->email)->send(new PassengerProfileUpdateAcceptedMail($passenger));

            PlatformEmailLog::create([
                'assigned_from_employee_id' => auth()->guard('admin')->id(),
                'driver_id' => $passenger->id,
                'driver_email' => $passenger->email,
                'email_type' => 'profile_update_approved',
                'status' => 'sent',
                'error_message' => null,
            ]);

            PassengerRequestDecision::create([
                'user_id' => $passenger->id,
                'action_type' => 'approve_profile_update',
                'old_approval' => $oldApproval,
                'new_approval' => 1,
                'reason' => __('Profile update approved'),
                'decided_by_employee_id' => auth()->guard('admin')->id(),
            ]);

            DB::commit();

            return redirect()->route('passengers.profile-update-requests')
                ->with('success', __('Profile update approved successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('passengers.show', $passenger->id)
                ->with('error', __('Unable to approve profile update.'));
        }
    }

    public function rejectProfileUpdate(Request $request, User $passenger)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        // Check if passenger has pending profile update
        if ($passenger->approval !== 2 || !$passenger->newUserInfo) {
            return redirect()->route('passengers.show', $passenger->id)
                ->with('error', __('No pending profile update found for this passenger.'));
        }

        try {
            DB::beginTransaction();

            $oldApproval = $passenger->approval;
            $rejectionReason = $request->input('rejection_reason');

            $passenger->newUserInfo->delete();

            $passenger->update([
                'approval' => 1,
                'date-of-edit' => now()
            ]);

            Mail::to($passenger->email)->send(new PassengerProfileUpdateRejectedMail($passenger, $rejectionReason));

            PlatformEmailLog::create([
                'assigned_from_employee_id' => auth()->guard('admin')->id(),
                'driver_id' => $passenger->id,
                'driver_email' => $passenger->email,
                'email_type' => 'profile_update_rejected',
                'status' => 'sent',
                'error_message' => null,
            ]);

            PassengerRequestDecision::create([
                'user_id' => $passenger->id,
                'action_type' => 'reject_profile_update',
                'old_approval' => $oldApproval,
                'new_approval' => 1,
                'reason' => $rejectionReason,
                'decided_by_employee_id' => auth()->guard('admin')->id(),
            ]);

            DB::commit();

            return redirect()->route('passengers.profile-update-requests')
                ->with('success', __('Profile update rejected successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', __('Unable to reject profile update.'));
        }
    }

    public function assignProfileUpdateToAdmin(Request $request, User $passenger)
    {
        $request->validate([
            'assign_note' => ['required', 'string', 'max:1000'],
            'assigned_admin' => ['required', 'exists:admins,id'],
        ]);

        if ($passenger->{'user-type'} !== 'passenger' || $passenger->approval !== 2 || !$passenger->newUserInfo) {
            return redirect()->back()
                ->with('error', __('No pending profile update found for this passenger.'));
        }

        $assignedAdmin = Admin::findOrFail($request->input('assigned_admin'));
        $assignedBy = auth()->guard('admin')->user();

        if ($assignedAdmin->id === $assignedBy->id) {
            return redirect()->back()
                ->with('error', __('Please select another admin to assign this request.'));
        }

        try {
            DB::beginTransaction();

            PassengerRequestAssignment::create([
                'user_id' => $passenger->id,
                'assigned_from_employee_id' => $assignedBy->id,
                'assigned_to_employee_id' => $assignedAdmin->id,
                'note' => $request->input('assign_note'),
                'status' => 'assigned',
            ]);

            Mail::to($assignedAdmin->email)->send(
                new PassengerRequestAssignmentMail($assignedAdmin, $passenger, $request->input('assign_note'), $assignedBy)
            );

            DB::commit();

            return redirect()->back()
                ->with('success', __('Passenger request assigned successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', __('Unable to assign passenger request.'));
        }
    }
}

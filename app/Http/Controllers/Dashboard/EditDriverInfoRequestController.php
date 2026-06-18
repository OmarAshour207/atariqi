<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\DriverInfo\UpdateDriverInfoRequest;
use App\Mail\DriverInfoAcceptedMail;
use App\Mail\DriverRejectedMail;
use App\Models\CaptainRequestDecision;
use App\Models\DriverType;
use App\Models\Neighbour;
use App\Models\NewDriverCar;
use App\Models\NewDriverInfo;
use App\Models\NewUserInfo;
use App\Models\PlatformEmailLog;
use App\Models\Stage;
use App\Models\University;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class EditDriverInfoRequestController extends Controller
{
    public function index()
    {
        $drivers = NewUserInfo::with(['user', 'user.callingKey'])
            ->where('user-type', 'driver')
            ->paginate(20);

        return view('dashboard.drivers_info_requests.index', compact('drivers'));
    }

    public function show($driver)
    {
        $newDriverInfo = NewUserInfo::where('user-id', $driver)->firstOrFail();
        $oldDriver = User::findOrFail($driver);

        $universities = University::all();
        $stages = Stage::all();
        $neighborhoods = Neighbour::all();
        $driverTypes = DriverType::all();

        $newDriverInfo->load('callingKey', 'university', 'stage');
        $oldDriver->load('callingKey', 'university', 'stage', 'driverInfo', 'driverCar.driverType');

        $newDriverInfoRecord = NewDriverInfo::where('driver-id', $driver)->first();
        $newDriverCarRecord = NewDriverCar::with('driverType')->where('driver-id', $driver)->first();

        return view('dashboard.drivers_info_requests.show', compact(
            'newDriverInfo',
            'oldDriver',
            'newDriverInfoRecord',
            'newDriverCarRecord',
            'universities',
            'stages',
            'neighborhoods',
            'driverTypes'
        ));
    }

    public function update($driver, UpdateDriverInfoRequest $request)
    {
        $driver = User::findOrFail($driver);
        $oldApproval = $driver->approval;
        $employeeId = auth()->guard('admin')->id();

        $newUserInfo = NewUserInfo::where('user-id', $driver->id)->first();
        $newDriverInfo = NewDriverInfo::where('driver-id', $driver->id)->first();
        $newCarInfo = NewDriverCar::where('driver-id', $driver->id)->first();

        if ($request->input('approval') == 3) {
            $rejectionReason = $request->input('rejection-reason', 'Request rejected by administrator');

            CaptainRequestDecision::create([
                'user_id' => $driver->id,
                'action_type' => 'edit_driver_info_rejected',
                'old_approval' => $oldApproval,
                'new_approval' => 1,
                'decided_by_employee_id' => $employeeId,
                'reject_reason' => $rejectionReason,
            ]);

            $newUserInfo?->delete();
            $newDriverInfo?->delete();
            $newCarInfo?->delete();

            $driver->update(['approval' => 1]);

            $this->sendEditInfoEmail($driver, 'driver_info_update_rejected', $rejectionReason, true);

            return redirect()->route('edit-info-request.index')
                ->with('success', __('Driver info update request rejected successfully.'));
        }

        $userfields = [
            'user-first-name' => $newUserInfo?->{'user-first-name'},
            'user-last-name' => $newUserInfo?->{'user-last-name'},
            'email' => $newUserInfo?->email,
            'phone-no' => $newUserInfo?->{'phone-no'},
            'gender' => $newUserInfo?->gender,
            'image' => $newUserInfo?->image,
            'university-id' => $newUserInfo?->{"university-id"},
            'user-stage-id' => $newUserInfo?->{"user-stage-id"},
            'approval' => 1,
        ];

        $driver->update($userfields);

        if ($driver->driverInfo && $newDriverInfo) {
            $driver->driverInfo->update([
                'car-brand' => $newDriverInfo->{'car-brand'},
                'car-model' => $newDriverInfo->{'car-model'},
                'car-number' => $newDriverInfo->{'car-number'},
                'car-letters' => $newDriverInfo->{'car-letters'},
                'car-color' => $newDriverInfo->{'car-color'},
                'driver-license-link' => $newDriverInfo->{'driver-license-link'},
                'allow-disabilities' => $newDriverInfo->{'allow-disabilities'} ?? 'no',
            ]);
        }

        if ($driver->driverCar && $newCarInfo) {
            $driver->driverCar->update([
                'driver-type-id' => $newCarInfo->{'driver-type-id'},
                'car_form_img' => $newCarInfo->car_form_img,
                'license_img' => $newCarInfo->license_img ?? $newCarInfo->licnese_img ?? null,
                'car_front_img' => $newCarInfo->car_front_img,
                'car_back_img' => $newCarInfo->car_back_img,
                'car_rside_img' => $newCarInfo->car_rside_img,
                'car_lside_img' => $newCarInfo->car_lside_img,
                'car_insideFront_img' => $newCarInfo->car_insideFront_img,
                'car_insideBack_img' => $newCarInfo->car_insideBack_img,
            ]);
        }

        $newUserInfo?->delete();
        $newDriverInfo?->delete();
        $newCarInfo?->delete();

        CaptainRequestDecision::create([
            'user_id' => $driver->id,
            'action_type' => 'edit_driver_info_approved',
            'old_approval' => $oldApproval,
            'new_approval' => 1,
            'decided_by_employee_id' => $employeeId,
        ]);

        $this->sendEditInfoEmail($driver, 'driver_info_update', null, false);

        return redirect()->route('edit-info-request.index')
            ->with('success', __('Driver updated successfully.'));
    }

    private function sendEditInfoEmail(User $driver, string $emailType, ?string $rejectionReason, bool $isRejection): void
    {
        if (!$driver->email) {
            return;
        }

        try {
            if ($isRejection) {
                Mail::to($driver->email)->send(new DriverRejectedMail($driver, $rejectionReason));
            } else {
                Mail::to($driver->email)->send(new DriverInfoAcceptedMail($driver));
            }

            PlatformEmailLog::create([
                'assigned_from_employee_id' => auth()->guard('admin')->id(),
                'driver_id' => $driver->id,
                'driver_email' => $driver->email,
                'email_type' => $emailType,
                'status' => 'sent',
                'error_message' => null,
            ]);
        } catch (\Throwable $e) {
            PlatformEmailLog::create([
                'assigned_from_employee_id' => auth()->guard('admin')->id(),
                'driver_id' => $driver->id,
                'driver_email' => $driver->email,
                'email_type' => $emailType,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }
}

<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\DriverInfo\UpdateDriverInfoRequest;
use App\Mail\DriverInfoAcceptedMail;
use App\Mail\DriverEditInfoRejectedMail;
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
use App\Services\WaslService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EditDriverInfoRequestController extends Controller
{
    public function __construct(private WaslService $waslService)
    {
    }

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
        $newDriverCarRecord = NewDriverCar::with('driverType')
            ->where('driver-id', $driver)
            ->latest('id')
            ->first();

        $waslEligibility = [
            'is_valid' => null,
            'api_error' => false,
            'message' => __('Unknown'),
            'display_status' => __('Unknown'),
        ];

        if ($oldDriver->driverInfo && filled($oldDriver->driverInfo->identity_number)) {
            try {
                $waslDriver = $this->waslService->buildDriverForWaslRegistration(
                    $oldDriver,
                    $newDriverInfo,
                    $newDriverInfoRecord
                );

                $waslEligibility = $this->waslService->syncDriverWithWasl($waslDriver);
            } catch (\Exception $e) {
                Log::error('Error syncing Wasl driver data for edit-info-request: ' . $e->getMessage());
                $waslEligibility = array_merge($waslEligibility, [
                    'is_valid' => false,
                    'api_error' => true,
                    'message' => $e->getMessage(),
                    'display_status' => __('Verification Failed'),
                ]);
            }
        }

        $comparisonData = $this->buildDriverInfoRequestComparisons(
            $oldDriver,
            $newDriverInfo,
            $newDriverInfoRecord,
            $newDriverCarRecord
        );

        return view('dashboard.drivers_info_requests.show', array_merge(
            compact(
                'newDriverInfo',
                'oldDriver',
                'newDriverInfoRecord',
                'newDriverCarRecord',
                'universities',
                'stages',
                'neighborhoods',
                'driverTypes',
                'waslEligibility'
            ),
            $comparisonData
        ));
    }

    public function update($driver, UpdateDriverInfoRequest $request)
    {
        $driver = User::findOrFail($driver);
        $oldApproval = $driver->approval;
        $employeeId = auth()->guard('admin')->id();

        $newUserInfo = NewUserInfo::where('user-id', $driver->id)->first();
        $newDriverInfo = NewDriverInfo::where('driver-id', $driver->id)->first();
        $newCarInfo = NewDriverCar::where('driver-id', $driver->id)->latest('id')->first();

        if ($request->input('approval') == 3) {
            $rejectionReason = $request->input('rejection-reason', 'Request rejected by administrator');

            try {
                if ($driver->driverInfo && filled($driver->driverInfo->identity_number)) {
                    $this->waslService->registerDriver($driver->loadMissing(['driverInfo', 'callingKey']));
                }
            } catch (\Exception $e) {
                Log::warning('Failed to restore Wasl driver data after rejecting edit request: ' . $e->getMessage());
            }

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

        try {
            DB::beginTransaction();

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
                    'driver-license-link' => $newDriverInfo->{'driver-license-link'}
                        ?? $newCarInfo?->license_img
                        ?? $newCarInfo?->licnese_img
                        ?? $driver->driverInfo->{'driver-license-link'},
                    'allow-disabilities' => $newDriverInfo->{'allow-disabilities'} ?? $driver->driverInfo->{'allow-disabilities'} ?? 'no',
                ]);
            }

            if ($driver->driverCar && $newCarInfo) {
                $currentCar = $driver->driverCar;
                $carUpdate = [];

                if (filled($newCarInfo->{'driver-type-id'})
                    && (string) $newCarInfo->{'driver-type-id'} !== (string) $currentCar->{'driver-type-id'}) {
                    $carUpdate['driver-type-id'] = $newCarInfo->{'driver-type-id'};
                }

                $carImageFields = [
                    'car_form_img',
                    'license_img',
                    'car_front_img',
                    'car_back_img',
                    'car_rside_img',
                    'car_lside_img',
                    'car_insideFront_img',
                    'car_insideBack_img',
                ];

                foreach ($carImageFields as $field) {
                    $newValue = $newCarInfo->{$field};

                    if ($field === 'license_img' && !filled($newValue)) {
                        $newValue = $newCarInfo->licnese_img ?? null;
                    }

                    if (filled($newValue) && $newValue !== $currentCar->{$field}) {
                        $carUpdate[$field] = $newValue;
                    }
                }

                if (!empty($carUpdate)) {
                    $currentCar->update($carUpdate);
                }
            } elseif ($driver->driverCar && $newDriverInfo && filled($newDriverInfo->{'driver-license-link'})) {
                $licenseLink = $newDriverInfo->{'driver-license-link'};
                $currentLicense = $driver->driverInfo?->{'driver-license-link'}
                    ?? $driver->driverCar->license_img;

                if ($licenseLink !== $currentLicense) {
                    $driver->driverCar->update([
                        'license_img' => $licenseLink,
                    ]);
                }
            }

            $driver->refresh()->loadMissing(['driverInfo', 'callingKey']);

            if ($driver->driverInfo && filled($driver->driverInfo->identity_number)) {
                $this->waslService->registerDriver($driver);
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

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', __('Unable to update driver in ministry system.') . ' ' . $e->getMessage());
        }

        $this->sendEditInfoEmail($driver, 'driver_info_update', null, false);

        return redirect()->route('edit-info-request.index')
            ->with('success', __('Driver updated successfully.'));
    }

    private function buildDriverInfoRequestComparisons(
        User $oldDriver,
        NewUserInfo $newDriverInfo,
        ?NewDriverInfo $newDriverInfoRecord,
        ?NewDriverCar $newDriverCarRecord
    ): array {
        $carImageFields = [
            'car_form_img' => __('Car Form Image'),
            'license_img' => __('License Image'),
            'car_front_img' => __('Car Front Image'),
            'car_back_img' => __('Car Back Image'),
            'car_rside_img' => __('Car Right Side Image'),
            'car_lside_img' => __('Car Left Side Image'),
            'car_insideFront_img' => __('Car Inside Front Image'),
            'car_insideBack_img' => __('Car Inside Back Image'),
        ];

        $carImageComparisons = [];

        foreach ($carImageFields as $field => $label) {
            $carImageComparisons[] = [
                'label' => $label,
                'pending' => pending_image_filename(
                    $oldDriver->driverCar?->{$field},
                    $newDriverCarRecord?->{$field}
                ),
            ];
        }

        return [
            'pendingUserImage' => pending_image_filename($oldDriver->image, $newDriverInfo->image),
            'newLicenseImage' => pending_image_filename(
                $oldDriver->driverInfo?->{'driver-license-link'} ?? $oldDriver->driverCar?->license_img,
                $newDriverInfoRecord?->{'driver-license-link'} ?? $newDriverCarRecord?->license_img
            ),
            'carImageComparisons' => $carImageComparisons,
        ];
    }

    private function sendEditInfoEmail(User $driver, string $emailType, ?string $rejectionReason, bool $isRejection): void
    {
        if (!$driver->email) {
            return;
        }

        try {
            if ($isRejection) {
                Mail::to($driver->email)->send(new DriverEditInfoRejectedMail($driver, $rejectionReason));
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

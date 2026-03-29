<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\DriverInfo\UpdateDriverInfoRequest;
use App\Models\DriverNeighborhood;
use App\Models\DriverType;
use App\Models\Neighbour;
use App\Models\NewDriverCar;
use App\Models\NewDriverInfo;
use App\Models\NewUserInfo;
use App\Models\Stage;
use App\Models\University;
use App\Models\User;
use Illuminate\Http\Request;

class EditDriverInfoRequestController extends Controller
{
    public function index(Request $request)
    {
        $drivers = NewUserInfo::with('callingKey')
            ->where('user-type', 'driver')
            ->paginate(20);

        return view('dashboard.drivers_info_requests.index', compact('drivers'));
    }

    public function show($driver)
    {
        $newDriverInfo = NewUserInfo::where('user-id', $driver)->firstOrFail();

        $universities = University::all();
        $stages = Stage::all();
        $neighborhoods = Neighbour::all();
        $driverTypes = DriverType::all();

        $newDriverInfo->load('callingKey', 'driverInfo', 'driverCar');

        return view('dashboard.drivers_info_requests.show', compact('newDriverInfo', 'universities', 'stages', 'neighborhoods', 'driverTypes'));
    }

    public function update($driver, UpdateDriverInfoRequest $request)
    {
        $driver = User::findOrFail($driver);

        $newUserInfo = NewUserInfo::where('user-id', $driver->id)->first();
        $newDriverInfo = NewDriverInfo::where('driver-id', $driver->id)->first();
        $newCarInfo = NewDriverCar::where('driver-id', $driver->id)->first();

        if($request->input('approval') == 2) {
            $newUserInfo->delete();
            $newDriverInfo->delete();
            $newCarInfo->delete();
            return redirect()->route('edit-info-request.index')->with('success', 'Driver info update request rejected successfully.');
        }

        $userfields = [
            'user-first-name' => $newUserInfo->{'user-first-name'},
            'user-last-name' => $newUserInfo->{'user-last-name'},
            'email' => $newUserInfo->email,
            'phone-no' => $newUserInfo->{'phone-no'},
            'gender' => $newUserInfo->gender,
            'image' => $newUserInfo->image,
            'university-id' => $newUserInfo->{"university-id"},
            'user-stage-id' => $newUserInfo->{"user-stage-id"}
        ];

        $driver->update($userfields);

        $driverInfoFields = [
            'car-brand' => $newDriverInfo->{'car-brand'},
            'car-model' => $newDriverInfo->{'car-model'},
            'car-number' => $newDriverInfo->{'car-number'},
            'car-letters' => $newDriverInfo->{'car-letters'},
            'car-color' => $newDriverInfo->{'car-color'},
            'driver-license-link' => $newDriverInfo->{'driver-license-link'},
            'allow-disabilities' => $newDriverInfo->{'allow-disabilities'} ?? 'no',
        ];

        $driver->driverInfo->update($driverInfoFields);

        $driverCarFields = [
            'driver-type-id' => $newCarInfo->{'driver-type-id'},
            'car_form_img' => $newCarInfo->car_form_img,
            'licnese_img' => $newCarInfo->licnese_img,
            'car_front_img' => $newCarInfo->car_front_img,
            'car_back_img' => $newCarInfo->car_back_img,
            'car_rside_img' => $newCarInfo->car_rside_img,
            'car_lside_img' => $newCarInfo->car_lside_img,
            'car_insideFront_img' => $newCarInfo->car_insideFront_img,
            'car_insideBack_img' => $newCarInfo->car_insideBack_img,
        ];

        $driver->driverCar->update($driverCarFields);

        $newUserInfo->delete();
        $newDriverInfo->delete();
        $newCarInfo->delete();

        return redirect()->route('edit-info-request.index')->with('success', 'Driver updated successfully.');
    }
}

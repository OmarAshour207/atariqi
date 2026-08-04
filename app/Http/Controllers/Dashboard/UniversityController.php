<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Service;
use App\Models\UniDrivingService;
use App\Models\University;
use App\Models\User;
use App\Services\ActionsLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UniversityController extends Controller
{
    public function __construct(private ActionsLogService $actionsLog)
    {
    }

    public function index()
    {
        $universities = University::with(['cityUni', 'uniDrivingServices.service'])
            ->orderByDesc('id')
            ->paginate(20);

        return view('dashboard.universities.index', compact('universities'));
    }

    public function create()
    {
        $cities = City::orderBy('city-ar')->get();
        $services = Service::orderBy('service-ar')->get();

        return view('dashboard.universities.create', compact('cities', 'services'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name-ar' => 'required|string|max:255',
            'name-eng' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id',
            'location' => 'nullable|string|max:500',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'service_ids' => 'nullable|array',
            'service_ids.*' => 'exists:services,id',
        ]);

        $city = City::findOrFail($data['city_id']);
        $now = now();

        DB::transaction(function () use ($data, $city, $now, $request) {
            $university = University::create([
                'name-ar' => $data['name-ar'],
                'name-eng' => $data['name-eng'],
                'city_id' => $data['city_id'],
                'city' => $city->{'city-ar'},
                'country' => 'SA',
                'location' => $data['location'] ?? null,
                'lat' => $data['lat'] ?? null,
                'lng' => $data['lng'] ?? null,
                'date-of-add' => $now,
                'date-of-edit' => $now,
            ]);

            $this->actionsLog->logAdd('university', $university->id);

            foreach ($request->input('service_ids', []) as $serviceId) {
                $link = UniDrivingService::create([
                    'id' => UniDrivingService::nextId(),
                    'university-id' => $university->id,
                    'service-id' => $serviceId,
                    'date-of-add' => $now,
                ]);
                $this->actionsLog->logAdd('uni-driving-services', $link->id);
            }
        });

        return redirect()->route('universities.index')->with('success', __('University created successfully.'));
    }

    public function services(University $university)
    {
        $university->load(['uniDrivingServices.service', 'cityUni']);
        $services = Service::orderBy('service-ar')->get();
        $linkedIds = $university->uniDrivingServices->pluck('service-id')->all();

        return view('dashboard.universities.services', compact('university', 'services', 'linkedIds'));
    }

    public function storeServices(Request $request, University $university)
    {
        $data = $request->validate([
            'service_ids' => 'required|array|min:1',
            'service_ids.*' => 'exists:services,id',
        ]);

        $now = now();

        foreach ($data['service_ids'] as $serviceId) {
            $exists = UniDrivingService::where('university-id', $university->id)
                ->where('service-id', $serviceId)
                ->exists();

            if ($exists) {
                continue;
            }

            $link = UniDrivingService::create([
                'id' => UniDrivingService::nextId(),
                'university-id' => $university->id,
                'service-id' => $serviceId,
                'date-of-add' => $now,
            ]);

            $this->actionsLog->logAdd('uni-driving-services', $link->id);
        }

        return redirect()
            ->route('universities.services', $university)
            ->with('success', __('University services updated successfully.'));
    }

    public function destroy(Request $request, University $university)
    {
        $data = $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        if (User::where('university-id', $university->id)->exists()) {
            return redirect()
                ->route('universities.index')
                ->with('error', __('Cannot delete university because users are linked to it.'));
        }

        $old = $university->load('uniDrivingServices')->toArray();

        DB::transaction(function () use ($university, $old, $data) {
            UniDrivingService::where('university-id', $university->id)->delete();
            $university->delete();
            $this->actionsLog->logDelete('university', $old['id'], $old, $data['reason']);
        });

        return redirect()->route('universities.index')->with('success', __('University deleted successfully.'));
    }
}

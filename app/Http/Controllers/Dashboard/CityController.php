<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\DriverNeighborhood;
use App\Models\Neighbour;
use App\Services\ActionsLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CityController extends Controller
{
    public function __construct(private ActionsLogService $actionsLog)
    {
    }

    public function index()
    {
        $cities = City::with('neighbours')->orderBy('city-ar')->get();

        return view('dashboard.cities.index', compact('cities'));
    }

    public function create()
    {
        return view('dashboard.cities.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'city-ar' => 'required|string|max:255',
            'city-en' => 'required|string|max:255',
            'neighborhoods' => 'nullable|array',
            'neighborhoods.*.ar' => 'required_with:neighborhoods|string|max:255',
            'neighborhoods.*.eng' => 'required_with:neighborhoods|string|max:255',
        ]);

        if (City::where('city-ar', $data['city-ar'])->orWhere('city-en', $data['city-en'])->exists()) {
            return back()->withInput()->with('error', __('A city with the same name already exists.'));
        }

        DB::transaction(function () use ($data) {
            $city = City::create([
                'city-ar' => $data['city-ar'],
                'city-en' => $data['city-en'],
            ]);

            $this->actionsLog->logAdd('cities', $city->id);

            foreach ($data['neighborhoods'] ?? [] as $neighborhood) {
                if (empty($neighborhood['ar']) || empty($neighborhood['eng'])) {
                    continue;
                }

                $row = Neighbour::create([
                    'city_id' => $city->id,
                    'neighborhood-ar' => $neighborhood['ar'],
                    'neighborhood-eng' => $neighborhood['eng'],
                ]);

                $this->actionsLog->logAdd('neighborhoods', $row->id);
            }
        });

        return redirect()->route('cities.index')->with('success', __('City and neighborhoods created successfully.'));
    }

    public function storeNeighborhood(Request $request, City $city)
    {
        $data = $request->validate([
            'neighborhood-ar' => 'required|string|max:255',
            'neighborhood-eng' => 'required|string|max:255',
        ]);

        $exists = Neighbour::where('city_id', $city->id)
            ->where(function ($query) use ($data) {
                $query->where('neighborhood-ar', $data['neighborhood-ar'])
                    ->orWhere('neighborhood-eng', $data['neighborhood-eng']);
            })
            ->exists();

        if ($exists) {
            return back()->with('error', __('This neighborhood already exists in the selected city.'));
        }

        $neighborhood = Neighbour::create([
            'city_id' => $city->id,
            'neighborhood-ar' => $data['neighborhood-ar'],
            'neighborhood-eng' => $data['neighborhood-eng'],
        ]);

        $this->actionsLog->logAdd('neighborhoods', $neighborhood->id);

        return back()->with('success', __('Neighborhood added successfully.'));
    }

    public function updateNeighborhood(Request $request, Neighbour $neighborhood)
    {
        $old = $neighborhood->toArray();

        $data = $request->validate([
            'neighborhood-ar' => 'required|string|max:255',
            'neighborhood-eng' => 'required|string|max:255',
        ]);

        $neighborhood->update($data);
        $this->actionsLog->logEdit('neighborhoods', $neighborhood->id, $old);

        return back()->with('success', __('Neighborhood updated successfully.'));
    }

    public function destroyNeighborhood(Request $request, Neighbour $neighborhood)
    {
        $data = $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        if ($this->neighborhoodUsedByDriver($neighborhood->id)) {
            return back()->with('error', __('Cannot delete neighborhood because drivers are linked to it.'));
        }

        $old = $neighborhood->toArray();
        $neighborhood->delete();
        $this->actionsLog->logDelete('neighborhoods', $old['id'], $old, $data['reason']);

        return back()->with('success', __('Neighborhood deleted successfully.'));
    }

    private function neighborhoodUsedByDriver(int $neighborhoodId): bool
    {
        $needle = (string) $neighborhoodId;

        return DriverNeighborhood::query()
            ->where(function ($query) use ($needle) {
                $query->where('neighborhoods-to', 'like', "%{$needle}%")
                    ->orWhere('neighborhoods-from', 'like', "%{$needle}%");
            })
            ->exists();
    }
}

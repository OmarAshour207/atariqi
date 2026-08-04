<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Services\ActionsLogService;
use Illuminate\Http\Request;

class DeliveryServiceController extends Controller
{
    public function __construct(private ActionsLogService $actionsLog)
    {
    }

    public function index()
    {
        $services = Service::orderBy('id')->paginate(20);

        return view('dashboard.delivery_services.index', compact('services'));
    }

    public function edit(Service $deliveryService)
    {
        return view('dashboard.delivery_services.edit', ['service' => $deliveryService]);
    }

    public function update(Request $request, Service $deliveryService)
    {
        $old = $deliveryService->toArray();

        $data = $request->validate([
            'service' => 'required|string|max:255',
            'service-ar' => 'required|string|max:255',
            'service-eng' => 'required|string|max:255',
            'cost' => 'required|numeric|min:0',
            'road-way' => 'nullable|string|max:255',
        ]);

        $deliveryService->update(array_merge($data, [
            'date-of-edit' => now(),
        ]));

        $this->actionsLog->logEdit('services', $deliveryService->id, $old);

        return redirect()
            ->route('delivery-services.index')
            ->with('success', __('Service updated successfully.'));
    }
}

<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GeneralDuesPercentageController extends Controller
{
    public function show()
    {
        $subscription = Subscription::generalDuesPercentage();

        return view('dashboard.general-dues-percentage.show', compact('subscription'));
    }

    public function update(Request $request)
    {
        $subscription = Subscription::generalDuesPercentage();

        if (!$subscription) {
            return redirect()
                ->route('general-dues-percentage.show')
                ->with('error', __('General dues percentage record not found.'));
        }

        $data = $request->validate([
            'cost' => 'required|numeric|min:0|max:100',
        ], [
            'cost.required' => __('The percentage is required.'),
            'cost.numeric' => __('The percentage must be a valid number.'),
            'cost.min' => __('The percentage must be between 0 and 100.'),
            'cost.max' => __('The percentage must be between 0 and 100.'),
        ]);

        $oldCost = $subscription->cost;
        $newCost = (int) round($data['cost']);

        if ($oldCost === $newCost) {
            return redirect()
                ->route('general-dues-percentage.show')
                ->with('success', __('Saved successfully'));
        }

        $adminId = auth()->guard('admin')->id();

        DB::beginTransaction();
        try {
            $subscription->update(['cost' => $newCost]);

            DB::table('subscription_employee_log')->insert([
                'employee_id' => $adminId,
                'package_id' => $subscription->id,
                'action_type' => 'updated',
                'description' => __('General dues percentage updated by employee.'),
                'payload' => json_encode([
                    'old' => ['cost' => $oldCost],
                    'new' => ['cost' => $newCost],
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()
                ->route('general-dues-percentage.show')
                ->with('error', __('Unable to update general dues percentage.'));
        }

        return redirect()
            ->route('general-dues-percentage.show')
            ->with('success', __('General dues percentage updated successfully.'));
    }
}

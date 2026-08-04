<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Announce;
use App\Models\DriverAnnounce;
use App\Services\ActionsLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnnouncementController extends Controller
{
    public function __construct(private ActionsLogService $actionsLog)
    {
    }

    public function index()
    {
        $passengerAnnouncements = Announce::orderByDesc('date-of-add')->get()->map(function ($row) {
            return [
                'id' => $row->id,
                'source' => 'passengers',
                'title' => $row->{'title-ar'} ?: $row->{'title-eng'},
                'content' => $row->{'contant-ar'} ?: $row->{'contant-eng'},
                'target_app' => __('Passengers'),
                'created_at' => $row->{'date-of-add'},
            ];
        });

        $driverAnnouncements = DriverAnnounce::orderByDesc('id')->get()->map(function ($row) {
            return [
                'id' => $row->id,
                'source' => 'drivers',
                'title' => $row->{'title-ar'} ?: $row->{'title-eng'},
                'content' => $row->{'content-ar'} ?: $row->{'content-eng'},
                'target_app' => __('Drivers'),
                'created_at' => $row->{'date-of-add'},
            ];
        });

        $announcements = $passengerAnnouncements
            ->concat($driverAnnouncements)
            ->sortByDesc('created_at')
            ->values();

        return view('dashboard.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('dashboard.announcements.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title-ar' => 'required|string|max:255',
            'title-eng' => 'required|string|max:255',
            'content-ar' => 'required|string',
            'content-eng' => 'required|string',
            'target_app' => 'required|in:passengers,drivers,both',
        ]);

        $now = now();

        DB::transaction(function () use ($data, $now) {
            if (in_array($data['target_app'], ['passengers', 'both'], true)) {
                $id = Announce::nextId();
                Announce::create([
                    'id' => $id,
                    'title-ar' => $data['title-ar'],
                    'title-eng' => $data['title-eng'],
                    'contant-ar' => $data['content-ar'],
                    'contant-eng' => $data['content-eng'],
                    'date-of-add' => $now,
                    'date-of-edit' => $now,
                ]);
                $this->actionsLog->logAdd('announce', $id);
            }

            if (in_array($data['target_app'], ['drivers', 'both'], true)) {
                $announcement = DriverAnnounce::create([
                    'title-ar' => $data['title-ar'],
                    'title-eng' => $data['title-eng'],
                    'content-ar' => $data['content-ar'],
                    'content-eng' => $data['content-eng'],
                    'date-of-add' => $now,
                    'date-of-edit' => $now,
                ]);
                $this->actionsLog->logAdd('driver-announce', $announcement->id);
            }
        });

        return redirect()->route('announcements.index')->with('success', __('Announcement created successfully.'));
    }

    public function destroy(Request $request, string $source, int $id)
    {
        $data = $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        if ($source === 'passengers') {
            $announcement = Announce::findOrFail($id);
            $old = $announcement->toArray();
            $announcement->delete();
            $this->actionsLog->logDelete('announce', $id, $old, $data['reason']);
        } elseif ($source === 'drivers') {
            $announcement = DriverAnnounce::findOrFail($id);
            $old = $announcement->toArray();
            $announcement->delete();
            $this->actionsLog->logDelete('driver-announce', $id, $old, $data['reason']);
        } else {
            abort(404);
        }

        return redirect()->route('announcements.index')->with('success', __('Announcement deleted successfully.'));
    }
}

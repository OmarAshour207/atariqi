<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ActionsLog;
use App\Models\Admin;
use App\Models\CaptainRequestDecision;
use App\Models\DocumentUpdateLog;
use App\Models\DriverBanned;
use App\Models\EmployeePackageLog;
use App\Models\PassengerBanned;
use App\Models\PassengerRequestDecision;
use App\Models\PlatformEmailLog;
use App\Models\TicketStatusLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogsManagementController extends Controller
{
    private const PER_PAGE = 10;

    private array $tables = [
        'actions_log' => [
            'title' => 'Actions Log',
            'model' => ActionsLog::class,
            'columns' => ['id', 'action_type', 'table_raw', 'decided_by_employee_id', 'created_at'],
            'employee_column' => 'decided_by_employee_id',
            'date_column' => 'created_at',
            'action_column' => 'action_type',
        ],
        'ticket_status_logs' => [
            'title' => 'Ticket Status Logs',
            'model' => TicketStatusLog::class,
            'columns' => ['id', 'ticket_id', 'old_status', 'new_status', 'changed_by_employee_id', 'created_at'],
            'employee_column' => 'changed_by_employee_id',
            'date_column' => 'created_at',
            'action_column' => 'new_status',
        ],
        'platform_email_log' => [
            'title' => 'Platform Email Log',
            'model' => PlatformEmailLog::class,
            'columns' => ['id', 'assigned_from_employee_id', 'user_id', 'driver_email', 'email_type', 'status', 'created_at'],
            'employee_column' => 'assigned_from_employee_id',
            'date_column' => 'created_at',
            'action_column' => 'email_type',
        ],
        'subscription_employee_log' => [
            'title' => 'Subscription Employee Log',
            'model' => null,
            'table' => 'subscription_employee_log',
            'columns' => ['id', 'employee_id', 'action_type', 'description', 'created_at'],
            'employee_column' => 'employee_id',
            'date_column' => 'created_at',
            'action_column' => 'action_type',
        ],
        'passenger_request_decisions' => [
            'title' => 'Passenger Request Decisions',
            'model' => PassengerRequestDecision::class,
            'columns' => ['id', 'user_id', 'action_type', 'old_approval', 'new_approval', 'decided_by_employee_id', 'created_at'],
            'employee_column' => 'decided_by_employee_id',
            'date_column' => 'created_at',
            'action_column' => 'action_type',
        ],
        'document_updates_log' => [
            'title' => 'Document Updates Log',
            'model' => DocumentUpdateLog::class,
            'columns' => ['id', 'document_id', 'assigned_from_employee_id', 'document_link_old', 'document_link_new', 'status', 'created_at'],
            'employee_column' => 'assigned_from_employee_id',
            'date_column' => 'created_at',
            'action_column' => 'status',
        ],
        'captain_request_decisions' => [
            'title' => 'Captain Request Decisions',
            'model' => CaptainRequestDecision::class,
            'columns' => ['id', 'user_id', 'action_type', 'old_approval', 'new_approval', 'decided_by_employee_id', 'created_at'],
            'employee_column' => 'decided_by_employee_id',
            'date_column' => 'created_at',
            'action_column' => 'action_type',
        ],
        'drivers_banned' => [
            'title' => 'Driver Banned Log',
            'model' => DriverBanned::class,
            'columns' => ['id', 'assigned_from_employee_id', 'driver_identity', 'driver_no', 'driver_car_no', 'note', 'created_at'],
            'employee_column' => 'assigned_from_employee_id',
            'date_column' => 'created_at',
            'action_column' => 'note',
        ],
        'passenger_banned' => [
            'title' => 'Passenger Banned Log',
            'model' => PassengerBanned::class,
            'columns' => ['id', 'assigned_from_employee_id', 'passenger_identity', 'passenger_no', 'note', 'created_at'],
            'employee_column' => 'assigned_from_employee_id',
            'date_column' => 'created_at',
            'action_column' => 'note',
        ],
        'employee_package_logs' => [
            'title' => 'Employee Package Logs',
            'model' => EmployeePackageLog::class,
            'columns' => ['id', 'assigned_from_employee_id', 'driver_id', 'id_package_old', 'id_package_new', 'status', 'created_at'],
            'employee_column' => 'assigned_from_employee_id',
            'date_column' => 'created_at',
            'action_column' => 'status',
        ],
    ];

    public function index(Request $request)
    {
        $employees = Admin::orderBy('name')->get(['id', 'name']);
        $sections = [];

        foreach ($this->tables as $key => $config) {
            $sections[$key] = [
                'title' => __($config['title']),
                'columns' => $config['columns'],
                'rows' => $this->queryLogTable($key, $config, $request)
                    ->paginate(self::PER_PAGE, ['*'], "{$key}_page")
                    ->withQueryString(),
                'filters' => $request->only(["{$key}_employee", "{$key}_date", "{$key}_action", "{$key}_sort"]),
            ];
        }

        return view('dashboard.logs.index', compact('sections', 'employees'));
    }

    public function show(Request $request, string $table, int $id)
    {
        abort_unless(isset($this->tables[$table]), 404);

        $config = $this->tables[$table];
        $row = $this->baseQuery($table, $config)->where('id', $id)->first();

        abort_if(!$row, 404, __('Log record is not available.'));

        return view('dashboard.logs.show', [
            'table' => $table,
            'title' => __($config['title']),
            'row' => $row,
        ]);
    }

    private function queryLogTable(string $key, array $config, Request $request): Builder|\Illuminate\Database\Query\Builder
    {
        $query = $this->baseQuery($table = $key, $config);

        $employeeId = $request->input("{$key}_employee");
        $date = $request->input("{$key}_date");
        $action = $request->input("{$key}_action");
        $sort = $request->input("{$key}_sort", 'desc');

        if ($employeeId && !empty($config['employee_column'])) {
            $query->where($config['employee_column'], $employeeId);
        }

        if ($date && !empty($config['date_column'])) {
            $query->whereDate($config['date_column'], $date);
        }

        if ($action && !empty($config['action_column'])) {
            $query->where($config['action_column'], 'like', '%' . $action . '%');
        }

        $query->orderBy($config['date_column'] ?? 'created_at', $sort === 'asc' ? 'asc' : 'desc');

        return $query;
    }

    private function baseQuery(string $table, array $config): Builder|\Illuminate\Database\Query\Builder
    {
        if (!empty($config['model'])) {
            return $config['model']::query();
        }

        return DB::table($config['table'] ?? $table);
    }
}

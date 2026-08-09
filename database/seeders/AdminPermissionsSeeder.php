<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Permission;
use App\Models\WebPage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'View', 'code' => 'view'],
            ['name' => 'Edit', 'code' => 'edit'],
            ['name' => 'Delete', 'code' => 'delete'],
            ['name' => 'Add', 'code' => 'add'],
            ['name' => 'Approve', 'code' => 'approve'],
            ['name' => 'Reject', 'code' => 'reject'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['code' => $permission['code']], $permission);
        }

        $pages = [
            ['name' => 'Dashboard', 'route' => 'dashboard.index', 'sort_order' => 1],
            ['name' => 'Drivers', 'route' => 'drivers.index', 'sort_order' => 10],
            ['name' => 'New Drivers', 'route' => 'new-drivers.index', 'sort_order' => 11],
            ['name' => 'Edit Info Requests', 'route' => 'edit-info-request.index', 'sort_order' => 12],
            ['name' => 'Passengers', 'route' => 'passengers.index', 'sort_order' => 20],
            ['name' => 'Support Tickets', 'route' => 'support-tickets.index', 'sort_order' => 30],
            ['name' => 'Announcements', 'route' => 'announcements.index', 'sort_order' => 40],
            ['name' => 'Universities', 'route' => 'universities.index', 'sort_order' => 41],
            ['name' => 'Cities', 'route' => 'cities.index', 'sort_order' => 42],
            ['name' => 'Delivery Services', 'route' => 'delivery-services.index', 'sort_order' => 43],
            ['name' => 'Documents', 'route' => 'documents.index', 'sort_order' => 44],
            ['name' => 'Packages', 'route' => 'packages.index', 'sort_order' => 50],
            ['name' => 'Features', 'route' => 'features.index', 'sort_order' => 51],
            ['name' => 'Settings', 'route' => 'settings.index', 'sort_order' => 60],
            ['name' => 'Employees', 'route' => 'employees.index', 'sort_order' => 70],
            ['name' => 'Logs Management', 'route' => 'logs.index', 'sort_order' => 71],
        ];

        foreach ($pages as $page) {
            WebPage::updateOrCreate(['route' => $page['route']], array_merge($page, ['is_active' => true]));
        }

        $admin = Admin::query()->orderBy('id')->first();

        if ($admin) {
            $admin->update([
                'role' => 'admin',
                'type' => 'admin',
                'is_active' => true,
            ]);

            $admin->permissions()->sync(Permission::pluck('id'));
            $admin->pages()->sync(WebPage::pluck('id'));
        }
    }
}

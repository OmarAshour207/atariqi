<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Permission;
use App\Models\WebPage;
use Illuminate\Database\Seeder;

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

        $this->call(WebPagesSeeder::class);

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

<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\WebPage;
use Illuminate\Database\Seeder;

class WebPagesSeeder extends Seeder
{
    /**
     * Dashboard pages + only the actions that exist for each page's routes/UI.
     *
     * Sub-routes (show, edit, store, …) inherit access via resolveFallbackRoute().
     */
    public static function definitions(): array
    {
        return [
            // General
            [
                'name' => 'Dashboard',
                'route' => 'dashboard.index',
                'sort_order' => 1,
                'actions' => [Admin::ACTION_VIEW],
            ],

            // Homepage
            [
                'name' => 'Homepage Sections',
                'route' => 'homepage-sections.index',
                'sort_order' => 10,
                'group' => 'homepage',
                'actions' => [Admin::ACTION_VIEW, Admin::ACTION_ADD_DELETE, Admin::ACTION_UPDATE],
            ],
            [
                'name' => 'Homepage Stats',
                'route' => 'homepage-stats.index',
                'sort_order' => 11,
                'group' => 'homepage',
                'actions' => [Admin::ACTION_VIEW, Admin::ACTION_ADD_DELETE, Admin::ACTION_UPDATE],
            ],
            [
                'name' => 'Testimonials',
                'route' => 'testimonials.index',
                'sort_order' => 12,
                'group' => 'homepage',
                'actions' => [Admin::ACTION_VIEW, Admin::ACTION_ADD_DELETE, Admin::ACTION_UPDATE],
            ],
            [
                'name' => 'Partners & Achievements',
                'route' => 'partner-achievements.index',
                'sort_order' => 13,
                'group' => 'homepage',
                'actions' => [Admin::ACTION_VIEW, Admin::ACTION_ADD_DELETE, Admin::ACTION_UPDATE],
            ],

            // Drivers
            [
                'name' => 'Drivers',
                'route' => 'drivers.index',
                'sort_order' => 20,
                'group' => 'drivers',
                'actions' => [Admin::ACTION_VIEW, Admin::ACTION_UPDATE, Admin::ACTION_BAN],
            ],
            [
                'name' => 'New Drivers',
                'route' => 'new-drivers.index',
                'sort_order' => 21,
                'group' => 'drivers',
                'actions' => [Admin::ACTION_VIEW, Admin::ACTION_DECIDE, Admin::ACTION_ASSIGN],
            ],
            [
                'name' => 'Edit Info Requests',
                'route' => 'edit-info-request.index',
                'sort_order' => 22,
                'group' => 'drivers',
                'actions' => [Admin::ACTION_VIEW, Admin::ACTION_DECIDE],
            ],
            [
                'name' => 'Driver Package Management',
                'route' => 'drivers.packages',
                'sort_order' => 23,
                'group' => 'drivers',
                'actions' => [Admin::ACTION_VIEW, Admin::ACTION_UPDATE, Admin::ACTION_ADD_DELETE],
            ],
            [
                'name' => 'Driver Rates',
                'route' => 'drivers.rates',
                'sort_order' => 24,
                'group' => 'drivers',
                'actions' => [Admin::ACTION_VIEW],
            ],
            [
                'name' => 'Driver Trips',
                'route' => 'drivers.trips',
                'sort_order' => 25,
                'group' => 'drivers',
                'actions' => [Admin::ACTION_VIEW],
            ],
            [
                'name' => 'General Dues Percentage',
                'route' => 'general-dues-percentage.show',
                'sort_order' => 26,
                'group' => 'drivers',
                'actions' => [Admin::ACTION_VIEW, Admin::ACTION_UPDATE],
            ],
            [
                'name' => 'Packages',
                'route' => 'packages.index',
                'sort_order' => 27,
                'group' => 'drivers',
                'actions' => [Admin::ACTION_VIEW, Admin::ACTION_ADD_DELETE, Admin::ACTION_UPDATE],
            ],
            [
                'name' => 'Features',
                'route' => 'features.index',
                'sort_order' => 28,
                'group' => 'drivers',
                'actions' => [Admin::ACTION_VIEW, Admin::ACTION_ADD_DELETE, Admin::ACTION_UPDATE],
            ],

            // Passengers / users
            [
                'name' => 'Passengers',
                'route' => 'passengers.index',
                'sort_order' => 30,
                'group' => 'users',
                'actions' => [Admin::ACTION_VIEW, Admin::ACTION_BAN],
            ],
            [
                'name' => 'All Passenger Trips',
                'route' => 'passengers.all-trips',
                'sort_order' => 31,
                'group' => 'users',
                'actions' => [Admin::ACTION_VIEW],
            ],
            [
                'name' => 'Profile Update Requests',
                'route' => 'passengers.profile-update-requests',
                'sort_order' => 32,
                'group' => 'users',
                'actions' => [Admin::ACTION_VIEW, Admin::ACTION_DECIDE, Admin::ACTION_ASSIGN],
            ],
            [
                'name' => 'User Trips',
                'route' => 'users.trips',
                'sort_order' => 33,
                'group' => 'users',
                'actions' => [Admin::ACTION_VIEW],
            ],
            [
                'name' => 'User Rates',
                'route' => 'users.rates',
                'sort_order' => 34,
                'group' => 'users',
                'actions' => [Admin::ACTION_VIEW],
            ],
            [
                'name' => 'Unride Rates',
                'route' => 'users.unride-rates',
                'sort_order' => 35,
                'group' => 'users',
                'actions' => [Admin::ACTION_VIEW],
            ],

            // Support
            [
                'name' => 'Support Tickets',
                'route' => 'support-tickets.index',
                'sort_order' => 40,
                'group' => 'support',
                'actions' => [Admin::ACTION_VIEW, Admin::ACTION_UPDATE, Admin::ACTION_ASSIGN, Admin::ACTION_CLOSE],
            ],

            // Platform management
            [
                'name' => 'Announcements',
                'route' => 'announcements.index',
                'sort_order' => 50,
                'group' => 'platform',
                'actions' => [Admin::ACTION_VIEW, Admin::ACTION_ADD_DELETE],
            ],
            [
                'name' => 'Universities',
                'route' => 'universities.index',
                'sort_order' => 51,
                'group' => 'platform',
                'actions' => [Admin::ACTION_VIEW, Admin::ACTION_ADD_DELETE],
            ],
            [
                'name' => 'Cities & Neighborhoods',
                'route' => 'cities.index',
                'sort_order' => 52,
                'group' => 'platform',
                'actions' => [Admin::ACTION_VIEW, Admin::ACTION_ADD_DELETE, Admin::ACTION_UPDATE],
            ],
            [
                'name' => 'Delivery Services',
                'route' => 'delivery-services.index',
                'sort_order' => 53,
                'group' => 'platform',
                'actions' => [Admin::ACTION_VIEW, Admin::ACTION_UPDATE],
            ],
            [
                'name' => 'Documents',
                'route' => 'documents.index',
                'sort_order' => 54,
                'group' => 'platform',
                'actions' => [Admin::ACTION_VIEW, Admin::ACTION_UPDATE],
            ],

            // Settings
            [
                'name' => 'Settings',
                'route' => 'settings.index',
                'sort_order' => 60,
                'actions' => [Admin::ACTION_VIEW, Admin::ACTION_UPDATE],
            ],

            // Company admin
            [
                'name' => 'Employee Management',
                'route' => 'employees.index',
                'sort_order' => 70,
                'group' => 'admin',
                'actions' => [Admin::ACTION_VIEW, Admin::ACTION_ADD_DELETE, Admin::ACTION_UPDATE],
            ],
            [
                'name' => 'Roles Management',
                'route' => 'roles.index',
                'sort_order' => 71,
                'group' => 'admin',
                'actions' => [Admin::ACTION_VIEW, Admin::ACTION_ADD_DELETE, Admin::ACTION_UPDATE],
            ],
            [
                'name' => 'Logs Management',
                'route' => 'logs.index',
                'sort_order' => 72,
                'group' => 'admin',
                'actions' => [Admin::ACTION_VIEW],
            ],
        ];
    }

    public static function groupLabels(): array
    {
        return [
            'general' => 'General',
            'homepage' => 'Homepage',
            'drivers' => 'Drivers',
            'users' => 'Passengers / Users',
            'support' => 'Support',
            'platform' => 'Platform',
            'settings' => 'Settings',
            'admin' => 'Admin',
        ];
    }

    public function run(): void
    {
        $groupParentIds = [];

        foreach (self::definitions() as $page) {
            $parentId = null;

            if (! empty($page['group'])) {
                if (! isset($groupParentIds[$page['group']])) {
                    $groupParentIds[$page['group']] = null;
                }

                $parentId = $groupParentIds[$page['group']];
            }

            $record = WebPage::updateOrCreate(
                ['route' => $page['route']],
                [
                    'name' => $page['name'],
                    'sort_order' => $page['sort_order'],
                    'parent_id' => $parentId,
                    'is_active' => true,
                ]
            );

            if (! empty($page['group']) && $groupParentIds[$page['group']] === null) {
                $groupParentIds[$page['group']] = $record->id;
            }
        }
    }
}

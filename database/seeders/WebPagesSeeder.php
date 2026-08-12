<?php

namespace Database\Seeders;

use App\Models\WebPage;
use Illuminate\Database\Seeder;

class WebPagesSeeder extends Seeder
{
    /**
     * Dashboard pages used for employee_page assignments and AdminAuthorizationService.
     *
     * Sub-routes (show, edit, store, …) inherit access via resolveFallbackRoute().
     */
    public static function definitions(): array
    {
        return [
            // General
            ['name' => 'Dashboard', 'route' => 'dashboard.index', 'sort_order' => 1],

            // Homepage
            ['name' => 'Homepage Sections', 'route' => 'homepage-sections.index', 'sort_order' => 10, 'group' => 'homepage'],
            ['name' => 'Homepage Stats', 'route' => 'homepage-stats.index', 'sort_order' => 11, 'group' => 'homepage'],
            ['name' => 'Testimonials', 'route' => 'testimonials.index', 'sort_order' => 12, 'group' => 'homepage'],
            ['name' => 'Partners & Achievements', 'route' => 'partner-achievements.index', 'sort_order' => 13, 'group' => 'homepage'],

            // Drivers
            ['name' => 'Drivers', 'route' => 'drivers.index', 'sort_order' => 20, 'group' => 'drivers'],
            ['name' => 'New Drivers', 'route' => 'new-drivers.index', 'sort_order' => 21, 'group' => 'drivers'],
            ['name' => 'Edit Info Requests', 'route' => 'edit-info-request.index', 'sort_order' => 22, 'group' => 'drivers'],
            ['name' => 'Driver Package Management', 'route' => 'drivers.packages', 'sort_order' => 23, 'group' => 'drivers'],
            ['name' => 'Driver Rates', 'route' => 'drivers.rates', 'sort_order' => 24, 'group' => 'drivers'],
            ['name' => 'Driver Trips', 'route' => 'drivers.trips', 'sort_order' => 25, 'group' => 'drivers'],
            ['name' => 'General Dues Percentage', 'route' => 'general-dues-percentage.show', 'sort_order' => 26, 'group' => 'drivers'],
            ['name' => 'Packages', 'route' => 'packages.index', 'sort_order' => 27, 'group' => 'drivers'],
            ['name' => 'Features', 'route' => 'features.index', 'sort_order' => 28, 'group' => 'drivers'],

            // Passengers / users
            ['name' => 'Passengers', 'route' => 'passengers.index', 'sort_order' => 30, 'group' => 'users'],
            ['name' => 'All Passenger Trips', 'route' => 'passengers.all-trips', 'sort_order' => 31, 'group' => 'users'],
            ['name' => 'Profile Update Requests', 'route' => 'passengers.profile-update-requests', 'sort_order' => 32, 'group' => 'users'],
            ['name' => 'Unride Rates', 'route' => 'users.unride-rates', 'sort_order' => 33, 'group' => 'users'],

            // Support
            ['name' => 'Support Tickets', 'route' => 'support-tickets.index', 'sort_order' => 40, 'group' => 'support'],

            // Platform management
            ['name' => 'Announcements', 'route' => 'announcements.index', 'sort_order' => 50, 'group' => 'platform'],
            ['name' => 'Universities', 'route' => 'universities.index', 'sort_order' => 51, 'group' => 'platform'],
            ['name' => 'Cities & Neighborhoods', 'route' => 'cities.index', 'sort_order' => 52, 'group' => 'platform'],
            ['name' => 'Delivery Services', 'route' => 'delivery-services.index', 'sort_order' => 53, 'group' => 'platform'],
            ['name' => 'Documents', 'route' => 'documents.index', 'sort_order' => 54, 'group' => 'platform'],

            // Settings
            ['name' => 'Settings', 'route' => 'settings.index', 'sort_order' => 60],

            // Company admin only (routes still registered for reference)
            ['name' => 'Employee Management', 'route' => 'employees.index', 'sort_order' => 70, 'group' => 'admin'],
            ['name' => 'Logs Management', 'route' => 'logs.index', 'sort_order' => 71, 'group' => 'admin'],
        ];
    }

    public function run(): void
    {
        $groupParentIds = [];

        foreach (self::definitions() as $page) {
            $parentId = null;

            if (!empty($page['group'])) {
                if (!isset($groupParentIds[$page['group']])) {
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

            if (!empty($page['group']) && $groupParentIds[$page['group']] === null) {
                $groupParentIds[$page['group']] = $record->id;
            }
        }
    }
}

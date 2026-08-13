<?php

$output = __DIR__ . '/Dashboard-Manual-Test-Cases.html';

function tc(string $title, string $objective, array $steps, string $expected): string
{
    $steps = array_map('htmlspecialchars', $steps);
    $stepsHtml = implode('', array_map(fn ($s) => "<li>{$s}</li>", $steps));

    return "<h4>{$title}</h4>"
        . "<p><strong>Objective:</strong> {$objective}</p>"
        . '<p><strong>Steps:</strong></p><ol>' . $stepsHtml . '</ol>'
        . '<p><strong>Expected Result:</strong></p>'
        . "<p>{$expected}</p><hr>";
}

function ep(string $method, string $uri, string $name, array $testCases, string $note = ''): string
{
    $html = "<h3>Endpoint: {$method} {$uri}</h3><p><strong>Route name:</strong> {$name}</p>";
    if ($note) {
        $html .= "<p><em>Note: {$note}</em></p>";
    }
    foreach ($testCases as $case) {
        $html .= tc($case[0], $case[1], $case[2], $case[3]);
    }

    return $html;
}

function getIndexCases(string $path, string $title): array
{
    return [
        ['Test Case 1: Successful Page Load', "View {$title} page", ["Login as company admin", "Navigate to {$path}"], "{$title} page loads with expected content."],
        ['Test Case 2: Unauthenticated Access', 'Guest blocked', ["Access {$path} without login"], 'Redirect to /dashboard/login.'],
        ['Test Case 3: Employee Without Page Permission', 'Unauthorized employee denied', ['Login as employee without this page assigned', "Navigate to {$path}"], '403 Forbidden: "You do not have permission to access this page."'],
        ['Test Case 4: Company Admin Access', 'Full admin always allowed', ['Login as company admin', "Navigate to {$path}"], 'Page loads successfully.'],
    ];
}

function getUnimplementedCases(string $path): array
{
    return [
        ['Test Case 1: Route Not Implemented', 'Verify unimplemented controller method', ["Navigate to {$path}"], '500 error or missing method. Route registered in web.php but controller action may not exist — confirm with development team.'],
    ];
}

$body = '';

// ==================== AUTH ====================
$body .= '<h2>1. Authentication</h2>';
$body .= ep('GET', '/dashboard/login', 'dashboard.loginForm', [
    ['Test Case 1: Display Login Page', 'Login form visible to guests', ['Open /dashboard/login in incognito window'], 'Login form with email and password fields renders.'],
    ['Test Case 2: Successful Login Redirect', 'Valid login reaches dashboard', ['Enter valid admin credentials', 'Submit form'], 'Redirect to /dashboard/index. Admin session active.'],
    ['Test Case 3: Invalid Credentials', 'Wrong password rejected', ['Enter valid email, wrong password', 'Submit'], 'Error: "The provided credentials do not match our records."'],
    ['Test Case 4: Validation — Empty Fields', 'Required validation', ['Submit empty form'], 'Validation errors on email and password fields.'],
    ['Test Case 5: Validation — Invalid Email', 'Email format check', ['Enter invalid email format', 'Submit'], 'Validation error on email field.'],
    ['Test Case 6: Login Throttle', 'Rate limit protection', ['Submit wrong password repeatedly'], 'Throttle middleware blocks excessive attempts after threshold.'],
    ['Test Case 7: Inactive Admin Blocked', 'Inactive accounts denied', ['Login with is_active=0 admin', 'Try accessing dashboard'], 'Logout with message: "Your account is inactive."'],
]);
$body .= ep('POST', '/dashboard/logout', 'dashboard.logout', [
    ['Test Case 1: Successful Logout', 'Session terminated', ['Login as admin', 'Click Logout'], 'Redirect to login. Session cleared.'],
    ['Test Case 2: Post-Logout Protection', 'Dashboard inaccessible after logout', ['Logout', 'Visit /dashboard/index'], 'Redirect to login.'],
    ['Test Case 3: CSRF Required', 'Token validation', ['POST logout without CSRF token'], '419 Page Expired.'],
]);

// ==================== HOME ====================
$body .= '<h2>2. Dashboard Home</h2>';
$body .= ep('GET', '/dashboard/index', 'dashboard.index', [
    ['Test Case 1: Authenticated Access', 'Home page loads', ['Login as admin', 'Open /dashboard/index'], 'Dashboard home renders.'],
    ['Test Case 2: Guest Redirect', 'Unauthenticated blocked', ['Open /dashboard/index without login'], 'Redirect to login.'],
    ['Test Case 3: Exempt From Page Check', 'All active admins can access', ['Login as employee with no pages', 'Open dashboard home'], 'Page loads (exempt route).'],
    ['Test Case 4: Inactive Admin', 'Inactive blocked', ['Use inactive admin account'], 'Redirect to login with inactive message.'],
]);

// ==================== PROFILE ====================
$body .= '<h2>3. Profile</h2>';
$body .= ep('GET', '/dashboard/profile/edit', 'profile.edit', [
    ['Test Case 1: View Form', 'Profile edit accessible', ['Login as admin', 'Open profile edit'], 'Password change form displayed.'],
    ['Test Case 2: Guest Blocked', 'Unauthenticated redirect', ['Access without login'], 'Redirect to login.'],
    ['Test Case 3: Exempt Route', 'No page permission needed', ['Employee without profile page assigned opens URL'], 'Form loads successfully.'],
]);
$body .= ep('POST', '/dashboard/profile/update', 'profile.update', [
    ['Test Case 1: Successful Password Change', 'Valid update', ['Enter correct old password', 'Enter new password (min 8) and confirmation', 'Submit'], 'Success: "Password updated successfully!"'],
    ['Test Case 2: Wrong Old Password', 'Incorrect old password', ['Enter wrong old password', 'Submit'], 'Error: "Old password is incorrect!"'],
    ['Test Case 3: Password Mismatch', 'Confirmation must match', ['Enter different new/confirm passwords', 'Submit'], 'Validation error on confirm_new_password.'],
    ['Test Case 4: Minimum Length', 'Min 8 characters', ['Enter new password with 7 characters', 'Submit'], 'Validation error: min 8 characters.'],
    ['Test Case 5: Required Fields', 'All fields required', ['Submit empty form'], 'Validation errors on all password fields.'],
]);

// ==================== SETTINGS & LANGUAGE ====================
$body .= '<h2>4. Settings &amp; Language</h2>';
$body .= ep('GET', '/dashboard/settings', 'settings.index', getIndexCases('/dashboard/settings', 'Settings'));
$body .= ep('POST', '/dashboard/settings/store', 'settings.store', [
    ['Test Case 1: Save Settings', 'Update app settings', ['Modify settings values', 'Submit form'], 'Flash: "Saved successfully". Values persisted.'],
    ['Test Case 2: Unauthorized Employee', 'Permission denied', ['Employee without settings page POSTs form'], '403 Forbidden.'],
    ['Test Case 3: CSRF Protection', 'Token required', ['POST without CSRF token'], '419 Page Expired.'],
]);
$body .= ep('GET', '/dashboard/language/{locale}', 'language', [
    ['Test Case 1: Switch to Arabic', 'Arabic UI', ['Click Language → Arabic or visit /dashboard/language/ar'], 'Redirect back. Arabic UI, dir=rtl, RTL CSS loaded.'],
    ['Test Case 2: Switch to English', 'English UI', ['Click Language → English or visit /dashboard/language/en'], 'Redirect back. English UI, dir=ltr.'],
    ['Test Case 3: Invalid Locale', 'Unsupported locale ignored', ['Visit /dashboard/language/fr'], 'Locale unchanged. Only en/ar accepted.'],
    ['Test Case 4: Locale Persists', 'Session retained', ['Switch language', 'Navigate to another page'], 'Selected language persists.'],
    ['Test Case 5: Exempt Route', 'Any admin can switch', ['Employee without page permissions switches language'], 'Language changes successfully.'],
]);

// ==================== HOMEPAGE SECTIONS ====================
$body .= '<h2>5. Homepage Sections</h2>';
$body .= ep('GET', '/dashboard/homepage-sections', 'homepage-sections.index', getIndexCases('/dashboard/homepage-sections', 'Homepage Sections'));
$body .= ep('GET', '/dashboard/homepage-sections/{id}/edit', 'homepage-sections.edit', [
    ['Test Case 1: Edit Section', 'Form loads with data', ['Click Edit on existing section'], 'Edit form pre-filled with current values.'],
    ['Test Case 2: Invalid ID', '404 for missing section', ['Visit edit URL with invalid ID'], '404 Not Found.'],
    ['Test Case 3: Unauthorized', '403 without permission', ['Employee without access opens edit URL'], '403 Forbidden.'],
]);
$body .= ep('PUT/PATCH', '/dashboard/homepage-sections/{id}', 'homepage-sections.update', [
    ['Test Case 1: Successful Update', 'Save section changes', ['Update title, content, icon', 'Submit'], 'Flash: "Section updated successfully." DB updated.'],
    ['Test Case 2: Invalid Image', 'Icon must be image', ['Upload non-image as icon', 'Submit'], 'Validation error on icon.'],
    ['Test Case 3: Max Length', '255 char limit on title', ['Enter title > 255 chars', 'Submit'], 'Validation error on title.'],
    ['Test Case 4: Unauthorized Update', '403 without permission', ['Unauthorized employee submits update'], '403 Forbidden.'],
]);
$body .= ep('GET', '/dashboard/homepage-sections/create', 'homepage-sections.create', getUnimplementedCases('/dashboard/homepage-sections/create'), 'Controller method may not be implemented.');
$body .= ep('POST', '/dashboard/homepage-sections', 'homepage-sections.store', getUnimplementedCases('/dashboard/homepage-sections'), 'Controller method may not be implemented.');
$body .= ep('DELETE', '/dashboard/homepage-sections/{id}', 'homepage-sections.destroy', getUnimplementedCases('/dashboard/homepage-sections/1'), 'Controller method may not be implemented.');

// ==================== HOMEPAGE STATS ====================
$body .= '<h2>6. Homepage Stats</h2>';
$body .= ep('GET', '/dashboard/homepage-stats', 'homepage-stats.index', getIndexCases('/dashboard/homepage-stats', 'Homepage Stats'));
$body .= ep('GET', '/dashboard/homepage-stats/create', 'homepage-stats.create', [
    ['Test Case 1: Create Form', 'Form loads', ['Navigate to create page'], 'Create stat form displayed.'],
    ['Test Case 2: Unauthorized', '403 without permission', ['Employee without access'], '403 Forbidden.'],
]);
$body .= ep('POST', '/dashboard/homepage-stats', 'homepage-stats.store', [
    ['Test Case 1: Create Stat', 'Add new stat', ['Fill number, label, label_ar, optional icon', 'Submit'], 'Redirect to index: "Section updated successfully." New record in DB.'],
    ['Test Case 2: Invalid Icon', 'Image validation', ['Upload non-image icon', 'Submit'], 'Validation error.'],
    ['Test Case 3: Unauthorized', '403 without permission', ['Unauthorized employee submits'], '403 Forbidden.'],
]);
$body .= ep('PUT/PATCH', '/dashboard/homepage-stats/{id}', 'homepage-stats.update', [
    ['Test Case 1: Update Stat', 'Modify stat', ['Edit values and submit'], 'Success message. DB updated.'],
    ['Test Case 2: Invalid ID', '404', ['Update non-existent ID'], '404 Not Found.'],
]);
$body .= ep('DELETE', '/dashboard/homepage-stats/{id}', 'homepage-stats.destroy', [
    ['Test Case 1: Delete Stat', 'Remove stat', ['Delete a stat and confirm'], 'Flash: "Stat deleted successfully." Record deleted.'],
    ['Test Case 2: Invalid ID', '404', ['Delete invalid ID'], '404 Not Found.'],
]);

// ==================== TESTIMONIALS ====================
$body .= '<h2>7. Testimonials</h2>';
$body .= ep('GET', '/dashboard/testimonials', 'testimonials.index', getIndexCases('/dashboard/testimonials', 'Testimonials'));
$body .= ep('GET', '/dashboard/testimonials/create', 'testimonials.create', [
    ['Test Case 1: Create Form', 'Form loads', ['Open create page'], 'Testimonial create form displayed.'],
    ['Test Case 2: Unauthorized', '403', ['Employee without access'], '403 Forbidden.'],
]);
$body .= ep('POST', '/dashboard/testimonials', 'testimonials.store', [
    ['Test Case 1: Create Testimonial', 'Add testimonial', ['Fill name, title, description fields, optional icon', 'Submit'], 'Redirect: "Section updated successfully." Record created.'],
    ['Test Case 2: Invalid Icon', 'Image validation', ['Upload invalid icon', 'Submit'], 'Validation error.'],
    ['Test Case 3: Unauthorized', '403', ['Unauthorized employee'], '403 Forbidden.'],
]);
$body .= ep('PUT/PATCH', '/dashboard/testimonials/{id}', 'testimonials.update', [
    ['Test Case 1: Update Testimonial', 'Save changes', ['Edit and submit'], 'Success message. DB updated.'],
    ['Test Case 2: Unauthorized', '403', ['Unauthorized employee'], '403 Forbidden.'],
]);
$body .= ep('DELETE', '/dashboard/testimonials/{id}', 'testimonials.destroy', [
    ['Test Case 1: Delete Testimonial', 'Remove record', ['Delete and confirm'], 'Flash: "Testimonial deleted successfully."'],
    ['Test Case 2: Invalid ID', '404', ['Delete invalid ID'], '404 Not Found.'],
]);

// ==================== PARTNER ACHIEVEMENTS ====================
$body .= '<h2>8. Partner Achievements</h2>';
$body .= ep('GET', '/dashboard/partner-achievements', 'partner-achievements.index', getIndexCases('/dashboard/partner-achievements', 'Partner Achievements'));
$body .= ep('GET', '/dashboard/partner-achievements/create', 'partner-achievements.create', [
    ['Test Case 1: Create Form', 'Form loads', ['Open create with type filter if applicable'], 'Create form displayed.'],
    ['Test Case 2: Unauthorized', '403', ['Employee without access'], '403 Forbidden.'],
]);
$body .= ep('POST', '/dashboard/partner-achievements', 'partner-achievements.store', [
    ['Test Case 1: Create Record', 'Add partner/achievement', ['Fill title, description, type, optional icon', 'Submit'], 'Success: "Section updated successfully."'],
    ['Test Case 2: Type Filter Redirect', 'Redirect preserves type', ['Create from partners tab', 'Submit'], 'Redirect back to index with correct type filter.'],
    ['Test Case 3: Unauthorized', '403', ['Unauthorized employee'], '403 Forbidden.'],
]);
$body .= ep('PUT/PATCH', '/dashboard/partner-achievements/{id}', 'partner-achievements.update', [
    ['Test Case 1: Update Record', 'Save changes', ['Edit and submit'], 'Flash: "Stat updated successfully."'],
    ['Test Case 2: Unauthorized', '403', ['Unauthorized employee'], '403 Forbidden.'],
]);
$body .= ep('DELETE', '/dashboard/partner-achievements/{id}', 'partner-achievements.destroy', [
    ['Test Case 1: Delete Record', 'Remove item', ['Delete and confirm'], 'Flash: "Stat deleted successfully."'],
    ['Test Case 2: Invalid ID', '404', ['Delete invalid ID'], '404 Not Found.'],
]);

// ==================== PACKAGES ====================
$body .= '<h2>9. Packages</h2>';
$body .= ep('GET', '/dashboard/packages', 'packages.index', array_merge(getIndexCases('/dashboard/packages', 'Packages'), [
    ['Test Case 5: Filter by Status', 'Status filter works', ['Select status filter and apply'], 'List shows only matching packages.'],
    ['Test Case 6: Sort Options', 'Sorting works', ['Change sort order'], 'Packages re-ordered correctly.'],
]));
$body .= ep('GET', '/dashboard/packages/create', 'packages.create', [
    ['Test Case 1: Create Form', 'Form with features', ['Open create page'], 'Package create form with feature checkboxes.'],
    ['Test Case 2: Unauthorized', '403', ['Employee without access'], '403 Forbidden.'],
]);
$body .= ep('POST', '/dashboard/packages', 'packages.store', [
    ['Test Case 1: Create Package', 'Successful creation', ['Fill name_ar, name_en, prices, status, select features', 'Submit'], 'Flash: "Package created successfully." Package in DB. Passengers emailed.'],
    ['Test Case 2: Required Fields Missing', 'Validation', ['Submit with empty required fields'], 'Validation errors on name_ar, name_en, prices, status.'],
    ['Test Case 3: Invalid Status', 'Status enum', ['Enter status not in 0,1,2,3', 'Submit'], 'Validation error on status.'],
    ['Test Case 4: Invalid Feature ID', 'Feature exists rule', ['Select non-existent feature ID', 'Submit'], 'Validation error on features.'],
    ['Test Case 5: Negative Price', 'Min 0 validation', ['Enter negative price', 'Submit'], 'Validation error on price.'],
    ['Test Case 6: Email Partial Failure', 'Some emails fail', ['Create package when mail server partially fails'], 'Success message may include ":count emails failed to send."'],
    ['Test Case 7: DB Failure Rollback', 'Transaction safety', ['Simulate DB error during create'], 'Flash: "Unable to create package." No partial data.'],
]);
$body .= ep('PUT/PATCH', '/dashboard/packages/{id}', 'packages.update', [
    ['Test Case 1: Update Package', 'Save changes', ['Edit package and submit'], 'Flash: "Package updated successfully." Features synced. Passengers emailed.'],
    ['Test Case 2: Update Failure', 'DB error', ['Cause update failure'], 'Flash: "Unable to update package."'],
    ['Test Case 3: Unauthorized', '403', ['Unauthorized employee'], '403 Forbidden.'],
]);
$body .= ep('DELETE', '/dashboard/packages/{id}', 'packages.destroy', [
    ['Test Case 1: Delete Package', 'Remove package', ['Delete and confirm'], 'Flash: "Package deleted successfully."'],
    ['Test Case 2: Delete Failure', 'DB error', ['Cause delete failure'], 'Flash: "Unable to delete package."'],
    ['Test Case 3: Unauthorized', '403', ['Unauthorized employee'], '403 Forbidden.'],
]);

// ==================== FEATURES ====================
$body .= '<h2>10. Features</h2>';
$body .= ep('GET', '/dashboard/features', 'features.index', getIndexCases('/dashboard/features', 'Features'));
$body .= ep('GET', '/dashboard/features/create', 'features.create', [
    ['Test Case 1: Create Form', 'Form loads', ['Open create page'], 'Feature create form displayed.'],
    ['Test Case 2: Unauthorized', '403', ['Employee without access'], '403 Forbidden.'],
]);
$body .= ep('POST', '/dashboard/features', 'features.store', [
    ['Test Case 1: Create Feature', 'Add feature', ['Fill name_ar, name_en, optional descriptions and service', 'Submit'], 'Flash: "Feature created successfully." Log entry created.'],
    ['Test Case 2: Required Names', 'name_ar and name_en required', ['Submit without names'], 'Validation errors.'],
    ['Test Case 3: Invalid Service', 'service_id exists rule', ['Enter invalid service_id', 'Submit'], 'Validation error on service_id.'],
]);
$body .= ep('GET', '/dashboard/features/{id}', 'features.show', [
    ['Test Case 1: View Feature', 'Detail page', ['Click view on a feature'], 'Feature details displayed.'],
    ['Test Case 2: Invalid ID', '404', ['Visit invalid feature ID'], '404 Not Found.'],
]);
$body .= ep('PUT/PATCH', '/dashboard/features/{id}', 'features.update', [
    ['Test Case 1: Update Feature', 'Save changes', ['Edit and submit'], 'Flash: "Feature updated successfully." Passengers emailed.'],
    ['Test Case 2: Unauthorized', '403', ['Unauthorized employee'], '403 Forbidden.'],
]);
$body .= ep('DELETE', '/dashboard/features/{id}', 'features.destroy', [
    ['Test Case 1: Delete Feature', 'Remove feature', ['Delete and confirm'], 'Flash: "Feature deleted successfully."'],
    ['Test Case 2: Delete Failure', 'DB error', ['Cause failure'], 'Flash: "Unable to delete feature."'],
]);

// ==================== DRIVERS ====================
$body .= '<h2>11. Drivers Management</h2>';
$body .= ep('GET', '/dashboard/drivers', 'drivers.index', getIndexCases('/dashboard/drivers', 'Drivers List'));
$body .= ep('GET', '/dashboard/drivers/{driver}', 'drivers.show', [
    ['Test Case 1: View Driver Profile', 'Driver details', ['Click on approved driver'], 'Driver profile page with tabs/info.'],
    ['Test Case 2: Invalid Driver User', 'Non-driver user', ['Open show URL for passenger user ID'], 'Redirect to drivers.index: "Invalid driver."'],
    ['Test Case 3: Unauthorized', '403', ['Employee without access'], '403 Forbidden.'],
]);
$body .= ep('GET', '/dashboard/drivers/{driver}/edit', 'drivers.edit', [
    ['Test Case 1: Edit Form', 'Driver edit form', ['Click Edit on driver'], 'Form with name, email, phone pre-filled.'],
    ['Test Case 2: Unauthorized', '403', ['Employee without access'], '403 Forbidden.'],
]);
$body .= ep('PUT/PATCH', '/dashboard/drivers/{driver}', 'drivers.update', [
    ['Test Case 1: Update Driver', 'Save driver info', ['Update first name, last name, email, phone', 'Submit'], 'Redirect drivers.index: "Driver updated successfully."'],
    ['Test Case 2: Duplicate Email', 'Unique email rule', ['Enter email used by another user', 'Submit'], 'Validation error on email.'],
    ['Test Case 3: Required Fields', 'All fields required', ['Clear required fields', 'Submit'], 'Validation errors.'],
    ['Test Case 4: Unauthorized', '403', ['Unauthorized employee'], '403 Forbidden.'],
]);
$body .= ep('GET', '/dashboard/driver/packages', 'drivers.packages', getIndexCases('/dashboard/driver/packages', 'Driver Packages'));
$body .= ep('GET', '/dashboard/drivers/{driver}/packages', 'drivers.packagePlans', [
    ['Test Case 1: View Driver Plans', 'Package plans for driver', ['Open package plans for a driver'], 'Driver package assignment page displayed.'],
    ['Test Case 2: Invalid Driver', 'Non-driver rejected', ['Use passenger user ID'], 'Redirect with "Invalid driver."'],
]);
$body .= ep('POST', '/dashboard/drivers/{driver}/packages/assign', 'drivers.assignPackage', [
    ['Test Case 1: Assign Package Successfully', 'Assign paid package', ['Select active package and interval (monthly/yearly)', 'Submit'], 'Flash: "Package assignment updated successfully." Old package moved to history. Driver emailed.'],
    ['Test Case 2: Package Not Available', 'SOON status package', ['Assign package with status SOON', 'Submit'], 'Error: "This package is not available for assignment."'],
    ['Test Case 3: Duplicate Assignment', 'Same package and interval', ['Assign same package/interval driver already has', 'Submit'], 'Error: "User already has this package assigned with the same interval."'],
    ['Test Case 4: Missing package_id', 'Required validation', ['Submit without package_id', 'Submit'], 'Validation error.'],
    ['Test Case 5: Invalid Interval', 'monthly or yearly only', ['Submit invalid interval value', 'Submit'], 'Validation error on interval.'],
    ['Test Case 6: Invalid Driver', 'Non-driver user', ['Assign to passenger user ID'], 'Error: "Invalid driver."'],
    ['Test Case 7: Unauthorized', '403', ['Unauthorized employee'], '403 Forbidden.'],
]);
$body .= ep('POST', '/dashboard/drivers/{driver}/packages/cancel', 'drivers.cancelPackage', [
    ['Test Case 1: Cancel Active Subscription', 'Move to free plan', ['Cancel subscription for driver with active paid package', 'Submit'], 'Flash: "Subscription cancelled and driver moved to free plan successfully." Free package assigned. Driver emailed.'],
    ['Test Case 2: No Active Subscription', 'No package to cancel', ['Cancel for driver with no active subscription', 'Submit'], 'Error: "No active subscription found for this driver."'],
    ['Test Case 3: Already on Free Plan', 'Free subscription active', ['Cancel for driver already on free plan', 'Submit'], 'Error: "Driver is already on the free subscription."'],
    ['Test Case 4: Free Package Not Configured', 'No free package in DB', ['Remove free packages from DB, attempt cancel', 'Submit'], 'Error: "Free package is not configured."'],
    ['Test Case 5: Missing Driver Email', 'Email required for notification', ['Cancel for driver without email', 'Submit'], 'Error: "Driver email is not available."'],
    ['Test Case 6: Email Send Failure', 'Cancel succeeds, email fails', ['Cancel when mail fails', 'Submit'], 'Warning: "Subscription was cancelled but the notification email could not be sent."'],
    ['Test Case 7: Invalid Driver', 'Non-driver', ['Use passenger user ID'], 'Error: "Invalid driver."'],
]);
$body .= ep('GET', '/dashboard/driver/rates', 'drivers.rates', getIndexCases('/dashboard/driver/rates', 'Driver Rates'));
$body .= ep('GET', '/dashboard/driver/trips', 'drivers.trips', getIndexCases('/dashboard/driver/trips', 'All Driver Trips'));
$body .= ep('GET', '/dashboard/drivers/{driver}/trips', 'drivers.driverTrips', [
    ['Test Case 1: Driver Trips List', 'Trips for specific driver', ['Open trips tab for a driver'], 'Driver trips listed.'],
    ['Test Case 2: Invalid Driver', 'Non-driver', ['Use passenger ID'], 'Redirect: "Invalid driver."'],
]);
$body .= ep('GET', '/dashboard/drivers/{driver}/earnings', 'drivers.earnings', [
    ['Test Case 1: View Earnings', 'Driver earnings page', ['Open earnings for driver with trips'], 'Earnings summary displayed.'],
    ['Test Case 2: Invalid Driver', 'Non-driver', ['Use passenger ID'], 'Redirect: "Invalid driver."'],
]);
$body .= ep('POST', '/dashboard/drivers/{driver}/send-payment-reminder', 'drivers.sendPaymentReminder', [
    ['Test Case 1: Send Reminder Successfully', 'Dues exceed 50 SAR', ['Send reminder to driver with dues > 50 SAR'], 'Flash: "Payment reminder sent successfully to driver." Email sent.'],
    ['Test Case 2: Dues Below Threshold', 'Dues ≤ 50 SAR', ['Send reminder when dues ≤ 50'], 'Error: "A reminder cannot be sent because dues do not exceed 50 SAR."'],
    ['Test Case 3: Missing Email', 'No driver email', ['Driver without email'], 'Error: "Driver email is not available."'],
    ['Test Case 4: Invalid Driver', 'Non-driver', ['Use passenger ID'], 'Error: "Invalid driver."'],
    ['Test Case 5: Mail Failure', 'Email send fails', ['Simulate mail failure'], 'Error: "Unable to send payment reminder."'],
]);
$body .= ep('POST', '/dashboard/drivers/{driver}/ban', 'drivers.ban', [
    ['Test Case 1: Ban Driver Successfully', 'Rating below 1', ['Ban driver with rating < 1, enter ban_reason', 'Submit'], 'Redirect drivers.show: "Driver has been banned successfully." DriverBanned record created.'],
    ['Test Case 2: Rating Too High', 'Rating ≥ 1', ['Attempt ban on driver with rating ≥ 1', 'Submit'], 'Error: "Driver cannot be banned. Rating is not below 1."'],
    ['Test Case 3: Missing Ban Reason', 'Required validation', ['Submit without ban_reason'], 'Validation error on ban_reason.'],
    ['Test Case 4: Ban Reason Too Long', 'Max 1000 chars', ['Enter reason > 1000 chars'], 'Validation error.'],
    ['Test Case 5: DB Failure', 'Transaction rollback', ['Simulate DB error'], 'Error: "Unable to ban driver."'],
]);
$body .= ep('POST', '/dashboard/drivers/{driver}/assign-to-admins', 'drivers.assignToAdmin', [
    ['Test Case 1: Assign Successfully', 'Assign driver to admin', ['Select admin, enter assign_note', 'Submit'], 'Flash: "Driver assignment submitted successfully." Assignment record created. Admin emailed.'],
    ['Test Case 2: Missing Fields', 'Required validation', ['Submit without admin or note'], 'Validation errors.'],
    ['Test Case 3: Invalid Admin ID', 'exists:admins rule', ['Select non-existent admin', 'Submit'], 'Validation error on assigned_admin.'],
    ['Test Case 4: Email Failure Warning', 'Assignment saved, email fails', ['Assign when mail fails'], 'Warning: assignment saved but email could not be sent.'],
    ['Test Case 5: Unauthorized', '403', ['Unauthorized employee'], '403 Forbidden.'],
]);
$body .= ep('GET', '/dashboard/drivers/create', 'drivers.create', getUnimplementedCases('/dashboard/drivers/create'));
$body .= ep('POST', '/dashboard/drivers', 'drivers.store', getUnimplementedCases('/dashboard/drivers'));
$body .= ep('DELETE', '/dashboard/drivers/{driver}', 'drivers.destroy', getUnimplementedCases('/dashboard/drivers/1'));

// ==================== NEW DRIVERS ====================
$body .= '<h2>12. New Drivers &amp; Driver Approval</h2>';
$body .= ep('GET', '/dashboard/new-drivers', 'new-drivers.index', getIndexCases('/dashboard/new-drivers', 'New Driver Requests'));
$body .= ep('POST', '/dashboard/drivers/{driver}/update-status', 'drivers.updateStatus', [
    ['Test Case 1: Approve Driver', 'Successful approval', ['Select approval=1 (approved)', 'Submit from new drivers page'], 'Flash: "Driver status updated successfully." Approval updated. Approval email sent. Wasl eligibility verified.'],
    ['Test Case 2: Reject Driver', 'Rejection with reason', ['Select approval=3, enter reject-reason', 'Submit'], 'Driver rejected. Rejection email sent with reason. CaptainRequestDecision logged.'],
    ['Test Case 3: Pending Status', 'Set pending', ['Select approval=2', 'Submit'], 'Status updated to pending. Appropriate notification sent.'],
    ['Test Case 4: Reject Without Reason', 'Reason required for rejection', ['Select approval=3 without reject-reason', 'Submit'], 'Validation error on reject-reason.'],
    ['Test Case 5: Wasl Eligibility Failure', 'Ministry check fails', ['Approve driver failing Wasl eligibility', 'Submit'], 'Error about ministry eligibility. Driver not approved.'],
    ['Test Case 6: Missing Identity Data', 'Wasl prerequisites', ['Approve driver missing required identity fields', 'Submit'], 'Error: unable to verify ministry eligibility.'],
    ['Test Case 7: Unauthorized', '403', ['Unauthorized employee'], '403 Forbidden.'],
]);

// ==================== USERS / TRIPS / RATES ====================
$body .= '<h2>13. Users, Trips &amp; Rates</h2>';
$body .= ep('GET', '/dashboard/users', 'users.index', getIndexCases('/dashboard/users', 'Users'));
$body .= ep('GET', '/dashboard/user/trips', 'users.trips', getIndexCases('/dashboard/user/trips', 'User Trips'));
$body .= ep('GET', '/dashboard/users/{user}', 'users.show', [
    ['Test Case 1: View User', 'User profile page', ['Click on a user/passenger'], 'User details displayed.'],
    ['Test Case 2: Invalid ID', '404', ['Visit invalid user ID'], '404 Not Found.'],
]);
$body .= ep('GET', '/dashboard/user/rates', 'users.rates', getIndexCases('/dashboard/user/rates', 'User Rates'));
$body .= ep('GET', '/dashboard/user/unride-rates', 'users.unride-rates', getIndexCases('/dashboard/user/unride-rates', 'Unride Rates'));
$body .= ep('GET', '/dashboard/passengers/{passenger}/complaints', 'passengers.complaints', [
    ['Test Case 1: View Complaints', 'Passenger complaints list', ['Open complaints for a passenger'], 'Complaint/unride rates listed.'],
    ['Test Case 2: Invalid Passenger', '404 or error', ['Use invalid passenger ID'], '404 or appropriate error.'],
]);
$body .= ep('GET', '/dashboard/users/create', 'users.create', getUnimplementedCases('/dashboard/users/create'));
$body .= ep('POST', '/dashboard/users', 'users.store', getUnimplementedCases('/dashboard/users'));
$body .= ep('PUT/PATCH', '/dashboard/users/{user}', 'users.update', getUnimplementedCases('/dashboard/users/1'));
$body .= ep('DELETE', '/dashboard/users/{user}', 'users.destroy', getUnimplementedCases('/dashboard/users/1'));

// ==================== PASSENGERS ====================
$body .= '<h2>14. Passengers Management</h2>';
$body .= ep('GET', '/dashboard/passengers', 'passengers.index', getIndexCases('/dashboard/passengers', 'Passengers'));
$body .= ep('GET', '/dashboard/passengers/{passenger}', 'passengers.show', [
    ['Test Case 1: View Passenger', 'Passenger profile', ['Click on passenger'], 'Passenger details displayed.'],
    ['Test Case 2: Invalid Passenger', 'Non-passenger user', ['Use driver user ID'], 'Redirect: "Invalid passenger."'],
]);
$body .= ep('GET', '/dashboard/passengers/{passenger}/trips', 'passengers.trips', [
    ['Test Case 1: Passenger Trips', 'Trip history', ['Open trips for passenger'], 'Passenger trips listed.'],
    ['Test Case 2: Invalid Passenger', 'Non-passenger', ['Use driver ID'], 'Error or redirect.'],
]);
$body .= ep('GET', '/dashboard/passengers-trips', 'passengers.all-trips', getIndexCases('/dashboard/passengers-trips', 'All Passenger Trips'));
$body .= ep('GET', '/dashboard/passengers/profile-update-requests', 'passengers.profile-update-requests', getIndexCases('/dashboard/passengers/profile-update-requests', 'Profile Update Requests'));
$body .= ep('POST', '/dashboard/passengers/{passenger}/update-approval', 'passengers.updateApproval', [
    ['Test Case 1: Approve Passenger', 'Set approval=1', ['Select approved status', 'Submit'], 'Flash: "Passenger status updated to approved". Email sent.'],
    ['Test Case 2: Reject Passenger', 'Set approval=3', ['Select rejected status', 'Submit'], 'Status updated to rejected. Email sent.'],
    ['Test Case 3: Pending Status', 'Set approval=2', ['Select pending', 'Submit'], 'Status updated to pending review.'],
    ['Test Case 4: Update Failure', 'DB error', ['Simulate failure'], 'Error: "Unable to update passenger status."'],
    ['Test Case 5: Unauthorized', '403', ['Unauthorized employee'], '403 Forbidden.'],
]);
$body .= ep('POST', '/dashboard/passengers/profile-updates/{newUserInfo}/approve', 'passengers.approve-profile-update', [
    ['Test Case 1: Approve Profile Update', 'Merge pending data', ['Approve pending profile update request', 'Submit'], 'Flash: "Profile update approved successfully." Pending data merged. Request deleted. Email sent.'],
    ['Test Case 2: No Pending Request', 'Nothing to approve', ['Approve when no pending request exists', 'Submit'], 'Error: "No pending profile update found for this passenger."'],
    ['Test Case 3: Approve Failure', 'DB error', ['Simulate failure'], 'Error: "Unable to approve profile update."'],
    ['Test Case 4: Multiple Pending Requests', 'Partial approval', ['Approve one of multiple pending requests', 'Submit'], 'Success message may note other pending requests remain.'],
    ['Test Case 5: Unauthorized', '403', ['Unauthorized employee'], '403 Forbidden.'],
]);
$body .= ep('POST', '/dashboard/passengers/profile-updates/{newUserInfo}/reject', 'passengers.reject-profile-update', [
    ['Test Case 1: Reject Profile Update', 'Reject with reason', ['Enter rejection_reason', 'Submit'], 'Flash: "Profile update rejected successfully." Request removed. Email sent.'],
    ['Test Case 2: Missing Reason', 'Required validation', ['Submit without rejection_reason'], 'Validation error.'],
    ['Test Case 3: No Pending Request', 'Nothing to reject', ['Reject when no pending request'], 'Error: "No pending profile update found for this passenger."'],
    ['Test Case 4: Reject Failure', 'DB error', ['Simulate failure'], 'Error: "Unable to reject profile update."'],
]);
$body .= ep('POST', '/dashboard/passengers/{passenger}/assign-to-admin', 'passengers.assign-to-admin', [
    ['Test Case 1: Assign Request', 'Assign to another admin', ['Select admin, enter assign_note for pending update', 'Submit'], 'Flash: "Passenger request assigned successfully."'],
    ['Test Case 2: No Pending Update', 'Nothing to assign', ['Assign when no pending profile update'], 'Error: "No pending profile update found for this passenger."'],
    ['Test Case 3: Self Assignment', 'Cannot assign to self', ['Select current logged-in admin', 'Submit'], 'Error: "Please select another admin to assign this request."'],
    ['Test Case 4: Missing Fields', 'Validation', ['Submit without admin or note'], 'Validation errors.'],
    ['Test Case 5: Assign Failure', 'DB error', ['Simulate failure'], 'Error: "Unable to assign passenger request."'],
]);
$body .= ep('POST', '/dashboard/passengers/{passenger}/ban', 'passengers.ban', [
    ['Test Case 1: Ban Passenger', 'Successful ban', ['Enter ban_reason', 'Submit'], 'Redirect passengers.show: "Passenger has been banned successfully." approval=3. Ban log created. Email sent.'],
    ['Test Case 2: Missing Reason', 'Required validation', ['Submit without ban_reason'], 'Validation error.'],
    ['Test Case 3: Ban Failure', 'DB error', ['Simulate failure'], 'Error: "Unable to ban passenger."'],
    ['Test Case 4: Unauthorized', '403', ['Unauthorized employee'], '403 Forbidden.'],
]);

// ==================== DRIVER INFO REQUESTS ====================
$body .= '<h2>15. Driver Info Update Requests</h2>';
$body .= ep('GET', '/dashboard/edit-info-request', 'edit-info-request.index', getIndexCases('/dashboard/edit-info-request', 'Driver Info Update Requests'));
$body .= ep('GET', '/dashboard/edit-info-request/{id}', 'edit-info-request.show', [
    ['Test Case 1: View Request', 'Request details with comparison', ['Click on pending driver info request'], 'Show page with current vs requested data and images.'],
    ['Test Case 2: No Pending Request', '404', ['Open ID with no pending NewUserInfo'], '404 Not Found.'],
    ['Test Case 3: Unauthorized', '403', ['Unauthorized employee'], '403 Forbidden.'],
]);
$body .= ep('PUT/PATCH', '/dashboard/edit-info-request/{id}', 'edit-info-request.update', [
    ['Test Case 1: Approve Request', 'Merge driver data', ['Set approval=1', 'Submit'], 'Flash: "Driver updated successfully." User/driver/car data merged. Wasl updated. Pending records deleted.'],
    ['Test Case 2: Reject Request', 'Reject with reason', ['Set approval=3, enter rejection-reason', 'Submit'], 'Flash: "Driver info update request rejected successfully." Pending records deleted. Wasl restored.'],
    ['Test Case 3: Reject Without Reason', 'Reason required', ['Set approval=3 without reason', 'Submit'], 'Validation error on rejection-reason.'],
    ['Test Case 4: Wasl Update Failure', 'Ministry API error', ['Approve when Wasl API fails', 'Submit'], 'Error: "Unable to update driver in ministry system." with exception details.'],
    ['Test Case 5: Unauthorized', '403', ['Unauthorized employee'], '403 Forbidden.'],
]);
$body .= ep('GET', '/dashboard/edit-info-request/create', 'edit-info-request.create', getUnimplementedCases('/dashboard/edit-info-request/create'));
$body .= ep('POST', '/dashboard/edit-info-request', 'edit-info-request.store', getUnimplementedCases('/dashboard/edit-info-request'));
$body .= ep('DELETE', '/dashboard/edit-info-request/{id}', 'edit-info-request.destroy', getUnimplementedCases('/dashboard/edit-info-request/1'));

// ==================== GENERAL DUES ====================
$body .= '<h2>16. General Dues Percentage</h2>';
$body .= ep('GET', '/dashboard/general-dues-percentage', 'general-dues-percentage.show', getIndexCases('/dashboard/general-dues-percentage', 'General Dues Percentage'));
$body .= ep('PATCH', '/dashboard/general-dues-percentage', 'general-dues-percentage.update', [
    ['Test Case 1: Update Percentage', 'Save new cost value', ['Enter valid cost between 0-100', 'Submit'], 'Flash: "General dues percentage updated successfully." Value saved and logged.'],
    ['Test Case 2: Unchanged Value', 'No change submitted', ['Submit same value as current', 'Submit'], 'Flash: "Saved successfully" (no change).'],
    ['Test Case 3: Below Minimum', 'Min 0 validation', ['Enter negative cost', 'Submit'], 'Validation error: minimum 0.'],
    ['Test Case 4: Above Maximum', 'Max 100 validation', ['Enter cost > 100', 'Submit'], 'Validation error: maximum 100.'],
    ['Test Case 5: Non-numeric Value', 'Numeric validation', ['Enter text in cost field', 'Submit'], 'Validation error: must be numeric.'],
    ['Test Case 6: Record Not Found', 'Missing config record', ['Update when no record exists in DB'], 'Error: "General dues percentage record not found."'],
    ['Test Case 7: Unauthorized', '403', ['Unauthorized employee'], '403 Forbidden.'],
]);

// ==================== SUPPORT TICKETS ====================
$body .= '<h2>17. Support Tickets</h2>';
foreach (['complaints', 'inquiries', 'technical'] as $page) {
    $body .= ep('GET', "/dashboard/support-tickets/{$page}", 'support-tickets.index', [
        ['Test Case 1: List Tickets', "View {$page} tickets", ["Login as authorized admin", "Navigate to /dashboard/support-tickets/{$page}"], 'Ticket list for category displayed.'],
        ['Test Case 2: Invalid Page Type', '404 for invalid category', ['Visit /dashboard/support-tickets/invalid'], '404 Not Found.'],
        ['Test Case 3: Unauthorized', '403', ['Employee without access'], '403 Forbidden.'],
        ['Test Case 4: Load Failure Fallback', 'Error handling', ['Simulate DB load failure'], 'Redirect dashboard.index: "Failed to load tickets. Please try again."'],
    ], "Page parameter must be: {$page}");
}
$body .= ep('GET', '/dashboard/support-tickets/{page}/{ticket}', 'support-tickets.show', [
    ['Test Case 1: View Ticket Detail', 'Ticket show page', ['Click on open ticket from list'], 'Ticket details, replies, and attachments displayed.'],
    ['Test Case 2: Wrong Category', 'Ticket type mismatch', ['Open ticket under wrong page category'], '404 Not Found.'],
    ['Test Case 3: Unauthorized', '403', ['Unauthorized employee'], '403 Forbidden.'],
]);
$body .= ep('POST', '/dashboard/support-tickets/{page}/{ticket}/reply', 'support-tickets.reply', [
    ['Test Case 1: Send Reply', 'Successful reply', ['Enter message (required)', 'Optionally attach up to 5 files (jpeg,jpg,png,pdf, max 5MB each)', 'Submit'], 'Flash: "Reply sent successfully." Reply saved. Ticket may move to in-progress.'],
    ['Test Case 2: Reply to Closed Ticket', 'Closed ticket blocked', ['Attempt reply on closed ticket', 'Submit'], 'Error: "Cannot reply to a closed ticket."'],
    ['Test Case 3: Empty Message', 'Required validation', ['Submit without message'], 'Validation error on message.'],
    ['Test Case 4: Invalid Attachment', 'File type/size validation', ['Attach invalid file type or file > 5MB', 'Submit'], 'Validation error on attachments.'],
    ['Test Case 5: Too Many Attachments', 'Max 5 files', ['Attach more than 5 files', 'Submit'], 'Validation error on attachments.'],
    ['Test Case 6: Save Failure', 'DB error', ['Simulate save failure'], 'Error: "Failed to save the reply. Please try again."'],
    ['Test Case 7: Unauthorized', '403', ['Unauthorized employee'], '403 Forbidden.'],
]);
$body .= ep('POST', '/dashboard/support-tickets/{page}/{ticket}/assign', 'support-tickets.assign', [
    ['Test Case 1: Assign Ticket', 'Assign to employee', ['Select active admin/support employee', 'Submit'], 'Flash: "Ticket assigned successfully." Assignment saved.'],
    ['Test Case 2: Missing Employee', 'Required validation', ['Submit without assigned_employee_id'], 'Validation error.'],
    ['Test Case 3: Invalid Employee', 'Must exist and be active', ['Select inactive/non-existent employee', 'Submit'], 'Validation error.'],
    ['Test Case 4: Assign Failure', 'DB error', ['Simulate failure'], 'Error: "Failed to assign the ticket. Please try again."'],
]);
$body .= ep('POST', '/dashboard/support-tickets/{page}/{ticket}/close', 'support-tickets.close', [
    ['Test Case 1: Close Ticket', 'Close after reply', ['Reply to ticket first', 'Click Close'], 'Flash: "Ticket closed successfully." Status set to closed.'],
    ['Test Case 2: Already Closed', 'Duplicate close', ['Close an already closed ticket'], 'Error: "Ticket is already closed."'],
    ['Test Case 3: Close Before Reply', 'Reply required', ['Close ticket with no employee reply'], 'Error: "Cannot close the ticket before sending a reply."'],
    ['Test Case 4: Close Failure', 'DB error', ['Simulate failure'], 'Error: "Failed to close the ticket. Please try again."'],
]);

// ==================== ANNOUNCEMENTS ====================
$body .= '<h2>18. Announcements</h2>';
$body .= ep('GET', '/dashboard/announcements', 'announcements.index', getIndexCases('/dashboard/announcements', 'Announcements'));
$body .= ep('GET', '/dashboard/announcements/create', 'announcements.create', [
    ['Test Case 1: Create Form', 'Form loads', ['Open create page'], 'Announcement create form with Arabic/English fields and target_app selector.'],
    ['Test Case 2: Unauthorized', '403', ['Employee without access'], '403 Forbidden.'],
]);
$body .= ep('POST', '/dashboard/announcements', 'announcements.store', [
    ['Test Case 1: Create for Passengers', 'target_app=passengers', ['Fill all required fields, select passengers', 'Submit'], 'Flash: "Announcement created successfully." Record in Announce table.'],
    ['Test Case 2: Create for Drivers', 'target_app=drivers', ['Select drivers target', 'Submit'], 'Record in DriverAnnounce table.'],
    ['Test Case 3: Create for Both', 'target_app=both', ['Select both', 'Submit'], 'Records in both tables.'],
    ['Test Case 4: Missing Required Fields', 'Validation', ['Submit with empty title/content fields'], 'Validation errors.'],
    ['Test Case 5: Invalid target_app', 'Enum validation', ['Submit invalid target_app value'], 'Validation error.'],
    ['Test Case 6: Unauthorized', '403', ['Unauthorized employee'], '403 Forbidden.'],
]);
$body .= ep('DELETE', '/dashboard/announcements/{source}/{id}', 'announcements.destroy', [
    ['Test Case 1: Delete Announcement', 'Remove with reason', ['Enter deletion reason (required)', 'Confirm delete'], 'Flash: "Announcement deleted successfully." Action log created.'],
    ['Test Case 2: Missing Reason', 'Required validation', ['Delete without reason'], 'Validation error on reason.'],
    ['Test Case 3: Invalid Source', 'Invalid source param', ['Use invalid source value in URL'], '404 Not Found.'],
    ['Test Case 4: Record Not Found', 'Invalid ID', ['Delete non-existent announcement ID'], '404 Not Found.'],
]);

// ==================== UNIVERSITIES ====================
$body .= '<h2>19. Universities</h2>';
$body .= ep('GET', '/dashboard/universities', 'universities.index', getIndexCases('/dashboard/universities', 'Universities'));
$body .= ep('GET', '/dashboard/universities/create', 'universities.create', [
    ['Test Case 1: Create Form', 'Form loads', ['Open create page'], 'University create form with city and services selection.'],
    ['Test Case 2: Unauthorized', '403', ['Employee without access'], '403 Forbidden.'],
]);
$body .= ep('POST', '/dashboard/universities', 'universities.store', [
    ['Test Case 1: Create University', 'Add university', ['Fill name-ar, name-eng, city_id, optional location/lat/lng/services', 'Submit'], 'Flash: "University created successfully." University and service links created.'],
    ['Test Case 2: Missing Required Fields', 'Validation', ['Submit without names or city_id'], 'Validation errors.'],
    ['Test Case 3: Invalid City', 'city_id exists rule', ['Select non-existent city', 'Submit'], 'Validation error on city_id.'],
    ['Test Case 4: Invalid Service', 'service_ids exists rule', ['Select invalid service ID', 'Submit'], 'Validation error on service_ids.'],
    ['Test Case 5: Unauthorized', '403', ['Unauthorized employee'], '403 Forbidden.'],
]);
$body .= ep('GET', '/dashboard/universities/{university}/services', 'universities.services', [
    ['Test Case 1: View Services', 'University services page', ['Open services for a university'], 'Current linked services displayed with selection form.'],
    ['Test Case 2: Invalid University', '404', ['Use invalid university ID'], '404 Not Found.'],
]);
$body .= ep('POST', '/dashboard/universities/{university}/services', 'universities.services.store', [
    ['Test Case 1: Update Services', 'Link services', ['Select one or more services', 'Submit'], 'Flash: "University services updated successfully." New links created.'],
    ['Test Case 2: Empty Selection', 'Min 1 service required', ['Submit with no services selected'], 'Validation error: service_ids min 1.'],
    ['Test Case 3: Invalid Service ID', 'exists rule', ['Submit invalid service ID'], 'Validation error.'],
]);
$body .= ep('DELETE', '/dashboard/universities/{university}', 'universities.destroy', [
    ['Test Case 1: Delete University', 'Remove with reason', ['Enter reason, confirm delete'], 'Flash: "University deleted successfully."'],
    ['Test Case 2: Linked Users Block Delete', 'Users associated', ['Delete university with linked users', 'Submit'], 'Error: "Cannot delete university because users are linked to it."'],
    ['Test Case 3: Missing Reason', 'Required validation', ['Delete without reason'], 'Validation error.'],
]);

// ==================== CITIES ====================
$body .= '<h2>20. Cities &amp; Neighborhoods</h2>';
$body .= ep('GET', '/dashboard/cities', 'cities.index', getIndexCases('/dashboard/cities', 'Cities'));
$body .= ep('GET', '/dashboard/cities/create', 'cities.create', [
    ['Test Case 1: Create Form', 'Form loads', ['Open create page'], 'City create form with neighborhood repeater.'],
    ['Test Case 2: Unauthorized', '403', ['Employee without access'], '403 Forbidden.'],
]);
$body .= ep('POST', '/dashboard/cities', 'cities.store', [
    ['Test Case 1: Create City with Neighborhoods', 'Add city and neighborhoods', ['Fill city-ar, city-en, add neighborhoods', 'Submit'], 'Flash: "City and neighborhoods created successfully."'],
    ['Test Case 2: Duplicate City Name', 'Unique name check', ['Create city with existing name', 'Submit'], 'Error: "A city with the same name already exists."'],
    ['Test Case 3: Missing City Names', 'Required validation', ['Submit without city-ar/city-en'], 'Validation errors.'],
    ['Test Case 4: Neighborhood Names Required', 'When neighborhoods provided', ['Add neighborhood row without ar/eng names', 'Submit'], 'Validation errors on neighborhood fields.'],
]);
$body .= ep('POST', '/dashboard/cities/{city}/neighborhoods', 'cities.neighborhoods.store', [
    ['Test Case 1: Add Neighborhood', 'Add to existing city', ['Fill neighborhood-ar and neighborhood-eng', 'Submit'], 'Flash: "Neighborhood added successfully."'],
    ['Test Case 2: Duplicate Neighborhood', 'Same name in city', ['Add neighborhood that already exists in city', 'Submit'], 'Error: "This neighborhood already exists in the selected city."'],
    ['Test Case 3: Missing Names', 'Required validation', ['Submit empty names'], 'Validation errors.'],
]);
$body .= ep('PUT', '/dashboard/neighborhoods/{neighborhood}', 'neighborhoods.update', [
    ['Test Case 1: Update Neighborhood', 'Save changes', ['Edit neighborhood names', 'Submit'], 'Flash: "Neighborhood updated successfully."'],
    ['Test Case 2: Duplicate Name', 'Unique in city', ['Rename to existing neighborhood name', 'Submit'], 'Duplicate error.'],
    ['Test Case 3: Missing Names', 'Required validation', ['Clear required fields', 'Submit'], 'Validation errors.'],
]);
$body .= ep('DELETE', '/dashboard/neighborhoods/{neighborhood}', 'neighborhoods.destroy', [
    ['Test Case 1: Delete Neighborhood', 'Remove with reason', ['Enter reason, confirm delete'], 'Flash: "Neighborhood deleted successfully."'],
    ['Test Case 2: Linked Drivers Block', 'Drivers associated', ['Delete neighborhood with linked drivers'], 'Error: "Cannot delete neighborhood because drivers are linked to it."'],
    ['Test Case 3: Missing Reason', 'Required validation', ['Delete without reason'], 'Validation error.'],
]);

// ==================== DELIVERY SERVICES ====================
$body .= '<h2>21. Delivery Services</h2>';
$body .= ep('GET', '/dashboard/delivery-services', 'delivery-services.index', getIndexCases('/dashboard/delivery-services', 'Delivery Services'));
$body .= ep('GET', '/dashboard/delivery-services/{id}/edit', 'delivery-services.edit', [
    ['Test Case 1: Edit Form', 'Form loads', ['Click edit on a service'], 'Edit form with service names, cost, road-way.'],
    ['Test Case 2: Invalid ID', '404', ['Use invalid service ID'], '404 Not Found.'],
]);
$body .= ep('PUT', '/dashboard/delivery-services/{id}', 'delivery-services.update', [
    ['Test Case 1: Update Service', 'Save changes', ['Update service, service-ar, service-eng, cost, road-way', 'Submit'], 'Flash: "Service updated successfully." Action log created.'],
    ['Test Case 2: Missing Required Fields', 'Validation', ['Clear required fields', 'Submit'], 'Validation errors on service names and cost.'],
    ['Test Case 3: Negative Cost', 'Min 0 validation', ['Enter negative cost', 'Submit'], 'Validation error on cost.'],
    ['Test Case 4: Unauthorized', '403', ['Unauthorized employee'], '403 Forbidden.'],
]);

// ==================== DOCUMENTS ====================
$body .= '<h2>22. Documents</h2>';
$body .= ep('GET', '/dashboard/documents', 'documents.index', getIndexCases('/dashboard/documents', 'Documents'));
$body .= ep('GET', '/dashboard/documents/{document}/download', 'documents.download', [
    ['Test Case 1: Download Document', 'File download', ['Click download on existing document'], 'PDF file downloads successfully.'],
    ['Test Case 2: Missing File', 'File not on disk', ['Download document with missing file path'], '404: "Sorry, the link is invalid or the document does not exist."'],
    ['Test Case 3: Unauthorized', '403', ['Unauthorized employee'], '403 Forbidden.'],
]);
$body .= ep('POST', '/dashboard/documents/{document}/replace', 'documents.replace', [
    ['Test Case 1: Replace Document', 'Upload new PDF', ['Select valid PDF file (max 10MB)', 'Submit replace form'], 'Flash: "Document replaced successfully." File updated. All users emailed.'],
    ['Test Case 2: Invalid File Type', 'PDF only', ['Upload non-PDF file', 'Submit'], 'Validation error on file (mimes:pdf).'],
    ['Test Case 3: File Too Large', 'Max 10MB', ['Upload file > 10MB', 'Submit'], 'Validation error on file size.'],
    ['Test Case 4: Missing File', 'Required validation', ['Submit without file'], 'Validation error on file.'],
    ['Test Case 5: Partial Email Failure', 'Some emails fail', ['Replace when some mail sends fail'], 'Warning: document updated but some notification emails failed.'],
    ['Test Case 6: Unauthorized', '403', ['Unauthorized employee'], '403 Forbidden.'],
]);

// ==================== EMPLOYEES ====================
$body .= '<h2>23. Employees (Company Admin Only)</h2>';
$body .= ep('GET', '/dashboard/employees', 'employees.index', [
    ['Test Case 1: Company Admin Access', 'List employees', ['Login as company admin', 'Navigate to /dashboard/employees'], 'Employees list displayed.'],
    ['Test Case 2: Non-Company Admin Denied', 'Agent/supervisor blocked', ['Login as employee (not company admin)', 'Navigate to /dashboard/employees'], '403: "Only company administrators can access this section."'],
    ['Test Case 3: Unauthenticated', 'Guest blocked', ['Access without login'], 'Redirect to login.'],
]);
$body .= ep('GET', '/dashboard/employees/create', 'employees.create', [
    ['Test Case 1: Create Form', 'Form loads', ['Company admin opens create page'], 'Employee create form with role and password fields.'],
    ['Test Case 2: Non-Company Admin', '403', ['Non-company admin accesses URL'], '403 Forbidden.'],
]);
$body .= ep('POST', '/dashboard/employees', 'employees.store', [
    ['Test Case 1: Create Employee', 'Add new employee', ['Fill name, email, password, password_confirmation, role, is_active', 'Submit'], 'Flash: "Employee created successfully." Admin record created.'],
    ['Test Case 2: Duplicate Email', 'Unique email', ['Use existing admin email', 'Submit'], 'Validation error on email.'],
    ['Test Case 3: Password Too Short', 'Min 8 chars', ['Enter password with 7 chars', 'Submit'], 'Validation error on password.'],
    ['Test Case 4: Password Confirmation', 'Must match', ['Enter mismatched password confirmation', 'Submit'], 'Validation error on password confirmation.'],
    ['Test Case 5: Invalid Role', 'agent/supervisor/admin only', ['Enter invalid role', 'Submit'], 'Validation error on role.'],
    ['Test Case 6: No Permissions Warning', 'Employee without permissions', ['Create employee without assigning permissions/pages'], 'Success with warning about view-only access.'],
    ['Test Case 7: Non-Company Admin', '403', ['Non-company admin submits form'], '403 Forbidden.'],
]);
$body .= ep('PUT', '/dashboard/employees/{employee}', 'employees.update', [
    ['Test Case 1: Update Employee', 'Save changes', ['Edit name, email, role, optional new password', 'Submit'], 'Flash: "Employee updated successfully."'],
    ['Test Case 2: Duplicate Email', 'Unique ignoring self', ['Change email to another admin\'s email', 'Submit'], 'Validation error on email.'],
    ['Test Case 3: Non-Company Admin', '403', ['Non-company admin'], '403 Forbidden.'],
]);
$body .= ep('GET', '/dashboard/employees/{employee}/permissions', 'employees.permissions.edit', [
    ['Test Case 1: Permissions Form', 'Form loads', ['Open permissions edit for employee'], 'Permission checkboxes displayed.'],
    ['Test Case 2: Non-Company Admin', '403', ['Non-company admin'], '403 Forbidden.'],
]);
$body .= ep('PUT', '/dashboard/employees/{employee}/permissions', 'employees.permissions.update', [
    ['Test Case 1: Update Permissions', 'Sync permissions', ['Select permissions and submit'], 'Flash: "Employee permissions updated successfully."'],
    ['Test Case 2: No Changes', 'Unchanged selection', ['Submit without changing permissions'], 'Warning: "No permission changes were made."'],
    ['Test Case 3: Empty Permissions', 'No permissions selected', ['Clear all permissions and submit'], 'Warning: employee saved without permissions, view-only on assigned pages.'],
    ['Test Case 4: Non-Company Admin', '403', ['Non-company admin'], '403 Forbidden.'],
]);
$body .= ep('GET', '/dashboard/employees/{employee}/pages', 'employees.pages.edit', [
    ['Test Case 1: Pages Form', 'Form loads', ['Open pages edit for employee'], 'Web pages checkboxes displayed.'],
    ['Test Case 2: Non-Company Admin', '403', ['Non-company admin'], '403 Forbidden.'],
]);
$body .= ep('PUT', '/dashboard/employees/{employee}/pages', 'employees.pages.update', [
    ['Test Case 1: Update Pages', 'Sync page access', ['Select pages and submit'], 'Flash: "Employee pages updated successfully."'],
    ['Test Case 2: No Changes', 'Unchanged selection', ['Submit without changes'], 'Warning about no changes.'],
    ['Test Case 3: Empty Pages', 'No pages selected', ['Clear all pages'], 'Warning: employee has no page access.'],
    ['Test Case 4: Non-Company Admin', '403', ['Non-company admin'], '403 Forbidden.'],
]);

// ==================== LOGS ====================
$body .= '<h2>24. Logs Management (Company Admin Only)</h2>';
$body .= ep('GET', '/dashboard/logs', 'logs.index', [
    ['Test Case 1: View All Logs', 'Logs dashboard', ['Login as company admin', 'Open /dashboard/logs'], 'Log tables displayed (up to 50 rows each) with filters.'],
    ['Test Case 2: Non-Company Admin Denied', '403', ['Login as non-company admin', 'Open /dashboard/logs'], '403: "Only company administrators can access this section."'],
    ['Test Case 3: Filter Logs', 'Apply filters', ['Use available table/date filters', 'Submit'], 'Log list filtered accordingly.'],
    ['Test Case 4: Unauthenticated', 'Guest blocked', ['Access without login'], 'Redirect to login.'],
]);
$body .= ep('GET', '/dashboard/logs/{table}/{id}', 'logs.show', [
    ['Test Case 1: View Log Detail', 'Single log record', ['Click on a log entry from index'], 'Log detail page with full record data.'],
    ['Test Case 2: Unknown Table', 'Invalid table name', ['Visit /dashboard/logs/invalid_table/1'], '404 Not Found.'],
    ['Test Case 3: Missing Record', 'Invalid ID', ['Visit valid table with non-existent ID'], '404: "Log record is not available."'],
    ['Test Case 4: Non-Company Admin', '403', ['Non-company admin'], '403 Forbidden.'],
]);

$html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Atariqi Dashboard - Manual Test Cases</title>
<style>
body { font-family: Arial, sans-serif; line-height: 1.5; max-width: 920px; margin: 40px auto; color: #222; }
h1 { color: #1a365d; border-bottom: 3px solid #1a365d; padding-bottom: 8px; }
h2 { color: #2c5282; margin-top: 44px; border-bottom: 1px solid #ccc; padding-bottom: 6px; page-break-before: auto; }
h3 { color: #2d3748; margin-top: 28px; background: #f7fafc; padding: 10px; border-left: 4px solid #4299e1; }
h4 { color: #4a5568; margin-top: 16px; }
table { border-collapse: collapse; width: 100%; margin: 16px 0; }
th, td { border: 1px solid #ccc; padding: 8px; text-align: left; vertical-align: top; }
th { background: #edf2f7; }
hr { border: none; border-top: 1px solid #e2e8f0; margin: 16px 0; }
.note { background: #fffbeb; border-left: 4px solid #f59e0b; padding: 12px; margin: 16px 0; }
code { background: #edf2f7; padding: 2px 6px; border-radius: 3px; }
@media print { h2 { page-break-before: always; } h2:first-of-type { page-break-before: avoid; } }
</style>
</head>
<body>
<h1>Atariqi Dashboard — Manual Test Cases</h1>
<p><strong>Version:</strong> 1.0 &nbsp;|&nbsp; <strong>Date:</strong> August 2026 &nbsp;|&nbsp; <strong>Type:</strong> Manual testing (no automation)</p>
<p>Complete manual test case document for all Dashboard web endpoints under <code>/dashboard/*</code>. Each endpoint includes up to 7 test cases covering success paths, validation, authorization, and error handling.</p>

<h2>Test Prerequisites</h2>
<table>
<tr><th>Item</th><th>Details</th></tr>
<tr><td>Base URL</td><td>Application URL (e.g. https://atariqi.test)</td></tr>
<tr><td>Company Admin</td><td>Admin with role or type = admin (full access)</td></tr>
<tr><td>Employee Admin</td><td>Admin with role = agent/supervisor, assigned pages and permissions</td></tr>
<tr><td>Inactive Admin</td><td>Admin with is_active = 0</td></tr>
<tr><td>Test Driver</td><td>Approved driver with active subscription and email</td></tr>
<tr><td>Test Passenger</td><td>Approved passenger, optionally with pending profile update</td></tr>
<tr><td>Support Ticket</td><td>Open ticket in complaints, inquiries, or technical</td></tr>
<tr><td>Free Package</td><td>Active free package configured in packages table</td></tr>
</table>

<div class="note">
<strong>Authorization notes:</strong><br>
• All dashboard routes require admin login except <code>/dashboard/login</code>.<br>
• Most routes require page assignment + view permission (except dashboard.index, logout, profile, language).<br>
• Employees &amp; Logs routes require <code>company.admin</code> middleware.<br>
• Some resource routes (create/store/destroy) are registered but controller methods may not be implemented — test cases note expected behavior.
</div>

<h2>Table of Contents</h2>
<ol>
<li>Authentication</li><li>Dashboard Home</li><li>Profile</li><li>Settings &amp; Language</li>
<li>Homepage Sections</li><li>Homepage Stats</li><li>Testimonials</li><li>Partner Achievements</li>
<li>Packages</li><li>Features</li><li>Drivers Management</li><li>New Drivers &amp; Driver Approval</li>
<li>Users, Trips &amp; Rates</li><li>Passengers Management</li><li>Driver Info Update Requests</li>
<li>General Dues Percentage</li><li>Support Tickets</li><li>Announcements</li><li>Universities</li>
<li>Cities &amp; Neighborhoods</li><li>Delivery Services</li><li>Documents</li>
<li>Employees (Company Admin Only)</li><li>Logs Management (Company Admin Only)</li>
</ol>

{$body}

<p style="margin-top:60px;color:#718096;font-size:0.9em;">— End of Document —</p>
</body>
</html>
HTML;

file_put_contents($output, $html);
echo "Generated: {$output}\n";
echo 'Size: ' . number_format(strlen($html)) . " bytes\n";

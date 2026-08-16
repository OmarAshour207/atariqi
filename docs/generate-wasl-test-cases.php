<?php

require __DIR__ . '/test-case-helpers.php';

$body = '';

// ==================== OVERVIEW ====================
$body .= '<h2>1. WASL Integration Overview</h2>';
$body .= '<p>Atariqi integrates with the Saudi Ministry of Transport WASL dispatching platform (Rabet API). Integration is controlled by <code>WASL_ENABLED</code> in <code>.env</code>. All WASL API calls are logged to <code>storage/logs/wasl/</code>.</p>';
$body .= '<table><tr><th>WASL Service</th><th>Internal Method</th><th>External API</th></tr>';
$body .= '<tr><td>5.1 Driver Registration</td><td>WaslService::registerDriver()</td><td>POST /api/dispatching/v2/drivers</td></tr>';
$body .= '<tr><td>5.2 Driver Eligibility</td><td>WaslService::checkDriverEligibility()</td><td>POST /api/dispatching/v2/drivers/eligibility</td></tr>';
$body .= '<tr><td>5.4 Trip Registration</td><td>WaslService::storeTrip()</td><td>POST /api/dispatching/v2/trips</td></tr>';
$body .= '<tr><td>5.6 Current Location</td><td>WaslService::updateCurrentLocations()</td><td>POST /api/dispatching/v2/locations</td></tr>';
$body .= '<tr><td>5.7 Province Inquiry</td><td>WaslService::inquireProvinces()</td><td>GET /api/dispatching/v2/trips/province-inquiry</td></tr>';
$body .= '</table>';

// ==================== DRIVER REGISTRATION API ====================
$body .= '<h2>2. Driver Registration (WASL 5.1)</h2>';
$body .= ep('POST', 'api/driver/register', 'Driver App Registration', [
    ['Test Case 1: Successful Registration with WASL', 'New driver registered locally and sent to WASL', ['Submit complete driver registration payload with valid identity_number, sequence_number, car details, and images', 'Ensure WASL_ENABLED=true and valid API credentials', 'Check response and logs'], 'Returns 200 with success message about registration review. User, driver_info, driver_car, and free package created. WASL registration API called. Entry logged in storage/logs/wasl/.'],
    ['Test Case 2: WASL Rejection During Registration', 'WASL returns INVALID eligibility', ['Register driver with data that fails WASL validation (e.g. expired license, invalid vehicle)', 'Submit registration'], 'Transaction rolled back OR error returned with translated WASL rejection message (e.g. "Driver license is expired"). No partial user record if exception thrown before commit.'],
    ['Test Case 3: Duplicate Driver/Vehicle on WASL', 'DRIVER_VEHICLE_DUPLICATE code', ['Register driver already registered in WASL', 'Submit registration'], 'Error: "Driver or vehicle already registered". Registration fails.'],
    ['Test Case 4: WASL Disabled', 'Integration turned off', ['Set WASL_ENABLED=false in .env', 'Submit valid driver registration'], 'Local registration succeeds. WASL API call skipped (no external request). Driver approval remains pending (0).'],
    ['Test Case 5: Missing Identity Number', 'Required for WASL', ['Attempt registration without identity_number field', 'Submit'], 'Validation error 422 on identity_number (required, unique).'],
    ['Test Case 6: Missing Sequence Number', 'Required for vehicle', ['Submit registration without sequence_number', 'Submit'], 'Validation error 422 on sequence_number.'],
    ['Test Case 7: WASL API Unreachable', 'Network/API failure', ['Simulate WASL API timeout or 500 error during registration', 'Submit valid registration'], 'Registration fails with exception message. DB transaction rolled back. Error logged to wasl channel.'],
], 'Triggered automatically after successful local driver creation via RegisterController.');

// ==================== ELIGIBILITY CHECK ====================
$body .= '<h2>3. Driver Eligibility Check (WASL 5.2)</h2>';
$body .= ep('Internal', 'WaslService::checkDriverEligibility()', 'WASL Eligibility API', [
    ['Test Case 1: Valid Eligible Driver', 'Driver and vehicle eligible', ['Use approved driver with valid identity_number in ministry system', 'Trigger eligibility check from dashboard driver show page or approval flow'], 'Response parsed as is_valid=true. display_status="Valid". Driver and vehicle expiry dates shown if returned.'],
    ['Test Case 2: Invalid Driver — Expired License', 'DRIVER_LICENSE_EXPIRED', ['Use driver known to have expired license in WASL', 'Run eligibility check'], 'is_valid=false. Message: "Driver license is expired". Rejection reason translated to Arabic when locale=ar.'],
    ['Test Case 3: Invalid Vehicle — Old Model', 'OLD_VEHICLE_MODEL', ['Use driver with vehicle older than 5 years', 'Run eligibility check'], 'is_valid=false. Message includes "Vehicle model is older than 5 years".'],
    ['Test Case 4: Criminal Record Check Failed', 'DRIVER_FAILED_CRIMINAL_RECORD_CHECK', ['Use driver failing criminal record check', 'Run eligibility check'], 'is_valid=false. Appropriate translated rejection message displayed.'],
    ['Test Case 5: Missing Identity Number', 'Cannot check without ID', ['Attempt eligibility check for driver without identity_number', 'Trigger check'], 'Returns null/empty or error: "Unable to verify ministry eligibility because the driver identity number is missing."'],
    ['Test Case 6: WASL API Error Response', 'API returns error', ['Simulate WASL API returning non-200 or error payload', 'Run eligibility check'], 'api_error=true. display_status="Verification Failed". Error message from resultMsg shown.'],
    ['Test Case 7: Multiple Rejection Reasons', 'Multiple codes returned', ['Use driver with multiple WASL rejection reasons', 'Run eligibility check'], 'All reasons translated and joined with " | " in message field. is_valid=false.'],
], 'Not a public API endpoint — invoked internally by dashboard and scheduled jobs.');

$body .= ep('POST', 'dashboard/drivers/{driver}/update-status', 'dashboard — Approve Driver (uses WASL eligibility)', [
    ['Test Case 1: Approve Eligible Driver', 'Successful approval after WASL check', ['Login as admin', 'Open new driver with valid WASL eligibility', 'Set approval=1 and submit'], 'Flash: "Driver status updated successfully." Driver approval updated to 1. Captain approval email sent.'],
    ['Test Case 2: Block Approval — Invalid Eligibility', 'WASL says not valid', ['Attempt to approve driver failing WASL eligibility', 'Set approval=1 and submit'], 'Redirect back with error: "Cannot approve this driver because ministry eligibility is not valid." plus WASL reasons. Approval NOT changed.'],
    ['Test Case 3: Block Approval — Missing Identity', 'No identity number', ['Approve driver without identity_number', 'Submit approval=1'], 'Error: "Unable to verify ministry eligibility because the driver identity number is missing."'],
    ['Test Case 4: Block Approval — API Error', 'WASL unreachable', ['Simulate WASL API failure during approval', 'Submit approval=1'], 'Error: "Unable to verify ministry eligibility. Please try again." Approval NOT changed.'],
    ['Test Case 5: Reject Driver (No WASL Check)', 'Rejection with reason', ['Set approval=3, enter reject-reason', 'Submit'], 'Driver rejected. DriverBanned record created. Rejection email sent. WASL eligibility check NOT required for rejection.'],
    ['Test Case 6: Pending Status', 'Set to pending review', ['Set approval=2', 'Submit'], 'Driver status updated to pending (2). No WASL check required.'],
    ['Test Case 7: Reject Without Reason', 'Validation error', ['Set approval=3 without reject-reason', 'Submit'], 'Validation error on reject-reason field. No status change.'],
]);

$body .= ep('GET', 'dashboard/drivers/{driver}', 'dashboard — View WASL Status on Driver Profile', [
    ['Test Case 1: Display Valid WASL Status', 'Eligible driver info shown', ['Open approved driver profile with valid WASL data'], 'Green alert showing Valid status. Driver and vehicle expiry dates displayed if available.'],
    ['Test Case 2: Display Invalid WASL Status', 'Ineligible driver info', ['Open pending driver with invalid WASL eligibility'], 'Red alert with rejection reasons. WASL status fields populated in driver info tab.'],
    ['Test Case 3: Unknown/Unavailable Status', 'WASL check not run', ['Open driver before eligibility check'], 'Status shows Unknown or Verification Failed as appropriate.'],
]);

// ==================== DRIVER INFO UPDATE ====================
$body .= ep('PUT/PATCH', 'dashboard/edit-info-request/{id}', 'dashboard — Approve Driver Info Update (WASL re-sync)', [
    ['Test Case 1: Approve Update — WASL Sync Success', 'Merge data and re-register', ['Open pending driver info update request', 'Set approval=1', 'Submit'], 'Flash: "Driver updated successfully." Local data merged. WASL registerDriver called. Pending records deleted.'],
    ['Test Case 2: Approve Update — WASL Failure', 'Ministry registration fails', ['Approve update when WASL API rejects new data', 'Submit approval=1'], 'Error: "Unable to update driver in ministry system." with exception details. Transaction rolled back.'],
    ['Test Case 3: Reject Update', 'Reject with reason', ['Set approval=3, enter rejection-reason', 'Submit'], 'Flash: "Driver info update request rejected successfully." WASL registerDriver called to restore original data. Pending records deleted.'],
    ['Test Case 4: Reject Without Reason', 'Validation', ['Set approval=3 without reason', 'Submit'], 'Validation error on rejection-reason.'],
    ['Test Case 5: Missing Pending Request', '404', ['Open invalid edit-info-request ID'], '404 Not Found.'],
]);

// ==================== TRIP REGISTRATION ====================
$body .= '<h2>4. Trip Registration (WASL 5.4)</h2>';
$body .= ep('POST', 'api/drivers/rate', 'Passenger Rates Driver (triggers WASL trip store)', [
    ['Test Case 1: Successful Trip Registration', 'Completed trip sent to WASL', ['Complete an immediate/daily/weekly trip', 'Passenger submits rating via api/drivers/rate with valid trip_id, type, and rate', 'Check wasl logs'], 'Rating saved locally. storeTrip() called. WASL POST /api/dispatching/v2/trips succeeds. Trip data logged in storage/logs/wasl/. Response: "Updated successfully".'],
    ['Test Case 2: Trip Not Found', 'No matching ride record', ['Submit rating with invalid trip_id', 'Submit'], 'Rating may fail or trip registration skipped. No WASL call for missing trip.'],
    ['Test Case 3: WASL Trip Registration Failure', 'WASL rejects trip data', ['Rate a trip with incomplete WASL-required data (missing province, invalid timestamps)', 'Submit rating'], 'Rating saved locally. WASL error logged but rating response still returns success (error caught silently in ImmediateDriverController). Check storage/logs/wasl/ and laravel.log.'],
    ['Test Case 4: Provinces Not Synced', 'Missing provinceId resolution', ['Attempt trip registration when wasl_provinces table is empty', 'Complete and rate a trip'], 'WASL error: "WASL provinces are not synced. Run: php artisan wasl:sync-provinces" OR province resolution failure logged.'],
    ['Test Case 5: WASL Disabled', 'Integration off', ['Set WASL_ENABLED=false', 'Rate a completed trip'], 'Rating saved. storeTrip() returns null immediately. No WASL API call.'],
    ['Test Case 6: Invalid Trip Type', 'Unsupported type parameter', ['Submit rating with invalid type value', 'Submit'], 'Appropriate validation or lookup error.'],
    ['Test Case 7: Verify Trip Payload Fields', 'Data integrity', ['Complete trip with known origin/destination cities', 'Rate trip and inspect wasl log payload'], 'Payload includes driverIdentityNumber, vehicleSequenceNumber, timestamps, coordinates, and trip cost fields per UpdateTripDataResource.'],
], 'WASL trip registration is triggered after passenger rates the driver, not at trip start.');

// ==================== LOCATION SYNC ====================
$body .= '<h2>5. Driver Location Sync (WASL 5.6)</h2>';
$body .= ep('POST', 'api/driver/location/update', 'Driver App — Update Location (feeds WASL sync)', [
    ['Test Case 1: Update Location Successfully', 'Store driver coordinates', ['Login as approved driver', 'POST lat and lng within valid ranges', 'Check driver_info record'], 'Returns 200: "Location updated successfully". current-lat, current-lng, current-location-at updated in driver_info.'],
    ['Test Case 2: Invalid Latitude', 'Validation', ['POST lat=95 (out of range)', 'Submit'], '422 Validation error on lat (between -90 and 90).'],
    ['Test Case 3: Invalid Longitude', 'Validation', ['POST lng=200', 'Submit'], '422 Validation error on lng (between -180 and 180).'],
    ['Test Case 4: Missing Coordinates', 'Required fields', ['POST without lat/lng', 'Submit'], '422 Validation errors.'],
    ['Test Case 5: Unauthenticated Driver', 'Auth required', ['Call without Bearer token'], '401 Unauthorized.'],
    ['Test Case 6: Driver Info Not Found', 'Missing driver_info', ['Login as user without driver_info record', 'POST location'], '404: "Driver info not found."'],
]);

$body .= ep('Artisan', 'php artisan wasl:sync-driver-locations', 'Scheduled Location Sync Command', [
    ['Test Case 1: Successful Sync', 'Send locations to WASL', ['Ensure drivers with is-receiving-rides=true OR on ongoing trips have stored lat/lng, identity_number, sequence-number', 'Run: php artisan wasl:sync-driver-locations', 'Check console output and wasl logs'], 'Console: "Sent N driver location(s) to WASL." WASL POST /api/dispatching/v2/locations called with locations array. hasCustomer=true for ongoing trip drivers, false for available drivers.'],
    ['Test Case 2: Dry Run Mode', 'Preview without sending', ['Run: php artisan wasl:sync-driver-locations --dry-run --show-details'], 'Console shows diagnostics (ongoing driver IDs, receiving rides IDs, collected locations). No WASL API call made. Message: "Dry run: would send N driver location(s) to WASL."'],
    ['Test Case 3: No Locations Collected', 'Nothing to send', ['Ensure no drivers are receiving rides and no ongoing trips with stored locations', 'Run command'], 'Console: "No driver locations collected. Nothing sent to WASL." Exit code 0.'],
    ['Test Case 4: Missing identity_number or sequence-number', 'Driver skipped', ['Driver receiving rides but missing identity_number or sequence-number', 'Run command with --show-details'], 'Driver listed in diagnostics with [missing: identity_number] or [missing: sequence-number]. Location NOT included in WASL payload.'],
    ['Test Case 5: WASL Disabled', 'Integration off', ['Set WASL_ENABLED=false', 'Run command'], 'Console: "WASL is disabled (WASL_ENABLED=false)." Exit code 0. No API call.'],
    ['Test Case 6: WASL API Failure', 'External error', ['Simulate WASL location API returning error', 'Run command'], 'Console error message. Logged to storage/logs/wasl/. Exit code 1 (FAILURE).'],
    ['Test Case 7: Scheduled Execution', 'Cron job', ['Verify schedule in app/Console/Kernel.php (everyMinute)', 'Wait for scheduler or run php artisan schedule:run'], 'Command executes automatically every minute when scheduler is active.'],
], 'Scheduled every minute via Laravel scheduler.');

// ==================== PROVINCE SYNC ====================
$body .= '<h2>6. Province Sync (WASL 5.7)</h2>';
$body .= ep('Artisan', 'php artisan wasl:sync-provinces', 'Province Sync Command', [
    ['Test Case 1: Successful Province Sync', 'Fetch and store provinces', ['Ensure WASL_ENABLED=true and valid credentials', 'Run: php artisan wasl:sync-provinces', 'Check wasl_provinces table'], 'Console: "Synced N WASL provinces successfully." Provinces stored with province_id, province_name, region_name, province_name_normalized, synced_at.'],
    ['Test Case 2: WASL Disabled', 'Integration off', ['Set WASL_ENABLED=false', 'Run command'], 'Console: "WASL integration is disabled (WASL_ENABLED=false)." Exit code 1 (FAILURE).'],
    ['Test Case 3: WASL API Failure', 'Province inquiry fails', ['Simulate WASL province-inquiry API error', 'Run command'], 'Console: "Failed to sync WASL provinces: [error message]". Exit code 1. No partial data or old data preserved.'],
    ['Test Case 4: Verify Province Used in Trip Registration', 'End-to-end dependency', ['Sync provinces', 'Complete and rate a trip between known cities', 'Check trip payload in logs'], 'Trip registration resolves provinceId from local wasl_provinces table by city name matching.'],
    ['Test Case 5: Empty Province Response', 'No provinces returned', ['Simulate empty result array from WASL', 'Run command'], 'Console: "Synced 0 WASL provinces successfully." wasl_provinces table unchanged or empty.'],
]);

$body .= ep('Internal', 'WaslService::resolveProvinceIdForTrip()', 'Province ID Resolution', [
    ['Test Case 1: Match Origin City', 'Resolve by origin', ['Ensure province synced for trip origin city name', 'Trigger trip registration'], 'provinceId resolved from wasl_provinces by normalized Arabic name match.'],
    ['Test Case 2: Fallback to Destination City', 'Origin not found', ['Origin city not in wasl_provinces, destination city exists', 'Trigger trip registration'], 'provinceId resolved from destination city name.'],
    ['Test Case 3: No Provinces Synced', 'Empty table', ['Clear wasl_provinces table', 'Attempt trip registration'], 'Exception: "WASL provinces are not synced. Run: php artisan wasl:sync-provinces"'],
    ['Test Case 4: City Not Found', 'No matching province', ['Trip between cities not in wasl_provinces', 'Attempt trip registration'], 'Exception: "Unable to resolve WASL provinceId for cities: [origin] / [destination]"'],
]);

// ==================== DAILY ELIGIBILITY JOB ====================
$body .= '<h2>7. Daily Eligibility Check (Scheduled Job)</h2>';
$body .= ep('Artisan', 'php artisan drivers:check-wasl-eligibility', 'Daily Eligibility Command', [
    ['Test Case 1: Driver Becomes Ineligible', 'Approval changed to 4', ['Start with approved driver (approval=1) whose WASL eligibility expires or becomes invalid', 'Run command', 'Check driver record'], 'Driver approval updated to 4. reject-reason set to WASL rejection message. Driver blocked from trip operations (ChecksDriverWaslStatus trait).'],
    ['Test Case 2: Driver Becomes Eligible Again', 'Approval restored from 4 to 0', ['Driver with approval=4 becomes valid in WASL again', 'Run command'], 'Driver approval updated to 0. reject-reason cleared. Driver can proceed with re-approval flow.'],
    ['Test Case 3: No Change — Still Valid', 'Approved driver remains valid', ['Run command for eligible approved driver'], 'No approval change. Driver remains approval=1.'],
    ['Test Case 4: Skip Drivers Without Identity', 'Missing identity_number', ['Driver without identity_number in driver_info', 'Run command'], 'Driver skipped silently. No API call.'],
    ['Test Case 5: WASL API Error During Check', 'Transient failure', ['Simulate WASL API error for a driver', 'Run command'], 'Driver skipped (is_valid=null or api_error). No approval change. Error logged to wasl channel.'],
    ['Test Case 6: Scheduled Daily Run', 'Cron at 02:00', ['Verify schedule in Kernel.php: dailyAt 02:00', 'Run php artisan schedule:run at scheduled time'], 'Command processes all drivers with approval in [0,1,4] and identity_number set.'],
    ['Test Case 7: Driver App Block After approval=4', 'Operational block', ['Set driver approval=4 via daily check', 'Driver attempts to start service or accept trip in app'], '403 error: s_abshir_update_required with message about Absher update required.'],
], 'Scheduled daily at 02:00 via Laravel scheduler.');

// ==================== DRIVER APP WASL BLOCKS ====================
$body .= '<h2>8. Driver App — WASL-Related Access Blocks</h2>';
$body .= ep('Various', 'api/driver/* (trip & service endpoints)', 'ChecksDriverWaslStatus trait', [
    ['Test Case 1: Unapproved Driver Blocked', 'approval != 1', ['Login as driver with approval=0 (pending)', 'Attempt POST api/driver/service/start'], '403: s_userNotApproved — registration under review message.'],
    ['Test Case 2: Absher Update Required', 'approval = 4', ['Login as driver with approval=4', 'Attempt trip action'], '403: s_abshir_update_required with reject-reason from WASL daily check.'],
    ['Test Case 3: Approved Driver Allowed', 'approval = 1', ['Login as approved eligible driver', 'Start service and accept trips'], 'Operations proceed normally (subject to subscription/dues checks).'],
    ['Test Case 4: Rejected Driver Blocked', 'approval = 3', ['Login as rejected driver', 'Attempt service start'], '403: s_userNotApproved.'],
]);

// ==================== CONFIG & LOGGING ====================
$body .= '<h2>9. Configuration & Logging</h2>';
$body .= ep('Config', '.env / config/wasl.php', 'WASL Configuration', [
    ['Test Case 1: Valid Credentials', 'API authentication', ['Set RAQFNI_CLIENT_KEY, RAQFNI_APP_ID, RAQFNI_APP_KEY, RAQNI_API_URL', 'Run any WASL operation'], 'API calls include headers: client-id, app-id, app-key. Requests succeed with valid credentials.'],
    ['Test Case 2: Invalid Credentials', 'Auth failure', ['Set invalid app-key', 'Run wasl:sync-provinces'], 'WASL API returns error. Failure logged to storage/logs/wasl/.'],
    ['Test Case 3: WASL Log Channel', 'Logging verification', ['Perform driver registration', 'Check storage/logs/wasl/laravel.log'], 'Request and response payloads logged with driver_id/trip_id context. Errors logged at error level.'],
    ['Test Case 4: Toggle WASL_ENABLED', 'Master switch', ['Set WASL_ENABLED=false', 'Attempt registration, location sync, trip store'], 'All WASL external API calls skipped. Local app operations continue where not dependent on WASL response.'],
]);

writeTestCaseDocument(
    __DIR__ . '/WASL-Manual-Test-Cases.html',
    'Atariqi — WASL Integration Manual Test Cases',
    'WASL (Ministry of Transport) integration covering driver registration, eligibility, trip registration, location sync, province sync, scheduled jobs, and dashboard flows.',
    [
        'WASL API credentials configured in .env (RAFQNI_CLIENT_KEY, RAQFNI_APP_ID, RAQFNI_APP_KEY, RAQNI_API_URL)',
        'WASL_ENABLED=true for live integration tests',
        'Test driver with valid Saudi identity_number and vehicle sequence_number in ministry system',
        'Test driver with known WASL rejection (for negative tests)',
        'Admin account for dashboard approval flows',
        'Passenger account for trip completion and rating',
        'Access to storage/logs/wasl/ for verification',
        'Artisan CLI access for scheduled command tests',
    ],
    [
        'WASL Integration Overview',
        'Driver Registration (WASL 5.1)',
        'Driver Eligibility Check (WASL 5.2)',
        'Trip Registration (WASL 5.4)',
        'Driver Location Sync (WASL 5.6)',
        'Province Sync (WASL 5.7)',
        'Daily Eligibility Check',
        'Driver App WASL Access Blocks',
        'Configuration & Logging',
    ],
    $body
);

echo "Generated WASL test cases.\n";

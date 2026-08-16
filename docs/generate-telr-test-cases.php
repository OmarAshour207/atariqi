<?php

require __DIR__ . '/test-case-helpers.php';

$body = '';

// ==================== OVERVIEW ====================
$body .= '<h2>1. Telr Payment Gateway Overview</h2>';
$body .= '<p>Atariqi uses Telr hosted payment pages for driver subscription payments and dues payments. Configuration is in <code>config/telr.php</code> and <code>.env</code>. Payment events are logged to <code>storage/logs/payment/</code>.</p>';
$body .= '<table><tr><th>Flow</th><th>Entry Point</th><th>Completion</th></tr>';
$body .= '<tr><td>Subscribe to Package</td><td>POST api/driver/subscribe</td><td>Telr webhook → activate subscription</td></tr>';
$body .= '<tr><td>Upgrade Package</td><td>POST api/driver/subscribe (with existing package)</td><td>Telr webhook → upgrade subscription</td></tr>';
$body .= '<tr><td>Pay Dues</td><td>POST api/driver/dues/pay</td><td>Telr webhook → record FinancialDue</td></tr>';
$body .= '<tr><td>Cancel Subscription</td><td>POST api/driver/cancel</td><td>Direct (no Telr — moves to free plan)</td></tr>';
$body .= '</table>';
$body .= '<div class="note"><strong>Note:</strong> Return URLs after Telr checkout: authorised → /payment/telr/success, declined → /payment/telr/declined. Cancelled URL is configured as /payment/telr/cancel but this route may not be registered — verify in routes/web.php.</div>';

// ==================== SUBSCRIBE ====================
$body .= '<h2>2. Driver Subscription Payment</h2>';
$body .= ep('POST', 'api/driver/subscribe', 'Create Subscription Payment Session', [
    ['Test Case 1: New Subscription — Monthly', 'First-time subscription', ['Login as driver with no active UserPackage', 'POST package_id and type=monthly for active paid package', 'Check response'], 'Returns 200 JSON: success=true, payment_url (Telr hosted page), order_ref. Order created with status=PENDING, type=subscription, interval=monthly. payment_gateway_id saved after Telr session created.'],
    ['Test Case 2: New Subscription — Yearly', 'Annual plan', ['Login as driver without package', 'POST package_id and type=yearly', 'Check response'], 'Order amount = package price_annual. payment_url returned. Order type=subscription, interval=yearly.'],
    ['Test Case 3: Upgrade Existing Package', 'Driver has active package', ['Login as driver with active paid package', 'POST different package_id or different interval', 'Check response'], 'Order type=upgrade (not subscription). payment_url returned. Amount based on selected interval price.'],
    ['Test Case 4: Already Subscribed — Same Package', 'Duplicate subscription', ['Login as driver already on same package_id and interval', 'POST same package_id and type'], '422 error: "You are already subscribed to this package." No new order created.'],
    ['Test Case 5: Inactive Package', 'SOON status package', ['POST package_id for package with status=SOON', 'Submit'], '422 error: "This package is not active." No order created.'],
    ['Test Case 6: Validation Errors', 'Missing/invalid fields', ['POST without package_id', 'POST with invalid type (not monthly/yearly)', 'POST with non-existent package_id'], '422 Validation Error with field-specific messages.'],
    ['Test Case 7: Telr Session Creation Failure', 'Telr API error', ['Configure invalid TELR_STORE_ID or TELR_AUTH_KEY', 'POST valid subscribe request'], '400 response: success=false with Telr error message. Order remains PENDING without payment_gateway_id.'],
], 'Requires auth:sanctum + is_driver middleware.');

// ==================== CANCEL ====================
$body .= ep('POST', 'api/driver/cancel', 'Cancel Subscription (No Telr)', [
    ['Test Case 1: Successful Cancellation', 'Move to free plan', ['Login as driver with active paid subscription', 'Ensure free package exists in database', 'POST api/driver/cancel'], '200 OK: "You have successfully canceled the subscription." Old package moved to UserPackageHistory with status=CANCELLED, canceled_date=today. New free package assigned (yearly, active, end_date=today+1 year).'],
    ['Test Case 2: No Active Subscription', 'Nothing to cancel', ['Login as driver with no UserPackage record', 'POST cancel'], '422: "You do not have an active subscription." No DB changes.'],
    ['Test Case 3: Already Cancelled', 'Duplicate cancel attempt', ['Login as driver whose package status is already CANCELLED', 'POST cancel'], '422: "You have already canceled your subscription." (if applicable) OR "You do not have an active subscription." depending on state.'],
    ['Test Case 4: Free Package Not Found', 'Missing free plan', ['Remove all free packages from DB', 'Login as driver with active paid subscription', 'POST cancel'], '500 error with exception about null free package. Transaction rolled back. Original package remains.'],
    ['Test Case 5: Transaction Rollback', 'DB failure during cancel', ['Simulate DB error during cancellation', 'POST cancel'], '500 error. Complete rollback. Original subscription intact.'],
    ['Test Case 6: Sequential Cancel Attempts', 'Idempotency check', ['Cancel successfully first time', 'Immediately POST cancel again'], 'First: 200 success. Second: 422 no active subscription. Driver ends on free plan only.'],
    ['Test Case 7: Unauthenticated', 'Auth required', ['POST cancel without Bearer token'], '401 Unauthorized.'],
]);

// ==================== DUES ====================
$body .= '<h2>3. Driver Dues Payment</h2>';
$body .= ep('GET', 'api/driver/dues', 'Get Dues Summary', [
    ['Test Case 1: View Dues Data', 'Driver with outstanding dues', ['Login as approved driver with revenue since last payment', 'GET api/driver/dues'], '200 response with: last_pay_date, last_pay_cost, new_revenues, current_dues (percentage × revenue), can_accept_trips, requires_abshir_update, abshir_message.'],
    ['Test Case 2: No Previous Payment', 'First-time dues check', ['Login as driver with no FinancialDue records but with trip revenue', 'GET dues'], 'last_pay_date=null, last_pay_cost=0. current_dues calculated from all revenue.'],
    ['Test Case 3: Can Accept Trips Flag', 'Dues threshold check', ['Check can_accept_trips when dues exceed limit vs when within limit'], 'can_accept_trips=true when approval=1 AND dues within acceptable range per scopeCheckacceptTrips(). false when dues too high.'],
    ['Test Case 4: Absher Update Required', 'approval=4 driver', ['Login as driver with approval=4', 'GET dues'], 'requires_abshir_update=true. abshir_message contains reject-reason.'],
    ['Test Case 5: Unauthenticated', 'Auth required', ['GET without token'], '401 Unauthorized.'],
]);

$body .= ep('POST', 'api/driver/dues/pay', 'Initiate Dues Payment via Telr', [
    ['Test Case 1: Pay Dues Successfully', 'Create payment session', ['Login as driver with current_dues > 0', 'POST api/driver/dues/pay', 'Check response'], '200 JSON: success=true, payment_url, order_ref. Order created: type=pay_due, status=PENDING, amount=calculated dues, interval=one-time.'],
    ['Test Case 2: No Dues to Pay', 'Zero or negative dues', ['Login as driver with no revenue since last payment (current_dues ≤ 0)', 'POST dues/pay'], 'Error: "No dues to pay". No order created.'],
    ['Test Case 3: Telr Session Failure', 'Telr API error', ['Configure invalid Telr credentials', 'POST dues/pay with outstanding dues'], '400: success=false with Telr error message. Order created but no payment_url.'],
    ['Test Case 4: Complete Payment Flow', 'End-to-end dues payment', ['POST dues/pay → open payment_url → complete payment on Telr test page → Telr sends webhook', 'Check FinancialDue and Order status'], 'Webhook processes payment. Order status=COMPLETED. FinancialDue record created with driver-id, amount, date-of-add.'],
    ['Test Case 5: Verify Order Amount', 'Correct dues calculation', ['Note revenue and general dues percentage', 'POST dues/pay', 'Compare order amount'], 'Order amount = (general_dues_percentage × new_revenues) / 100 since last payment date.'],
    ['Test Case 6: Unauthenticated', 'Auth required', ['POST without token'], '401 Unauthorized.'],
    ['Test Case 7: Customer Data in Telr Payload', 'Telr session fields', ['POST dues/pay', 'Inspect Telr API request in logs or network'], 'Telr payload includes customer email, phone, first/last name from authenticated driver.'],
]);

// ==================== WEBHOOK ====================
$body .= '<h2>4. Telr Webhook</h2>';
$body .= ep('POST', 'api/webhook/telr', 'telr.webhook — Payment Notification Handler', [
    ['Test Case 1: Authorized — Subscription Payment', 'tran_status=A, type=subscription', ['Create pending subscription order via api/driver/subscribe', 'Simulate Telr webhook POST with tran_status=A and tran_cartid=order.id', 'Check DB'], 'Order status updated to COMPLETED. UserPackage created (active, correct dates based on interval). UserPackageHistory record created. Response: "You have successfully subscribed to the package." Logged to storage/logs/payment/.'],
    ['Test Case 2: Authorized — Upgrade Payment', 'tran_status=A, type=upgrade', ['Create pending upgrade order', 'Send webhook with tran_status=A'], 'Old UserPackage moved to history with canceled_date. Old package deleted. New UserPackage created. Response: "You have successfully upgraded the package."'],
    ['Test Case 3: Authorized — Dues Payment', 'tran_status=A, type=pay_due', ['Create pending dues order via dues/pay', 'Send webhook with tran_status=A'], 'Order status=COMPLETED. FinancialDue record created with amount and date. Response: "Dues payment completed successfully."'],
    ['Test Case 4: Failed/Declined Payment', 'tran_status != A', ['Create pending order', 'Send webhook with tran_status=D (declined) or other non-A status'], 'Order status updated to FAILED. Logged as payment failed. Response: {"status":"success"} (acknowledges webhook).'],
    ['Test Case 5: Unknown Order ID', 'Order not found', ['Send webhook with tran_cartid that does not match pending order', 'Submit'], 'Response: {"status":"ignored"}. No DB changes.'],
    ['Test Case 6: Already Processed Order', 'Non-pending order', ['Send webhook for order already COMPLETED', 'Submit again'], 'Order not found (status != PENDING). Response: {"status":"ignored"} or order marked FAILED incorrectly — verify behavior.'],
    ['Test Case 7: Webhook DB Transaction Rollback', 'Subscription processing failure', ['Simulate DB error during subscribe() webhook handler', 'Send authorized webhook'], '500 error returned. Transaction rolled back. Order remains PENDING. No UserPackage created. Error logged to payment channel.'],
], 'Public endpoint — no authentication. Telr sends POST with tran_status, tran_cartid, and other fields.');

// ==================== RETURN PAGES ====================
$body .= '<h2>5. Payment Return Pages (Browser Redirect)</h2>';
$body .= ep('GET', '/payment/telr/success', 'telr.payment.success', [
    ['Test Case 1: Success Page Loads', 'After authorized payment', ['Complete successful payment on Telr hosted page', 'Telr redirects browser to authorised URL'], 'Payment success view rendered (resources/views/payment/success.blade.php). User-friendly confirmation message displayed.'],
    ['Test Case 2: Direct Access', 'Open URL directly', ['Navigate to /payment/telr/success without completing payment'], 'Success page still renders (informational only — actual payment confirmed via webhook).'],
    ['Test Case 3: Mobile App WebView', 'In-app browser return', ['Driver app opens payment_url in WebView', 'After payment, WebView redirected to success URL'], 'Success page displayed in WebView. App should detect return URL to close WebView.'],
]);

$body .= ep('GET', '/payment/telr/failed', 'telr.payment.failed', [
    ['Test Case 1: Failed Page Loads', 'Payment failure redirect', ['Navigate to /payment/telr/failed'], 'Failed payment view rendered with appropriate message.'],
    ['Test Case 2: User Guidance', 'Clear next steps', ['View failed page content'], 'Page informs user that payment was not completed and suggests retrying.'],
]);

$body .= ep('GET', '/payment/telr/declined', 'telr.payment.declined', [
    ['Test Case 1: Declined Page Loads', 'Card declined redirect', ['Complete payment with declined test card on Telr', 'Telr redirects to declined URL'], 'Declined payment view rendered.'],
    ['Test Case 2: Order Status After Decline', 'Webhook + redirect consistency', ['Decline payment on Telr', 'Check order status in DB via webhook'], 'Order status=FAILED. Declined page shown to user.'],
    ['Test Case 3: Retry Payment', 'User can pay again', ['After decline, call api/driver/subscribe or dues/pay again'], 'New pending order created. New payment_url returned. Previous failed order remains FAILED.'],
]);

// ==================== TELR SERVICE ====================
$body .= '<h2>6. TelrService (Internal)</h2>';
$body .= ep('Internal', 'TelrService::createSession()', 'Create Telr Hosted Payment Page', [
    ['Test Case 1: Valid Session Creation', 'Successful API call', ['Call with valid order_id, amount, customer details', 'TELR_TEST_MODE=true'], 'Returns success=true, payment_url, order_ref. Telr payload includes store, authkey, order (cartid, amount, currency=SAR, test=1), customer, return URLs.'],
    ['Test Case 2: Telr API Error', 'Invalid credentials', ['Set wrong TELR_AUTH_KEY', 'Create session'], 'Returns success=false, error message from Telr response.'],
    ['Test Case 3: Test Mode Flag', 'Test vs live', ['Set TELR_TEST_MODE=true', 'Create session and inspect payload'], 'order.test=1 in Telr request. Use Telr test cards for payment.'],
    ['Test Case 4: Production Mode', 'Live payments', ['Set TELR_TEST_MODE=false with live credentials', 'Create session'], 'order.test=0. Real charges processed.'],
    ['Test Case 5: Amount Formatting', 'Two decimal places', ['Create session with amount=99.5', 'Check Telr payload'], 'Amount sent as "99.50" (number_format with 2 decimals).'],
    ['Test Case 6: Return URLs', 'Correct redirect paths', ['Create session', 'Inspect return URLs in payload'], 'authorised=/payment/telr/success, cancelled=/payment/telr/cancel, declined=/payment/telr/declined (from config/telr.php).'],
]);

$body .= ep('Internal', 'TelrService::checkStatus()', 'Check Transaction Status', [
    ['Test Case 1: Check Completed Order', 'Verify payment status', ['Complete a payment and obtain order_ref', 'Call checkStatus(order_ref) via tinker or test script'], 'Telr returns transaction status details for the order reference.'],
    ['Test Case 2: Check Pending Order', 'Unpaid order', ['Check status for order_ref before payment completed'], 'Telr returns pending/unpaid status.'],
    ['Test Case 3: Invalid Order Reference', 'Non-existent ref', ['Call checkStatus with invalid ref'], 'Telr returns error response.'],
]);

// ==================== CONFIG ====================
$body .= '<h2>7. Configuration & Environment</h2>';
$body .= ep('Config', '.env / config/telr.php', 'Telr Configuration', [
    ['Test Case 1: Required Environment Variables', 'All keys set', ['Verify TELR_STORE_ID, TELR_AUTH_KEY, TELR_SECRET_KEY, TELR_ENDPOINT set in .env', 'Create payment session'], 'Telr API calls succeed. No missing config errors.'],
    ['Test Case 2: Currency Setting', 'SAR default', ['Check TELR_CURRENCY in .env (default SAR)', 'Create payment session'], 'Telr order.currency=SAR in payload.'],
    ['Test Case 3: Custom Return URLs', 'Override defaults', ['Set TELR_SUCCESS_URL, TELR_DECLINED_URL, TELR_CANCEL_URL in .env', 'Create session'], 'Custom URLs used in Telr return block instead of defaults.'],
    ['Test Case 4: Payment Logging', 'Log channel', ['Process a webhook', 'Check storage/logs/payment/laravel.log'], 'Webhook payload logged. Authorization, subscription processing, and errors logged with order references.'],
    ['Test Case 5: Test Card Payment', 'Telr sandbox', ['Use Telr test card numbers in test mode', 'Complete full subscribe flow'], 'Payment authorized. Webhook received. Subscription activated. Success page shown.'],
    ['Test Case 6: Declined Test Card', 'Negative payment test', ['Use Telr declined test card', 'Complete payment flow'], 'Payment declined. Webhook marks order FAILED. Declined page shown.'],
    ['Test Case 7: Dashboard Payment Reminder', 'Related feature', ['Admin sends payment reminder from dashboard for driver with dues > 50 SAR', 'Driver receives email and pays via dues/pay'], 'Reminder email sent. Driver can initiate Telr dues payment from app. Full flow completes via webhook.'],
], 'Dashboard reminder: POST dashboard/drivers/{driver}/send-payment-reminder (requires dues > 50 SAR).');

// ==================== ORDER MODEL ====================
$body .= '<h2>8. Order Lifecycle</h2>';
$body .= ep('Database', 'orders table', 'Order Status Transitions', [
    ['Test Case 1: Pending → Completed', 'Successful payment', ['Create order via subscribe/dues/pay', 'Complete Telr payment', 'Webhook processes'], 'Order: status 1 (PENDING) → 2 (COMPLETED). payment_gateway_id populated with Telr order ref.'],
    ['Test Case 2: Pending → Failed', 'Declined payment', ['Create order', 'Decline on Telr', 'Webhook with non-A status'], 'Order: status 1 (PENDING) → 3 (FAILED).'],
    ['Test Case 3: Order Types', 'Verify type field', ['Create subscription, upgrade, and dues orders', 'Inspect orders table'], 'type values: subscription, upgrade, pay_due respectively.'],
    ['Test Case 4: Duplicate Webhook Idempotency', 'Same webhook twice', ['Send authorized webhook twice for same order', 'Check DB'], 'Second webhook should not create duplicate UserPackage or FinancialDue. Verify idempotency behavior (order no longer PENDING on second call).'],
    ['Test Case 5: Amount Integrity', 'Order amount matches Telr', ['Subscribe to monthly package priced at X SAR', 'Verify order.amount = X'], 'Order amount matches package price for selected interval.'],
]);

writeTestCaseDocument(
    __DIR__ . '/Telr-Manual-Test-Cases.html',
    'Atariqi — Telr Payment Gateway Manual Test Cases',
    'Telr hosted payment page integration for driver subscriptions, package upgrades, and dues payments — including webhook processing and return pages.',
    [
        'Telr sandbox credentials: TELR_STORE_ID, TELR_AUTH_KEY, TELR_SECRET_KEY',
        'TELR_TEST_MODE=true for sandbox testing',
        'Telr test card numbers (authorised and declined)',
        'Approved driver account with Bearer token (Sanctum)',
        'Active paid package in packages table',
        'Free package configured (status=FREE) for cancel tests',
        'General dues percentage configured in subscriptions table',
        'Driver with trip revenue for dues payment tests',
        'Admin account for payment reminder tests (optional)',
        'Access to storage/logs/payment/ for verification',
        'Tool to simulate Telr webhook POST (Postman/curl)',
    ],
    [
        'Telr Payment Gateway Overview',
        'Driver Subscription Payment',
        'Cancel Subscription',
        'Driver Dues Payment',
        'Telr Webhook',
        'Payment Return Pages',
        'TelrService (Internal)',
        'Configuration & Environment',
        'Order Lifecycle',
    ],
    $body
);

echo "Generated Telr test cases.\n";

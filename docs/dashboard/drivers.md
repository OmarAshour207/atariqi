# إدارة السائقين — DriverController

**الكلاس:** `App\Http\Controllers\Dashboard\DriverController`  
**الملف:** `app/Http/Controllers/Dashboard/DriverController.php`  
**حقن:** `WaslService` · **Trait:** `Api\Driver\Traits\Payment`  
**Resource `drivers`:** موجود index/show/edit/update · **ناقص:** create/store/destroy

### قيم approval في هذا الكنترولر
| قيمة | المعنى |
|------|--------|
| 0 | طلب جديد |
| 1 | موافق/نشط |
| 2 | تحديث معلّق |
| 3 | مرفوض/محظور |

---

### GET /dashboard/drivers — `drivers.index`

- قائمة سائقين + فلاتر (اسم، إيميل، جوال، approval، جامعة، مرحلة، تقييم، مستحقات) وترتيب  
- يرفق `current_dues` عبر حساب المستحقات  
- View: `dashboard.drivers.index` · paginate 20

### GET /dashboard/new-drivers — `new-drivers.index`

- فقط `approval=0`  
- View: `dashboard.drivers.new_drivers`

### GET /dashboard/drivers/{driver} — `drivers.show`

- تفاصيل كاملة: باقة، أحياء، حظر، مستحقات، أهلية Wasl إن وُجدت هوية  
- رفض إن ليس سائقًا  
- View: `dashboard.drivers.show`

### GET .../edit | PUT ... — edit / update

- تحديث: أسماء، إيميل فريد، هاتف  
- **لا** يحدّث سيارة/رخصة هنا

### GET /dashboard/drivers/{driver}/trips — `drivers.driverTrips`

- رحلات فوري/يومي/أسبوعي للسائق  
- عرض رقم الأسبوعي عبر `trip_display_id` → **group-id**  
- View: `dashboard.drivers.driver_trips`

### GET /dashboard/drivers/{driver}/earnings — `drivers.earnings`

- إيرادات العمر، نسبة المستحقات، مدفوع، متبقي  
- View: `dashboard.drivers.driver_earnings`

### GET /dashboard/driver/packages — `drivers.packages`

- سائقون approval ∈ {1,2} مع باقات قابلة للتعيين (`status != SOON`)

### GET /dashboard/drivers/{driver}/packages — `drivers.packagePlans`

- خطط الباقات لسائق واحد

### POST .../packages/assign — `drivers.assignPackage`

- Validation: `package_id`, `interval` monthly|yearly  
- أرشفة النشط إن وُجد، إنشاء نشط جديد، `EmployeePackageLog`, إيميل `PackageAssignmentMail`

### POST .../packages/cancel — `drivers.cancelPackage`

- إلغاء النشط → باقة FREE سنوية؛ يمنع إلغاء المجانية؛ إيميل `PackageCancellationMail`

### GET /dashboard/driver/rates — `drivers.rates`

- تجميع تقييمات من `DelDailyInfo` / `DelWeekInfo` / `DelImmediateInfo`

### GET /dashboard/driver/trips — `drivers.trips`

- سجل عام بكل الأنواع + فلاتر `driver_id` / `trip_type`  
- رقم الأسبوعي: `trip_display_id`  
- View: `dashboard.drivers.trips`

### POST .../send-payment-reminder — `drivers.sendPaymentReminder`

- يرسل فقط إن المستحقات **> 50** SAR  
- `PaymentReminderMail` + `PlatformEmailLog`

### POST .../update-status — `drivers.updateStatus`

- Validation: `approval` in 1,2,3؛ سبب مطلوب إن 3  
- من 0→1: فحص Wasl إلزامي + `DriverApprovedMail`  
- إلى 3: `DriverBanned` + `DriverRejectedMail` + قرار `CaptainRequestDecision`  
- Redirect غالبًا إلى `new-drivers.index`

### POST .../assign-to-admins — `drivers.assignToAdmin`

- إسناد لمدير آخر + `CaptianRequestAssignment` + `DriverRequestAssignmentMail`  
- فشل الإيميل لا يلغي الإسناد (warning)

### POST .../ban — `drivers.ban`

- Validation: `ban_reason`  
- **شرط:** `driver-rate` موجود و **< 1**  
- approval=3 + `DriverBanned` + قرار `banned` + `DriverBannedMail`

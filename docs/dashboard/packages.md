# الباقات (Packages) — داشبورد

**الكلاس:** `PackageController`  
**Models:** `Package`, `User`, `PlatformEmailLog`, جدول `subscription_employee_log`  
**ناقص Resource:** `show`  
**المستلمون للإيميل:** سائقون (`user-type=driver`) ذوو إيميل — عبر `notifyDrivers`

---

### GET /dashboard/packages — index

- فلاتر: `status`, `name`, `monthly_price`, `annual_price`, `has_features`, `sort_by`, `sort_direction`
- View: `dashboard.packages.index`

### GET create | POST store

- **Validation:** `name_ar/en` required؛ أسعار numeric min:0؛ `status` in 0,1,2,3؛ `features.*` exists:features
- **W:** إنشاء باقة + sync ميزات + سجل موظف `created`
- **Side effects:** `NewPackageNotificationMail` لكل سائق + `PlatformEmailLog`

### GET edit | PUT update

- نفس التحقق؛ sync أو detach الميزات؛ سجل `updated` (old/new)
- **Side effects:** `PackageUpdateNotificationMail`

### DELETE destroy

- سجل `deleted` ثم حذف
- **Side effects:** `PackageDeletedNotificationMail` (على snapshot قبل الحذف)

ثوابت الحالة في نموذج الباقة: انظر `shared/payments-dues-packages.md`.

# المصادقة والأدوار

## 1. حراس المصادقة (Guards)

| Guard | الاستخدام | الآلية |
|-------|-----------|--------|
| `sanctum` (افتراضي لمستخدمي `users`) | تطبيق السائق والراكب | Bearer token بعد OTP |
| `admin` | الداشبورد | Session بعد `dashboard/login` |

نموذج الموبايل: `App\Models\User` (`user-type` = `driver` | `passenger`).  
نموذج الأدمن: `App\Models\Admin`.

---

## 2. Middleware المسارات

| Middleware | الملف | الشرط |
|------------|-------|-------|
| `auth:sanctum` | Laravel Sanctum | مستخدم مصادق بتوكن |
| `is_passenger` | `app/Http/Middleware/IsPassenger.php` | `user-type === passenger` |
| `is_driver` | `app/Http/Middleware/IsDriver.php` | `user-type === driver` |
| `is_admin` | `app/Http/Middleware/IsAdmin.php` | جلسة `admin` نشطة |
| `admin.page` | `EnsureAdminPageAccess` | صلاحية صفحة + فعل (view/update/…) عبر `AdminAuthorizationService` |
| `locale` | `LocaleCheck` | تعيين لغة التطبيق |
| `login.throttle` | `LoginThrottle` | حد محاولات دخول الداشبورد + إيميل عند التعطيل |

---

## 3. قيم `users.approval` (من استخدامات الكود)

| القيمة | ظهور في الكود |
|--------|----------------|
| `0` | تسجيل سائق جديد / إعادة بعد Wasl |
| `1` | معتمد — يمكنه العمل |
| `2` | تعديل ملف قيد المراجعة |
| `3` | مرفوض / محظور (مسارات دخول) |
| `4` | يتطلب تحديث أبشر / أهلية Wasl (يحظر تشغيل الرحلات عبر `ChecksDriverWaslStatus`) |

الراكب يستخدم أيضًا `approval` لطلبات تعديل الملف (`editProfile` يضع `2`).

---

## 4. تسجيل الدخول — راكب

1. `POST user/register` أو `POST user/send` → OTP  
2. `POST user/verify` → Sanctum token + `UserResource`  
3. حظر عبر جدول `passenger_banned` إن وُجد رقم الجوال

الملف: `app/Http/Controllers/Api/UserController.php` + `GuardsOtpSending`.

---

## 5. تسجيل الدخول — سائق

1. `POST driver/register` → إنشاء مستخدم `approval=0` + Wasl  
2. `POST driver/login` → OTP (يشترط موافقة مناسبة)  
3. `POST driver/verify` → token + `DriverResource`؛ قد يعيد `requires_abshir_update` عند `approval===4`

الملفات: `RegisterController`, `LoginController` تحت `Api/Driver/`.

---

## 6. الأدمن والصلاحيات

- Spatie Permission على guard `admin`
- صفحات في `web_pages` / `WebPage`
- ربط الصلاحيات عبر `AdminAuthorizationService`
- أدوار تُدار من `RoleController` وموظفون من `EmployeeController`

مسارات معفاة من فحص الصفحة: `dashboard.index`, `dashboard.logout`, `profile.edit`, `profile.update`, `language` (حسب تقرير الداشبورد من الكود).

---

## 7. ما ليس موجودًا

- لا يوجد Laravel Policy مخصص ظاهر لكل موارد الرحلات في API (الحماية بالـ middleware + فلاتر `driver-id`/`passenger-id` داخل الاستعلامات).
- لا يوجد `app/Notifications` — الإشعارات عبر FCM helper وبريد `app/Mail`.

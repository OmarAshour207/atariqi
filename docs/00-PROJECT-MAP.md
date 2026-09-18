# 00 — خريطة المشروع (Project Map)

> جرد مستخرج من الكود فقط. تاريخ الجرد: من حالة المستودع الحالية.  
> أي بند غير ظاهر صراحة في الكود يُعلَّم بـ «غير مؤكد من الكود».

---

## 1. الإطار والإصدار

| البند | القيمة من الكود |
|--------|------------------|
| الإطار | Laravel (`laravel/framework` في `composer.json`) |
| الإصدار | `^9.0` |
| PHP | `^8.0` |
| مصادقة API | Laravel Sanctum `^2.14` |
| بادئة API | `/api` عبر `RouteServiceProvider` |
| اسم الحزمة | `laravel/laravel` |
| المنتج | تطبيق رافقني / Atariqi (باك إند) |

مكتبات ذات أثر تشغيلي (من `composer.json`):

- `spatie/laravel-permission` — أدوار/صلاحيات الأدمن
- `spatie/laravel-query-builder` — فلاتر ملخص السائق
- `twilio/sdk` + `vonage/client` — قنوات OTP/SMS
- `guzzlehttp/guzzle` — تكاملات خارجية (Telr / Wasl)
- `anlutro/l4-settings` — إعدادات الموقع
- `intervention/image` — معالجة صور

---

## 2. مجلدات الدخول الأساسية

| المسار | الدور |
|--------|------|
| `routes/api.php` | API السائق + الراكب + إعدادات عامة + webhook Telr |
| `routes/web.php` | الموقع العام + الداشبورد + صفحات رجوع Telr |
| `routes/console.php` | أمر `inspire` فقط (تجريبي Laravel) |
| `routes/channels.php` | قنوات البث (افتراضي Laravel) |
| `app/Http/Controllers/Api/` | كنترولرات الراكب + مشتركات API |
| `app/Http/Controllers/Api/Driver/` | كنترولرات السائق |
| `app/Http/Controllers/Dashboard/` | لوحة التحكم |
| `app/Http/Controllers/HomeController.php` | الموقع التسويقي |
| `app/Http/Controllers/SupportController.php` | تذاكر الدعم العامة |
| `app/Models/` | نماذج Eloquent (~73 ملفًا) |
| `app/Models/Concerns/` | Traits مثل `SnapshotsAtariqiPercentage` |
| `app/Console/Kernel.php` | الجدولة |
| `app/Console/Commands/` | أوامر Artisan المجدولة/اليدوية |
| `app/Services/` | خدمات (Telr, Wasl, Dues, ContactSettings, …) |
| `app/Mail/` | رسائل البريد (~23 mailable) |
| `app/Helper/helpers.php` | دوال مساعدة محملة تلقائيًا |
| `database/migrations/` | مخطط الجداول |
| `lang/` | ترجمات `ar.json` / `en.json` |
| `resources/views/` | Blade للموقع والداشبورد والإيميل |

### مجلدات مطلوبة في المواصفات وغير موجودة في المشروع

| المجلد | الحالة |
|--------|--------|
| `app/Jobs/` | **غير موجود** |
| `app/Observers/` | **غير موجود** |
| `app/Events/` | **غير موجود** |
| `app/Listeners/` | **غير موجود** |
| `app/Notifications/` | **غير موجود** (Laravel Notifications) |

`EventServiceProvider` يسجّل فقط ربط إطار العمل `Registered` → `SendEmailVerificationNotification`.

---

## 3. ملفات الـ Routing ومجموعات الـ prefix

### `routes/api.php` — بادئة `/api` + middleware `api` + غالبًا `locale`

| المجموعة | Middleware | من يستدعيها |
|----------|------------|-------------|
| عامة | `locale` | تطبيقات + webhook |
| راكب | `auth:sanctum` + `is_passenger` | تطبيق الراكب |
| سائق | `auth:sanctum` + `is_driver` + prefix `driver` | تطبيق السائق |

**ملاحظة:** `POST driver/packages` معلن **خارج** مجموعة `auth:sanctum` (عام).  
**ملاحظة:** `POST trip/update/location` داخل `auth:sanctum` فقط (أي دور مصادق).

### `routes/web.php`

| المجموعة | Middleware / Prefix | من يستدعيها |
|----------|---------------------|-------------|
| موقع عام | `locale` | الزوار |
| تسجيل دخول داشبورد | بدون / مع `login.throttle` على POST | موظفو الإدارة |
| داشبورد | `is_admin` + `admin.page:view` + prefix `dashboard` | الأدمن |
| Telr return | `payment/telr/*` | متصفح بعد الدفع |

---

## 4. الجداول الزمنية (Cron / Schedule)

من `app/Console/Kernel.php`:

| التوقيت | الأمر | الكلاس |
|---------|-------|--------|
| كل 5 دقائق | `notify-driver-nearby-trips` | `NotifyDriverForNearbyTrips` |
| يوميًا 12:00 | `payment-reminder` | `PaymentReminder` |
| يوميًا 01:00 | `delete-late-trips` | `DeleteLateRidesCommand` |
| يوميًا 00:05 | `check-finished-subscriptions` | `CheckFinishedSubscriptionsCommand` |
| يوميًا 02:00 | `drivers:check-wasl-eligibility` | `CheckDriverWaslEligibilityCommand` |
| كل دقيقة | `wasl:sync-driver-locations` | `SyncWaslDriverLocationsCommand` |
| أسبوعي الأحد 03:00 | `wasl:sync-provinces` | **معلّق بتعليق في الكود** |

تفاصيل كل أمر: `docs/shared/cron-jobs-and-schedulers.md`.

---

## 5. Queues / Jobs / Observers / Triggers

| النوع | النتيجة من الجرد |
|-------|------------------|
| Jobs | لا توجد كلاسّات تحت `app/Jobs` |
| Observers | لا توجد |
| Events/Listeners مخصصة | لا توجد |
| DB Triggers في migrations | **لم يُعثر** على `CREATE TRIGGER` في `database/` |
| Model hooks | Trait `SnapshotsAtariqiPercentage` على `creating` لنماذج الاقتراحات الثلاثة |
| Mail queues | بعض Mailable قد تنفّذ `ShouldQueue` — راجع كل ملف تحت `app/Mail/` |

---

## 6. جداول الرحلات الأساسية

| النوع | جدول الحجز | جدول اقتراح السائق | جدول توصيل | شكاوى عدم الركوب |
|-------|------------|---------------------|------------|------------------|
| فوري | `ride-booking` | `suggestions-drivers` | `del-immediate-info` (نموذج `DelImmediateInfo`) | `ImmediateUnrideRate` |
| يومي | `day-ride-booking` | `sug-day-drivers` | `DelDailyInfo` | `DayUnrideRate` |
| أسبوعي | `week-ride-booking` | `sug-week-drivers` | `DelWeekInfo` | `WeekUnrideRate` |

### أعمدة حاسمة

- **`action` على جداول الاقتراح (sug):** دورة حياة الرحلة مع السائق (قيم مختلفة للفوري مقابل اليومي/الأسبوعي — انظر `docs/shared/trip-actions-and-statuses.md`).
- **`action` على جداول الحجز (booking):** مسار البحث/البث للراكب (`0` مطابقة، `1/2/3` فشل بحث، `4` إرسال للكل) — **معجم منفصل** عن `action` الاقتراح.
- **`status` على `week-ride-booking`:** حالة مجموعة أسبوعية (`0` جديد، `1` مقبول، `2` مرفوض) من تسميات الداشبورد.
- **`group-id` على `week-ride-booking`:** معرّف المجموعة الأسبوعية (ما يعرضه تطبيق السائق كرقم الرحلة الأسبوعية).
- **`trip_cost` + `atariqi_percentage` على جداول sug:** لقطة سعر ونسبة عند إنشاء صف الاقتراح.

---

## 7. الأسطح الوظيفية المكتشفة

1. **API السائق** — `docs/driver/`
2. **API الراكب** — `docs/passenger/`
3. **الداشبورد** — `docs/dashboard/`
4. **الموقع العام** — `docs/website/`
5. **مشترك** (مصادقة، رحلات، دفع، cron، تكاملات) — `docs/shared/`
6. **مدفوعات Telr** — webhook في API + صفحات رجوع في web
7. **Wasl / رفقني وزارة** — أوامر + `WaslService`
8. **OTP/SMS** — `GuardsOtpSending` + helpers التسليم
9. **إشعارات FCM** — `sendNotification` في `helpers.php` (ليس Laravel Notifications)

---

## 8. Middleware ذات الصلة

| الاسم | الملف | الاستخدام |
|-------|-------|-----------|
| `locale` | `LocaleCheck` | لغة API/موقع |
| `auth:sanctum` | Sanctum | توكن الموبايل |
| `is_passenger` | `IsPassenger` | راكب فقط |
| `is_driver` | `IsDriver` | سائق فقط |
| `is_admin` | `IsAdmin` | جلسة أدمن |
| `admin.page` | `EnsureAdminPageAccess` | صلاحيات صفحات الداشبورد |
| `login.throttle` | `LoginThrottle` | حماية دخول الداشبورد |

---

## 9. فهرس سريع لكنترولرات API

### سائق (`app/Http/Controllers/Api/Driver/`)

`RegisterController`, `LoginController`, `PackageController`, `ProfileController`, `ServiceController`, `LocationController`, `DriverController`, `SummaryController`, `TripController`, `DailyTripsController`, `WeeklyTripController`, `ImmediateTripController`, `TripsGroupController`, `RevenueController`, `DuesController`, `AnnouncementController`, `SubscriptionController`, `WebhookController`, `PaymentController`

### راكب / مشترك API

`UserController`, `ImmediateDriverController`, `DailyDriverController`, `WeeklyDriverController`, `TripController` (Api), `HomeController` (Api)

### داشبورد / موقع

راجع `docs/dashboard/README.md` و `docs/website/README.md`.

---

## 10. الخطوة التالية للقراءة

1. `docs/01-INDEX.md` — فهرس كل المسارات
2. `docs/shared/trip-actions-and-statuses.md` — دورة حياة الرحلة
3. `docs/driver/README.md` / `docs/passenger/README.md`

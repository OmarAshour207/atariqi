# 01 — فهرس التوثيق الكامل

ابدأ من: [00-PROJECT-MAP.md](00-PROJECT-MAP.md)

---

## 1. خريطة التدفقات المتقاطعة

| التدفق | أين يُوثَّق |
|--------|-------------|
| تسجيل/OTP سائق أو راكب | `shared/auth-and-roles.md` + ملفات auth في driver/passenger |
| أونلاين / استقبال رحلات | `driver/profile-service-location.md` |
| إنشاء طلب راكب → اقتراح/بث | `passenger/immediate|daily|weekly.md` |
| قبول/بدء/توصيل/تقييم سائق | `driver/trips.md` + `shared/trip-actions-and-statuses.md` |
| ملخصات الموبايل + trip_cost | `driver/summary.md` + `shared/database-and-models.md` |
| باقات Telr ومستحقات | `shared/payments-dues-packages.md` + `driver/dues-revenue-packages.md` |
| إشعارات FCM/بريد | `shared/notifications.md` |
| إغلاق متأخر / تذكير / Wasl | `shared/cron-jobs-and-schedulers.md` |
| موافقات داشبورد | `dashboard/drivers.md`, `passengers-users.md` |

---

## 2. ملفات حسب السطح

| السطح | الملف | ماذا يغطي |
|-------|-------|-----------|
| جذر | `00-PROJECT-MAP.md` | جرد البنية |
| جذر | `01-INDEX.md` | هذا الفهرس |
| shared | `auth-and-roles.md` | حراس وأدوار |
| shared | `database-and-models.md` | جداول ونماذج |
| shared | `trip-actions-and-statuses.md` | دورة حياة الرحلة |
| shared | `payments-dues-packages.md` | دفع وباقات |
| shared | `notifications.md` | FCM/OTP/Mail |
| shared | `cron-jobs-and-schedulers.md` | أوامر مجدولة |
| shared | `queues-jobs-events.md` | لا Jobs/Events |
| shared | `observers-and-model-hooks.md` | لقطة السعر |
| shared | `third-party-integrations.md` | Telr/Wasl/FCM |
| driver | `README.md` + ملفات الكنترولر | كل API سائق |
| passenger | `README.md` + ملفات | كل API راكب |
| dashboard | `README.md` + ملفات | لوحة التحكم |
| website | `README.md` | الموقع العام |

---

## 3. جدول المسارات — API (`/api` + `routes/api.php`)

| METHOD | المسار الكامل | التوثيق |
|--------|---------------|---------|
| POST | `/api/webhook/telr` | `driver/payments-webhook.md` |
| POST | `/api/test/notify` | `passenger/home-config.md` |
| GET | `/api/get/config` | `passenger/home-config.md` |
| GET | `/api/get/announce` | `passenger/home-config.md` |
| GET | `/api/get/contacts` | `passenger/home-config.md` |
| POST | `/api/user/register` | `passenger/auth-profile.md` |
| POST | `/api/user/send` | `passenger/auth-profile.md` |
| POST | `/api/user/verify` | `passenger/auth-profile.md` |
| POST | `/api/driver/register` | `driver/register-login.md` |
| POST | `/api/driver/login` | `driver/register-login.md` |
| POST | `/api/driver/verify` | `driver/register-login.md` |
| POST | `/api/driver/packages` | `driver/dues-revenue-packages.md` |
| POST | `/api/drivers/times/{id}` | `passenger/immediate.md` |
| POST | `/api/trip/update/location` | `passenger/trip-current-location.md` |
| POST | `/api/trip/current` | `passenger/trip-current-location.md` |
| POST | `/api/drivers/immediate/transport` | `passenger/immediate.md` |
| POST | `/api/immediate/transport/trips` | `passenger/immediate.md` |
| POST | `/api/immediate/transport/change/action` | `passenger/immediate.md` |
| POST | `/api/immediate/transport/execute` | `passenger/immediate.md` |
| POST | `/api/drivers/rate` | `passenger/immediate.md` |
| POST | `/api/drivers/daily/transport` | `passenger/daily.md` |
| POST | `/api/drivers/daily/select` | `passenger/daily.md` |
| POST | `/api/drivers/daily/send/all` | `passenger/daily.md` |
| POST | `/api/daily/transport/get/notifications` | `passenger/daily.md` |
| POST | `/api/daily/transport/get/summary` | `passenger/daily.md` |
| POST | `/api/daily/transport/trip` | `passenger/daily.md` |
| POST | `/api/daily/transport/execute` | `passenger/daily.md` |
| POST | `/api/daily/transport/change/action` | `passenger/daily.md` |
| POST | `/api/drivers/weekly/transport` | `passenger/weekly.md` |
| POST | `/api/drivers/weekly/select` | `passenger/weekly.md` |
| POST | `/api/drivers/weekly/send/all` | `passenger/weekly.md` |
| POST | `/api/weekly/transport/get/notifications` | `passenger/weekly.md` |
| POST | `/api/weekly/transport/get/summary` | `passenger/weekly.md` |
| POST | `/api/weekly/transport/trip` | `passenger/weekly.md` |
| POST | `/api/weekly/transport/execute` | `passenger/weekly.md` |
| POST | `/api/weekly/transport/change/action` | `passenger/weekly.md` |
| POST | `/api/profile/edit` | `passenger/auth-profile.md` |
| POST | `/api/driver/general/update` | `driver/profile-service-location.md` |
| POST | `/api/driver/info/update` | `driver/profile-service-location.md` |
| POST | `/api/driver/car/update` | `driver/profile-service-location.md` |
| POST | `/api/driver/transport/update` | `driver/profile-service-location.md` |
| POST | `/api/driver/transport/index` | `driver/profile-service-location.md` |
| POST | `/api/driver/service/start` | `driver/profile-service-location.md` |
| POST | `/api/driver/service/stop` | `driver/profile-service-location.md` |
| POST | `/api/driver/receiving-rides/toggle` | `driver/profile-service-location.md` |
| GET | `/api/driver/receiving-rides/status` | `driver/profile-service-location.md` |
| POST | `/api/driver/location/update` | `driver/profile-service-location.md` |
| POST | `/api/driver/rate` | `driver/announcements-rate.md` |
| POST | `/api/driver/summary` | `driver/summary.md` |
| GET | `/api/driver/summary/search` | `driver/summary.md` |
| POST | `/api/driver/trip/action/update` | `driver/trips.md` |
| GET | `/api/driver/trip/{type}/{id}` | `driver/trips.md` |
| POST | `/api/driver/trip/start` | `driver/trips.md` |
| POST | `/api/driver/trip/delivery/update` | `driver/trips.md` |
| POST | `/api/driver/trip/rate` | `driver/trips.md` |
| GET | `/api/driver/trips/daily` | `driver/trips.md` |
| POST | `/api/driver/trips/daily/accept` | `driver/trips.md` |
| GET | `/api/driver/weekly/{group_id}` | `driver/trips.md` |
| POST | `/api/driver/weekly/action/update` | `driver/trips.md` |
| GET | `/api/driver/immediate/get` | `driver/trips.md` |
| POST | `/api/driver/revenue` | `driver/dues-revenue-packages.md` |
| GET | `/api/driver/dues` | `driver/dues-revenue-packages.md` |
| POST | `/api/driver/dues/pay` | `driver/dues-revenue-packages.md` |
| GET | `/api/driver/announcements` | `driver/announcements-rate.md` |
| GET | `/api/driver/trips/{type}/today` | `driver/trips.md` |
| POST | `/api/driver/trips/group/start` | `driver/trips.md` |
| POST | `/api/driver/trips/group/get` | `driver/trips.md` |
| POST | `/api/driver/trip/current` | `driver/trips.md` |
| POST | `/api/driver/subscribe` | `driver/dues-revenue-packages.md` |
| POST | `/api/driver/cancel` | `driver/dues-revenue-packages.md` |
| — | `driver/renew|upgrade|downgrade` | **معلّقة** — نفس الملف |

---

## 4. جدول المسارات — Web (`routes/web.php`)

| METHOD | المسار | التوثيق |
|--------|--------|---------|
| GET | `/` | `website/README.md` |
| GET/POST | `/support`, `/support/track` | `website/README.md` |
| GET | `/homepage-sections` | `website/README.md` |
| GET | `/locale/{locale}` | `website/README.md` |
| GET/POST | `/dashboard/login` | `dashboard/auth.md` |
| POST | `/dashboard/logout` | `dashboard/auth.md` |
| GET | `/dashboard/index` | `dashboard/README.md` |
| * | `/dashboard/homepage-sections*` | `dashboard/home-cms.md` |
| * | `/dashboard/homepage-stats*` | `dashboard/home-cms.md` |
| * | `/dashboard/testimonials*` | `dashboard/home-cms.md` |
| * | `/dashboard/partner-achievements*` | `dashboard/home-cms.md` |
| * | `/dashboard/packages*` | `dashboard/packages-features.md` |
| * | `/dashboard/features*` | `dashboard/packages-features.md` |
| * | مسارات السائقين المخصّصة + Resource | `dashboard/drivers.md` |
| * | الركاب/المستخدمون/الشكاوى/الرحلات | `dashboard/passengers-users.md` |
| * | الدعم والإعلانات | `dashboard/support-announcements.md` |
| * | جامعات/مدن/خدمات/مستندات | `dashboard/geo-services-docs.md` |
| * | موظفون/أدوار/سجلات/إعدادات/ملف | `dashboard/employees-roles-logs-settings.md` |
| GET | `/payment/telr/{success\|failed\|declined}` | `driver/payments-webhook.md` |

توسعة Resource الكاملة مذكورة في `dashboard/README.md` مع الميثودات الناقصة.

---

## 5. Cron / Jobs / Observers

| العنصر | الملف |
|--------|-------|
| كل أوامر Kernel | `shared/cron-jobs-and-schedulers.md` |
| لا Jobs/Events | `shared/queues-jobs-events.md` |
| SnapshotsAtariqiPercentage | `shared/observers-and-model-hooks.md` |

---

## 6. مراجعة اكتمال التوثيق

- كل route ظاهر في `routes/api.php` و`routes/web.php` له صف في الجداول أعلاه أو إشارة صريحة (معلّق / Resource ناقص).
- السلوك الأوتوماتيك (cron + creating snapshot + إغلاق فوري قديم) موثّق في shared.
- إن وُجدت مسارات ديناميكية خارج هذين الملفين فهي **غير مؤكدة من كود التوجيه الحالي**.

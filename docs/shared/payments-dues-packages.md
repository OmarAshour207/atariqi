# المدفوعات والمستحقات والباقات

## 1. Telr

| مكوّن | المسار |
|-------|--------|
| الخدمة | `app/Services/TelrService.php` |
| إنشاء جلسة دفع | من `SubscriptionController` و `DuesController` |
| Webhook | `POST /api/webhook/telr` → `WebhookController@handleWebhook` (بدون auth) |
| صفحات الرجوع | `GET /payment/telr/success\|failed\|declined` → Blade فقط |

أنواع `Order.type` من الكود: `subscription`, `upgrade`, `pay_due`.  
حالات الطلب: `1` pending، `2` completed، `3` failed.

عند `tran_status === 'A'` في الـ webhook:

- subscription → تفعيل `UserPackage` + history  
- upgrade → أرشفة القديم + باقة جديدة  
- pay_due → `FinancialDue` + إكمال الطلب  

غير ذلك → فشل الطلب.

---

## 2. باقات السائق (API)

| Endpoint | Auth | الوظيفة |
|----------|------|---------|
| `POST /api/driver/packages` | عام | قائمة الباقات `PackageResource` |
| `POST /api/driver/subscribe` | سائق | إنشاء Order + رابط Telr |
| `POST /api/driver/cancel` | سائق | إلغاء النشط → باقة مجانية سنوية |
| `upgrade` / `renew` / `downgrade` | — | مسارات **معلّقة** في `routes/api.php`؛ `upgrade` موجود كميثود، `renew`/`downgrade` غير موجودين كميثودات مكتملة |

ثوابت الباقة (`Package`): `FREE=0`, `SOON=1`, `NEW=2`, `DISCOUNT=3`.  
`UserPackage`: `STATUS_ACTIVE=1`, `EXPIRED=2`, `CANCELLED=3`.

---

## 3. المستحقات (Dues)

| Endpoint | الوظيفة |
|----------|---------|
| `GET driver/dues` | `DriverDuesService::summary()` — إيرادات فترة، نسبة، `can_accept_trips`, رسائل أبشر |
| `POST driver/dues/pay` | Order من نوع `pay_due` + Telr |

منع قبول الرحلات عند المستحقات العالية: trait `ChecksDriverDues` يستدعي `canAcceptTrips()`.

نسبة المستحقات العامة: سجل في `subscriptions` (`type=2`) تُدار من الداشبورد `GeneralDuesPercentageController` وتُلتقط على sug عبر Trait اللقطة.

---

## 4. الإيرادات

`POST driver/revenue` مع `start_date`/`end_date`:  
يحسب عبر trait `Payment` رحلات منتهية فقط:

- فوري: suggestion `action=5`  
- يومي/أسبوعي: suggestion `action=6`  

المخرجات: `immediate`, `daily`, `weekly`, `total`, `total_dues`.

---

## 5. الباقات من الداشبورد

`Dashboard\PackageController`: إنشاء/تحديث/حذف يرسل بريدًا **للسائقين** (`user-type=driver`) عبر:

- `NewPackageNotificationMail`  
- `PackageUpdateNotificationMail`  
- `PackageDeletedNotificationMail`  

تعيين/إلغاء باقة لسائق محدد: `DriverController@assignPackage` / `cancelPackage` + إيميلات التعيين/الإلغاء.

---

## 6. Cron ذو صلة

- `payment-reminder` يوميًا 12:00 — تذكير بريد عند مستحقات ≥ 50  
- `check-finished-subscriptions` يوميًا 00:05 — انتهاء الباقات النشطة → مجانية  

انظر `cron-jobs-and-schedulers.md`.

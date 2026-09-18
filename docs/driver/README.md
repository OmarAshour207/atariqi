# API السائق — فهرس القسم

**البادئة:** `/api/driver/...` (ما عدا التسجيل/الدخول/الباقات العامة وwebhook)  
**Auth الافتراضي:** `locale` + `auth:sanctum` + `is_driver`  
**مجلد الكود:** `app/Http/Controllers/Api/Driver/`

## ملفات التوثيق

| الملف | الكنترولر |
|-------|-----------|
| [register-login.md](register-login.md) | Register + Login |
| [profile-service-location.md](profile-service-location.md) | Profile + Service + Location |
| [summary.md](summary.md) | SummaryController |
| [trips.md](trips.md) | Trip + Daily + Weekly + Immediate + Group |
| [dues-revenue-packages.md](dues-revenue-packages.md) | Dues + Revenue + Package + Subscription |
| [announcements-rate.md](announcements-rate.md) | Announcement + DriverRate |
| [payments-webhook.md](payments-webhook.md) | Webhook + Payment pages |

## Traits مشتركة

| Trait | الملف | الدور |
|-------|-------|------|
| `ChecksDriverWaslStatus` | `Traits/ChecksDriverWaslStatus.php` | حظر تشغيل الرحلات حسب `approval` |
| `ChecksDriverDues` | `Traits/ChecksDriverDues.php` | حظر القبول عند المستحقات |
| `ChecksDriverSameTimeTrips` | `Traits/ChecksDriverSameTimeTrips.php` | تعارض وقت يومي/أسبوعي (يتجاهل action 2 و 5) |
| `Payment` | `Traits/Payment.php` | حساب إيرادات/مستحقات |
| `GuardsOtpSending` | على Login | OTP |

## غلاف الاستجابة

من `Api\BaseController`: نجاح `{ success, data, message }`؛ فشل `{ success:false, message, errors }`.  
استثناء: بعض مسارات الدفع تُرجع شكلًا مخصصًا (`payment_url`).

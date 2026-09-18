# الأوامر المجدولة (Cron / Schedule)

المصدر: `app/Console/Kernel.php`.

---

## 1. `notify-driver-nearby-trips` — كل 5 دقائق

| | |
|--|--|
| **الكلاس** | `App\Console\Commands\NotifyDriverForNearbyTrips` |
| **الغرض** | تنبيه سائق أن رحلته اليومية/الأسبوعية خلال 5 دقائق |
| **شرط البيانات** | `date-of-ser = اليوم` و `time-go` أو `time-back` بين الآن و الآن+5د |
| **الجداول** | قراءة `sug-day-drivers` / `sug-week-drivers` + booking؛ يستخدم `fcm_token` |
| **الكتابة** | لا كتابة DB — فقط FCM |
| **عند الفشل** | لا try/catch شامل؛ الأمر يعيد SUCCESS إن اكتمل |

---

## 2. `payment-reminder` — يوميًا 12:00

| | |
|--|--|
| **الكلاس** | `App\Console\Commands\PaymentReminder` |
| **الغرض** | تذكير سائقين معتمدين بمستحقات ≥ 50 |
| **الجداول** | قراءة مستحقات؛ كتابة `payment_reminders`؛ بريد `PaymentReminderMail` |
| **منطق التكرار** | يتجنّب إعادة التذكير وفق سجل التذكير السابق (منطق يعتمد على عمر السجل — راجع الملف) |
| **عند الفشل** | استثناء البريد قد يوقف الـ chunk |

---

## 3. `delete-late-trips` — يوميًا 01:00

| | |
|--|--|
| **الكلاس** | `App\Console\Commands\DeleteLateRidesCommand` |
| **الغرض** | إغلاق رحلات متأخرة (تحديث `action` وليس حذفًا فعليًا بالاسم) |

التحويلات من الكود:

| جدول | شرط | إلى |
|------|------|-----|
| `suggestions-drivers` | `date-of-add` قبل اليوم و `action=1` | `action=4` |
| `sug-day-drivers` | تاريخ خدمة قبل اليوم و `action in (0,1)` | `action=2` |
| `day-ride-booking` | تاريخ قبل اليوم و `action in (0,4)` | `action=3` |
| `sug-week-drivers` | تاريخ قبل اليوم و `action in (0,1)` | `action=2` + `date-of-edit` |
| `week-ride-booking` | كود تحديث معلّق بتعليق | — |

---

## 4. `check-finished-subscriptions` — يوميًا 00:05

| | |
|--|--|
| **الكلاس** | `CheckFinishedSubscriptionsCommand` |
| **الغرض** | إنهاء باقات `end_date` منتهية → باقة مجانية سنوية |
| **جداول** | `user_packages`, `user_package_histories`, `packages` |

---

## 5. `drivers:check-wasl-eligibility` — يوميًا 02:00

| | |
|--|--|
| **الكلاس** | `CheckDriverWaslEligibilityCommand` |
| **الغرض** | فحص أهلية Wasl للسائقين `approval in (0,1,4)` بهوية |
| **أثر** | قد يضع `approval=4` أو يعيد لحالة انتظار عبر `WaslService` |
| **فشل جزئي** | يُسجَّل في قناة `wasl` ويكمل الباقي |

---

## 6. `wasl:sync-driver-locations` — كل دقيقة

| | |
|--|--|
| **الكلاس** | `SyncWaslDriverLocationsCommand` |
| **الغرض** | مزامنة مواقع السائقين النشطين/المستقبلين للرحلات مع Wasl |
| **إن WASL معطّل** | تحذير ويعود SUCCESS بدون عمل |

---

## 7. `wasl:sync-provinces` — **غير مجدول حاليًا**

التعليق في Kernel: كان أسبوعيًا الأحد 03:00.  
الأمر موجود يدويًا؛ يفشل إن `WASL_ENABLED=false`.

---

## 8. أوامر غير مجدولة أخرى

لا يوجد غيرها تحت `Commands/` بخلاف القائمة أعلاه + ما ذُكر.

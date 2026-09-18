# Observers و Model Hooks

## 1. Eloquent Observers

المجلد `app/Observers` **غير موجود**. لا تسجيل Observers في مزوّدات الخدمة ظاهر في الجرد.

---

## 2. Trait: `SnapshotsAtariqiPercentage`

**الملف:** `app/Models/Concerns/SnapshotsAtariqiPercentage.php`  
**الموديلات:** `SuggestionDriver`, `SugDayDriver`, `SugWeekDriver`

### حدث `creating`

| الشرط | الإجراء |
|-------|--------|
| `atariqi_percentage === null` | تعبئة من `Subscription::generalDuesPercentageValue()` |
| `trip_cost === null` | تعبئة من `booking()->with('service')->… service.cost` |

### دوال لاحقة (ليست events)

- `snapshottedTripCost()` — يعيد اللقطة أو fallback للخدمة  
- `snapshottedAtariqiPercentage()`  
- `companyAmount()` / `driverAmount()` / `duesAmountForCost()`

**متى يُستدعى؟** أي `Sug*::create(...)` من مسارات القبول/الاختيار/البث.

---

## 3. سلوكيات «أوتوماتيك» داخل الكنترولر (ليست Observer)

| المكان | السلوك |
|--------|--------|
| `ImmediateTripController@filterTrips` | اقتراح فوري قديم → `action=4` |
| `TripController@updateAction` (فوري) | حذف اقتراحات سائقين آخرين لنفس الحجز |
| `ChecksPassengerRideConflicts::cleanupFailedSearchDailyBookings` | حذف حجوزات يومية فاشلة بلا sug |
| أوامر الـ schedule | انظر `cron-jobs-and-schedulers.md` |

---

## 4. DB Triggers

لم يُعثر على تعريفات `TRIGGER` داخل `database/migrations`.  
إن وُجدت على السيرفر خارج المستودع فهي **غير موثّقة هنا** («غير مؤكد من كود المستودع»).

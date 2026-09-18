# دورة حياة الرحلة — actions و statuses

مصادر التسميات: `app/Helper/helpers.php` + مفاتيح `trip_action.*` / `trip_status.weekly.*` في `lang/ar.json` و `lang/en.json`.  
مصادر الكتابة: كنترولرات API الراكب/السائق + أمر `delete-late-trips`.

**مهم:** هناك معجمان مختلفان باسم `action`:

1. **`action` على جداول الاقتراح (sug)** = حالة الرحلة مع السائق (ما يظهر في بطاقات الموبايل والداشبورد عبر `trip_action_label`).
2. **`action` على جداول الحجز (booking)** = نتيجة بحث الراكب / البث للكل.

لا تخلط بينهما.

---

## 1. أنواع الرحلات واتجاه الطريق

| النوع | جداول | ملاحظة |
|-------|-------|--------|
| `immediate` | `ride-booking` + `suggestions-drivers` | فوري |
| `daily` | `day-ride-booking` + `sug-day-drivers` | يومي |
| `weekly` | `week-ride-booking` + `sug-week-drivers` | أسبوعي + `group-id` |

| `road-way` | المعنى من الاستخدام |
|------------|---------------------|
| `to` | إلى الجامعة — يعتمد `time-go` |
| `from` | من الجامعة — يعتمد `time-back` |
| `both` | يُنشئ صفّي حجز (to ثم from) في اليومي/الأسبوعي عند الاختيار/البث |

---

## 2. Action الاقتراح — يومي وأسبوعي (scheduled)

من `trip_action_label(..., 'daily'|'weekly')`:

| قيمة | التسمية (عربي) | استخدامات بارزة في الكود |
|------|----------------|---------------------------|
| 0 | جديد | إنشاء عند `selectDriver`؛ قائمة فورية قديمة تستخدم 0 للانتظار |
| 1 | مقبول | قبول السائق؛ قبول من بث يومي؛ قبول مجموعة أسبوعية |
| 2 | مرفوض | رفض؛ أمر التأخير يحوّل 0/1 المتأخرة إلى 2 |
| 3 | بدأت (وقت وصول متوقع) | شرط `executeRide` للراكب (يومي/أسبوعي) |
| 4 | قيد التنفيذ | رحلات «الحالية» في `TripController@getPassengerTrips` / السائق؛ Wasl ongoing |
| 5 | ملغاة | تُستثنى من تعارض الوقت ومن تعارض الركاب الأسبوعي |
| 6 | منتهية | إيرادات/مستحقات يومي وأسبوعي (`Payment` trait / finished scopes) |

---

## 3. Action الاقتراح — فوري (immediate)

| قيمة | التسمية (عربي) | استخدامات بارزة |
|------|----------------|-----------------|
| 7 | جديد | تظهر في التسميات؛ إنشاء الاقتراح غالبًا بـ `0` في كود البحث — **تحقق عند القراءة**: `ImmediateDriverController` يكتب `action=0` عند الإنشاء |
| 0 | مرفوض / غير مقبول | أيضًا صفوف انتظار في `ImmediateTripController` قبل الانتهاء |
| 1 | مقبول وبدأت | قبول؛ `execute` يقبل 1 أو 2 |
| 2 | وصل السائق | |
| 3 | ألغى الراكب | |
| 4 | مرفوض / راكب لم يحضر | انتهاء صلاحية عرض الفوري (~دقيقة) في `ImmediateTripController`; أمر التأخير للقديم `action=1` |
| 5 | منتهية | إيراد فوري / `scopeFinishedTrips` |

لا يوجد action `6` في match الفوري داخل `helpers.php`.

---

## 4. Action الحجز (booking) — يومي / فوري / أسبوعي

من مسارات `getDrivers` / `selectDriver` / `sendToAllDrivers`:

| قيمة | المعنى في كود الراكب |
|------|----------------------|
| 0 | تم اختيار سائق / مسار ناجح مع اقتراحات |
| 1 | فشل بحث: لا سائقين للجامعة/الخدمة/الجنس |
| 2 | فشل بحث: لا سائقين للحي/الخدمة |
| 3 | فشل بحث: لا جدول مناسب **أو** إغلاق متأخر عبر cron للحجوزات اليومية `{0,4}` |
| 4 | بث للكل (`sendToAllDrivers`) — طلب مفتوح للسائقين |

بعد قبول سائق لبث يومي (`DailyTripsController@accept`): الحجز يصبح `action=0` ويُنشأ sug بـ `action=1`.

---

## 5. Status المجموعة الأسبوعية (`week-ride-booking.status`)

من `weekly_group_status_label`:

| قيمة | عربي |
|------|------|
| 0 | جديد |
| 1 | مقبول |
| 2 | مرفوض |

يُحدَّث عبر `POST driver/weekly/action/update` مع `status` في الطلب.  
فلتر الموبايل `filter[status]` في `GET driver/summary/search?type=weekly` يعمل على **حجز** الأسبوع عبر scope، وليس على `sug-week-drivers.action`.

---

## 6. تسلسل مبسّط

### يومي — اختيار سائق

1. راكب: بحث `drivers/daily/transport` (قد ينشئ حجوزات فشل فقط)  
2. راكب: `drivers/daily/select` → booking `action=0` + sug `action=0` + FCM للسائق  
3. سائق: `trip/action/update` بـ `action=1` (قبول) أو غيره  
4. سائق: `trip/start` يضبط action + delivery info  
5. راكب: `executeRide` يتطلب sug `action=3` ونافذة زمنية ±5 دقائق  
6. إنهاء: sug `action=6` (للمحاسبة)

### يومي — بث للكل

1. راكب: `send/all` → booking `action=4`  
2. سائق: `GET trips/daily` يرى الحجوزات `action=4`  
3. سائق: `trips/daily/accept` → sug `1` + booking `0`

### أسبوعي

- كل الأيام تشترك في `group-id`.  
- قائمة السائق `summary/search?type=weekly`: تجميع حسب `group-id`.  
- `filter[action]=0` + وجود sug للسائق = «رحلاتي»؛ `filter[action]=4` = بث مفتوح.  
- `tag=my|all` في `weekly/action/update`: تحديث sug موجود أو إنشاء sug عند القبول من «الكل».

### فوري

1. بحث ينشئ booking + suggestions `action=0`  
2. سائق يقبل عبر `trip/action/update`  
3. عند القبول تُحذف اقتراحات السائقين الآخرين لنفس الحجز  
4. الإنهاء المحاسبي: suggestion `action=5`

---

## 7. السعر: `trip_cost` مقابل `service.cost`

| الحقل | المصدر | الغرض |
|-------|--------|-------|
| `trip.trip_cost` | لقطة على صف sug عند الإنشاء (أو fallback لـ service.cost) | عرض سعر الرحلة وقت الحجز في الملخصات |
| `service_id.cost` | جدول `services` الحي | سعر الكتالوج الحالي |

التفاصيل في Resources: `ResolvesTripCost` + Trait اللقطة.

---

## 8. فلاتر `GET driver/summary/search`

| Param | أثر |
|-------|-----|
| `type=daily\|weekly\|immediate` | اختيار النموذج/المسار |
| `type=weekly` | **دائمًا** مسار `WeekRideBooking` مجمع بـ `group-id` (لا يمر على فرع SugWeeklyDriverResource داخل نفس الميثود بعد الشرط المبكر) |
| `filter[action]` | على الحجز الأسبوعي: `0` يضيف `whereHas sugDriver` لهذا السائق؛ `4` بث |
| `filter[status]` | على `week-ride-booking.status` |
| `filter[date]` | نطاق تاريخ الخدمة عبر scopes |
| `sort` | `SortByDate` / `SortByRate` |

للأنواع غير الأسبوعية: QueryBuilder على `SugDayDriver` أو `SuggestionDriver` مع `driver-id = auth`.

---

## 9. من ينقل الحالة بدون زر من الفرونت؟

| الآلية | ماذا تفعل |
|--------|-----------|
| `delete-late-trips` | يغلق رحلات متأخرة (تحويلات action أعلاه) |
| `ImmediateTripController@index` | يحوّل اقتراحات فورية أقدم من ~دقيقة من انتظار إلى `action=4` |
| `SnapshotsAtariqiPercentage` | يكتب `trip_cost` / النسبة عند إنشاء sug فقط |
| قبول فوري | يحذف اقتراحات السائقين الآخرين |

لا يوجد Observer رحلات منفصل.

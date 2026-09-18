# ملخص رحلات السائق (Summary)

**الملف:** `app/Http/Controllers/Api/Driver/SummaryController.php`  
**الشاشة النموذجية:** تاريخ الملخص / طلباتي حسب النوع

---

## POST /api/driver/summary

- **الكود:** `summaryAll`
- **Auth:** سائق
- **Request:** `date` اختياري `Y-m-d` (على `date-of-add` للاقتراحات)
- **الخطوات:** جلب كل `SugWeekDriver` / `SugDayDriver` / `SuggestionDriver` للسائق مع علاقات الحجز
- **Response `data`:**
  - `weekly` → `SugWeeklyDriverResource` (فيها `trip` + `trip_cost`)
  - `daily` → `SugDayDriverResource`
  - `immediate` → `SugDriverResource`
- **من يستدعيه:** شاشة ملخص يوم محدد في تطبيق السائق

---

## GET /api/driver/summary/search

- **الكود:** `summary`
- **Query مطلوب:** `type` = `daily` | `weekly` | `immediate`
- **Query اختياري:** `filter[date]`, `filter[action]`, `filter[status]`, `sort`

### عندما `type=weekly`

يُستدعى داخليًا `weeklyAllTripsSummary`:

- المصدر: `WeekRideBooking` + Spatie filters  
- تجميع النتائج حسب `group-id`  
- إن `filter[action]=0`: يقتصر على حجوزات لها `sugDriver` لهذا السائق  
- الاستجابة: `WeekRideBookingGroupResource` = `{ group-id, data: [ رحلات الأيام ] }` وكل عنصر فيه `trip_cost`, `status`, `action`, …

**تفسير الموبايل الشائع (من عقد التطبيق السابق + الكود):**

| فلتر | المعنى التشغيلي |
|------|------------------|
| `filter[action]=0` | مجموعاتي (مرتبطة بـ sug للسائق) |
| `filter[action]=4` | طلبات البث المفتوحة |
| `filter[status]` | حالة المجموعة على الحجز 0/1/2 |
| رقم الرحلة المعروض | `group-id` وليس `booking-id` ليوم واحد |

### عندما `type=daily`

- QueryBuilder على `SugDayDriver` حيث `driver-id=auth`  
- Resources: `SugDayDriverResource`  
- كل عنصر: `trip.trip_cost` + `trip.service_id.cost`

### عندما `type=immediate` (وإلا)

- `SuggestionDriver` + `SugDriverResource`

### فشل التحقق

422 مع رسائل Validator.

---

## ملاحظات

- فرع `SugWeeklyDriverResource` داخل `summary()` بعد شرط weekly **غير قابل للوصول** لأن weekly يُرجع مبكرًا — وثّق كسلوك حالي في الكود.
- تفاصيل معاني action/status: `docs/shared/trip-actions-and-statuses.md`

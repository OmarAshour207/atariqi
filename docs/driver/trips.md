# رحلات السائق — تنفيذ وقبول ومجموعات

**الملفات:** `TripController`, `DailyTripsController`, `WeeklyTripController`, `ImmediateTripController`, `TripsGroupController`  
**Traits شائعة:** Wasl + Dues + SameTime على مسارات القبول/البدء

---

## POST /api/driver/trip/action/update

- **الكود:** `TripController@updateAction`
- **Request:** `type` in daily|weekly|immediate؛ `action` numeric max 6؛ `id` رقم صف الـ sug
- **الغرض:** تحديث حالة اقتراح السائق
- **حماية:** Wasl دائمًا؛ عند `action===1` مستحقات + تعارض وقت
- **فوري:** بعد التحديث يحذف اقتراحات السائقين الآخرين لنفس `booking-id`
- **يومي قبول:** قد يرسل FCM للراكب
- **W:** جدول الـ sug المناسب
- **Response:** نموذج الرحلة الخام

---

## GET /api/driver/trip/{type}/{id}

- **الكود:** `TripController@get`
- **الغرض:** تفاصيل رحلة للسائق
- **Resources:** يومي `SugDayDriverResource`؛ أسبوعي `SugWeeklyDriverResource`؛ فوري `SuggestionDriver` resource
- يضيف إحداثيات مصدر/وجهة حسب `road-way`

---

## POST /api/driver/trip/start

- **الكود:** `TripController@start`
- **Request:** `type`, `action`, `id`, `expect-arrived`
- **الخطوات:** يستدعي منطق `updateAction` ثم ينشئ/يحدّث سجل التوصيل (`Del*Info`) بالحقول ETA/إعاقات؛ إشعارات قبول/تحول لمجموعة في الفوري عند تعدد قبول نفس اليوم
- **Side effects:** FCM

---

## POST /api/driver/trip/delivery/update

- **الكود:** `TripController@updateDelivery`
- **Request:** `type`, `sug-id`, حقول وصول اختيارية، `passenger-rate` ≤5، `allow-disabilities`
- **W:** جدول التوصيل؛ قد يحدّث متوسط `PassengerRate`

---

## POST /api/driver/trip/rate

- **الكود:** `TripController@rate`
- **Request:** `type`, `rate`≤5، `comment`, `sug-id`
- **W:** `DayUnrideRate` / `WeekUnrideRate` / `ImmediateUnrideRate`
- **الغرض:** شكوى/تقييم عدم إتمام من جهة السائق

---

## GET /api/driver/trips/daily

- **الكود:** `DailyTripsController@get`
- **الغرض:** طلبات يومية مفتوحة (`day-ride-booking.action=4`) المطابقة لخدمات/أحياء السائق
- **Response:** `DayRideBookingResource` (+ `trip_cost`)
- **الشاشة:** «طلبات كل السائقين» اليومية

---

## POST /api/driver/trips/daily/accept

- **الكود:** `DailyTripsController@accept`
- **Request:** `id` = معرف `day-ride-booking`
- **حماية:** Wasl + dues + same-time
- **W:** إنشاء `SugDayDriver` بـ `action=1`؛ تحديث الحجز إلى `action=0`
- **لقطة:** `trip_cost` تُكتب عند create عبر Trait

---

## GET /api/driver/trips/{type}/today

- **الكود:** `DailyTripsController@getToday`
- **ملاحظة:** باراميتر `{type}` في المسار **غير مستخدم داخل الميثود** (من قراءة الكود)
- **الغرض:** رحلات يومية مقبولة (`sug.action=1`) لليوم ضمن نافذة ±10 دقائق لوقت الذهاب/العودة
- **Response:** `{ to: [...], from: [...] }` بـ `SugDayDriverDetailsResource`

---

## `reject` في DailyTripsController

- ميثود عامة **فارغة** — **لا route** مرتبط · كود ميت جزئيًا

---

## GET /api/driver/weekly/{group_id}

- **الكود:** `WeeklyTripController@get`
- **Response:** `WeekRideBookingGroupDetails` لكل يوم في المجموعة
- **الشاشة:** تفاصيل رحلة أسبوعية بالمعرّف `group-id`

---

## POST /api/driver/weekly/action/update

- **الكود:** `WeeklyTripController@updateAction`
- **Request:** `group_id`, `status`, `tag` in `my|all`, `action` مطلوب إن `tag=my`
- **الخطوات لكل حجز في المجموعة:**
  - تحديث `week-ride-booking.status` و `action=0` على الحجز
  - `tag=my`: تحديث `sugDriver.action`
  - `tag=all`: إنشاء `SugWeekDriver` بـ `action=1`
- **حماية:** Wasl/dues/same-time عند القبول
- **Side effects:** FCM للراكب عند القبول (نص الرسالة يستخدم مفتاح `group` من الطلب — راجع الكود إن لزم التطابق مع `group_id`)

---

## GET /api/driver/immediate/get

- **الكود:** `ImmediateTripController@index`
- **الغرض:** قائمة اقتراحات فورية للسائق بـ `action=0` مع إهمال/إغلاق القديمة (~دقيقة → `action=4`)
- **Response:** `SuggestionDriverDetailsResource`

---

## POST /api/driver/trips/group/start

- **الكود:** `TripsGroupController@store`
- **Request:** `type` daily|weekly؛ `trips` مصفوفة 2–3 عناصر كل منها `id`,`action`,`expect-arrived`
- **الغرض:** بدء مجموعة رحلات معًا + delivery + إشعار لكل راكب

---

## POST /api/driver/trips/group/get

- **الكود:** `TripsGroupController@get`
- **Request:** `type`, `trips` (JSON string يُفك)
- **Response:** مجموعة `SugDayDriverDetailsResource` (تُستخدم لكل الأنواع في الكود الحالي)

---

## POST /api/driver/trip/current

- **الكود:** `Api\TripController@getDriverTrips` (مشترك)
- **الغرض:** رحلات السائق «الحالية» مجمعة فوري/يومي/أسبوعي حسب قواعد action وتاريخ اليوم
- **أسبوعي:** يضيف `trips_count` بعدد أيام `group-id`

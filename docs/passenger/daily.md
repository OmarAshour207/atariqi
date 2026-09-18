# الرحلات اليومية — راكب

**الملف:** `DailyDriverController.php`  
**Concern:** `ChecksPassengerRideConflicts`

---

## POST /api/drivers/daily/transport

- **الكود:** `getDrivers`
- **الغرض:** بحث سائقين يوميين لتاريخ/أوقات
- **عند النجاح:** لا ينشئ حجزًا؛ ينظّف حجوزات فشل البحث لنفس اليوم
- **عند الفشل:** ينشئ `day-ride-booking` بـ action 1/2/3
- **فلتر السعة:** يستبعد سائقين لديهم أكثر من عدد معيّن من رحلات متزامنة (منطق `>2` في الكود)

---

## POST /api/drivers/daily/select

- **الكود:** `selectDriver`
- **طلب:** يتضمن `driver_id` + بيانات الرحلة
- **تعارض:** `findPassengerRideConflict` → 422
- **W:** حجز/حجوزات `action=0` + `SugDayDriver action=0` حسب road-way
- **Side effects:** FCM للسائق «عقد يومي جديد»
- **Response:** `DayRideBookingResource` + مجموعة `SugDayDrivingResource`

---

## POST /api/drivers/daily/send/all

- **الكود:** `sendToAllDrivers`
- **W:** `DayRideBooking` بـ `action=4` (updateOrCreate؛ يفصل both)
- **تعارض:** لا يوجد فحص تعارض هنا
- **Response:** `DayRideBookingResource`

---

## POST /api/daily/transport/get/notifications

- يجلب `SugDayDriver` غير معروض؛ يرسل FCM عند action 1 أو 2؛ يعلّم `viewed=1`

---

## POST /api/daily/transport/get/summary

- يدمج ملخص فوري (`SuggestionDriver` resource) + يومي (`SugDayDrivingResource`) لراكب المصادقة

---

## POST /api/daily/transport/trip

- تفاصيل sug يومي بالـ id للراكب → `SugDayDrivingResource`

---

## POST /api/daily/transport/execute

- نافذة زمنية اليوم ±5 دقائق على time-go/back  
- يشترط sug `action=3`  
- Response يتضمن `SugDayDrivingResource` ومفتاح action نصي للشاشة

---

## POST /api/daily/transport/change/action

- يحدّث `action` لصف `sug_day_driver_id` فقط

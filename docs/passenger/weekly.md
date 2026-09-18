# الرحلات الأسبوعية — راكب

**الملف:** `WeeklyDriverController.php`

## منطق `group-id`

- `getGroupId()`: آخر `group-id` للراكب + 1، أو `1` إن لم يوجد  
- كل أيام الأسبوع (وto/from) تشترك في نفس `group-id` عبر `saveWeekRideBooking`  
- تطبيق السائق يعرّف الرحلة بهذا الرقم (`GET driver/weekly/{group_id}`)

---

## POST /api/drivers/weekly/transport

- بحث سائقين لكل تواريخ `weekly_dates`  
- فشل → إنشاء مجموعة + حجوزات action 1/2/3 وإرجاع `group_id`  
- نجاح → لا حجوزات؛ قائمة سائقين فقط

---

## POST /api/drivers/weekly/select

- تعارض لكل تاريخ  
- مجموعة جديدة `action=0` + `SugWeekDriver action=0` لكل حجز  
- FCM للسائق  
- Response: `WeekRideBookingResource` collection

---

## POST /api/drivers/weekly/send/all

- **يتطلب** `group_id` قديم في الطلب  
- يحذف حجوزات المجموعة القديمة للراكب ثم ينشئ مجموعة جديدة بـ booking `action=4`  
- لا ينشئ suggestions هنا

---

## POST /api/weekly/transport/get/notifications

- مشابه لليومي مع نصوص مدى التواريخ

---

## POST /api/weekly/transport/get/summary

- كل `SugWeekDriver` للراكب  
- يحسب `weekly_days_count` لكل `group-id`  
- `SugWeekDriverResource` (+ `trip.trip_cost`)

---

## POST /api/weekly/transport/trip | execute | change/action

- تفاصيل / تنفيذ (يشترط sug action=3 ونافذة ±5د + FCM «اقبل الرحلة») / تغيير action لصف sug واحد  
- مفاتيح الاستجابة قد تستخدم اسم `sug_day_driver` حتى للأسبوعي (من الكود الحالي)

# التقييم والإعلانات

## POST /api/driver/rate

- **الكود:** `DriverController@driverRate` (اسم الميثود في PHP حساس لحالة الأحرف حسب التعريف الفعلي `DriverRate`/`driverRate`)
- **الغرض:** إحصاءات السائق: تقييم، رحلات منتهية/ملغاة، هل الخدمة بدأت، الباقة النشطة، بروفايل
- **عدّادات من الكود:**
  - منتهية: أسبوعي/يومي `action=6`؛ فوري `action=5`
  - ملغاة: أسبوعي/يومي `action=2`؛ فوري `action=4`
- **Response:** `rate`, `finished_rides`, `cancelled_rides`, `service_started`, `active_package`, `driver`

---

## GET /api/driver/announcements

- **الكود:** `AnnouncementController@index`
- **الغرض:** إعلانات تطبيق السائق
- **R:** `DriverAnnounce` مرتبة تنازليًا
- **Response:** مجموعة خام (بدون Resource مخصص)

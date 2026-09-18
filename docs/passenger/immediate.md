# الرحلات الفورية — راكب

**الملف:** `ImmediateDriverController.php`  
**Auth:** `is_passenger` ما عدا `getTimes`

---

## POST /api/drivers/immediate/transport

- **الكود:** `getDrivers`
- **الغرض:** البحث عن سائقين فوريين وإنشاء حجز + اقتراحات عند النجاح
- **Request:** `neighborhood_id`, `university_id`, `ride_type_id`, `lat`, `lng`
- **Booking action عند الفشل:** 1 / 2 / 3 حسب مرحلة الفلترة؛ عند النجاح: `0` + suggestions `action=0`
- **Response:** سائقون، إحداثيات، `trip`, مفتاح `action` نصي `immediate/transport/trips`

---

## POST /api/immediate/transport/trips

- **الكود:** `get`
- **Request:** `id` حجز
- **Response:** `RideBookingResource` + حالة وصول السائق + `driverinfo`

---

## POST /api/immediate/transport/execute

- **الكود:** `execute`
- **Request:** `booking_id`
- **يشترط:** suggestion للراكب بـ `action IN (1,2)`
- **Response:** رحلة + سائق + ETA من delivery

---

## POST /api/immediate/transport/change/action

- **الكود:** `changeAction`
- **Request:** `id` (حجز)، `action` رقمي
- **W:** **كل** suggestions الراكب لهذا الحجز بنفس القيمة (لا يحدّث booking.action — معلّق في الكود)

---

## POST /api/drivers/rate

- **الكود:** `rate`
- **Request:** `rate`≤5، `driver-id`, `trip_id`, `type` immediate|daily|weekly
- **W:** متوسط `driver-rate` على `driver-info`
- **Side effects:** `WaslService::storeTrip` حسب النوع

---

## POST /api/drivers/times/{id}

- **الكود:** `getTimes` — **بدون** `is_passenger`
- **الغرض:** نوافذ أوقات جدول السائق ± ساعتين
- **Response:** JSON خام لأيام to/from

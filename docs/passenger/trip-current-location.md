# الرحلة الحالية وتحديث الموقع

**الملف:** `app/Http/Controllers/Api/TripController.php`

---

## POST /api/trip/current

- **Auth:** راكب  
- **الكود:** `getPassengerTrips`
- **تجميع:**
  - فوري: حجوزات اليوم باقتراح action ∈ {1,2} → `SuggestDriverCurrentResource`
  - يومي: `date-of-ser` اليوم و sug `action=4` → `SuggestDailyCurrentResource`
  - أسبوعي: نفس فكرة اليوم و sug `action=4` → `SuggestWeeklyCurrentResource`

---

## POST /api/trip/update/location

- **Auth:** `auth:sanctum` فقط (سائق أو راكب)
- **Request:** `trip_id`, `type` immediate|daily|weekly, `lat`, `lng`
- **W:** `current-lat` / `current-lng` على جدول الحجز المناسب

---

## POST /api/driver/trip/current

موثّق ضمن قسم السائق؛ نفس الكلاس مع فلتر `driver-id`.

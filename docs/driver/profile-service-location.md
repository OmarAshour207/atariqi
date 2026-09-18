# الملف الشخصي والخدمة والموقع

**الملفات:** `ProfileController`, `ServiceController`, `LocationController`  
**Auth:** `auth:sanctum` + `is_driver`

---

## POST /api/driver/general/update

- **الكود:** `ProfileController@updateGeneral`
- **الغرض:** طلب تعديل بيانات عامة (أسماء، جوال، إيميل، صورة…) بانتظار موافقة الأدمن
- **W:** `NewUserInfo` (استبدال)، `users.approval=2`
- **Response:** رسالة نجاح معالجة
- **أوتوماتيك:** لا Observer — يظهر في داشبورد طلبات التعديل

---

## POST /api/driver/info/update

- **الكود:** `ProfileController@updateInfo`
- **الغرض:** طلب تعديل بيانات قيادة/سيارة نصية وصور اختيارية
- **W:** `NewDriverInfo`, `NewDriverCar`, ضمان `NewUserInfo`، `driverInfo.approval=2`, `users.approval=2`

---

## POST /api/driver/car/update

- **الكود:** `ProfileController@updateCar`
- **الغرض:** طلب استبدال صور السيارة/الرخصة
- **شرط:** صورة واحدة على الأقل
- **W:** `NewDriverCar`, approvals = 2

---

## POST /api/driver/transport/update

- **الكود:** `ProfileController@updateTransport`
- **الغرض:** حفظ أحياء العمل، الخدمات، الجدول الأسبوعي، ذوي الإعاقة
- **Request:** `neighborhood_to` و/أو `neighborhood_from` (JSON)، `times.*`, `services.*`, `allow-disabilities` in yes|no
- **W:** `drivers-neighborhoods`, `drivers-services`, `drivers-schedule`, تحديث `allow-disabilities` على معلومات السائق

---

## POST /api/driver/transport/index

- **الكود:** `ProfileController@getTransportData`
- **الغرض:** جلب بيانات النقل الحالية + أحياء المدينة للجامعة
- **Response:** جيران، خدمات، جدول، أعلام

---

## POST /api/driver/service/start

- **الكود:** `ServiceController@start`
- **Request:** `service_id` exists:services
- **الغرض:** بدء استقبال خدمة فورية/محددة + تفعيل `is-receiving-rides`
- **شرط:** اكتمال علاقات الملف
- **W:** `drivers-services` firstOrCreate؛ `users.is-receiving-rides=true`

---

## POST /api/driver/service/stop

- **الكود:** `ServiceController@stop`
- **الغرض:** إيقاف استقبال الرحلات الفورية (حذف خدمات immediate من ارتباط السائق) + `is-receiving-rides=false`

---

## POST /api/driver/receiving-rides/toggle

- **الكود:** `ServiceController@toggleReceivingRides`
- **Request:** `enabled` boolean
- **عند التفعيل:** يشترط ملف مكتمل و `approval===1`

---

## GET /api/driver/receiving-rides/status

- **الكود:** `ServiceController@receivingRidesStatus`
- **Response:** `{ is-receiving-rides: bool }`

---

## POST /api/driver/location/update

- **الكود:** `LocationController@update`
- **Request:** `lat` (-90..90), `lng` (-180..180)
- **W:** `driver-info`: `current-lat`, `current-lng`, `current-location-at`, `date-of-edit`
- **من يستدعيه:** تتبع السائق أثناء العمل؛ يغذي مزامنة Wasl للمواقع

# إدارة السائقين

**الملف:** `DriverController` (+ `EditDriverInfoRequestController`)

## Resource `drivers`

| Route | الغرض |
|-------|------|
| GET index | قائمة مع فلاتر ورسوم مستحقات |
| GET show | تفاصيل + Wasl |
| GET edit / PUT update | تعديل بيانات أساسية |

**غير منفّذ:** create, store, destroy.

## مسارات مخصّصة مهمة

| Method Path | الاسم | الوظيفة |
|-------------|-------|---------|
| GET new-drivers | new-drivers.index | طلبات تسجيل `approval=0` |
| GET driver/packages | drivers.packages | نظرة باقات السائقين |
| GET drivers/{id}/packages | packagePlans | خطط السائق |
| POST .../assign | assignPackage | تعيين باقة + إيميل |
| POST .../cancel | cancelPackage | إلغاء → مجانية + إيميل |
| GET driver/rates | drivers.rates | تقييمات مجمعة |
| GET driver/trips | drivers.trips | كل الرحلات مع فلاتر |
| GET drivers/{id}/trips | driverTrips | رحلات سائق؛ الأسبوعي يعرض `group-id` عبر `trip_display_id` |
| GET drivers/{id}/earnings | earnings | أرباح/مستحقات |
| POST .../send-payment-reminder | تذكير بريد إن المستحقات > 50 |
| POST .../update-status | قبول/رفض تسجيل + إيميلات |
| POST .../assign-to-admins | إسناد طلب لمدير + إيميل |
| POST .../ban | حظر (شرط تقييم &lt; 1 في الكود) + إيميل |

## EditDriverInfoRequestController — Resource `edit-info-request`

index/show/update فقط.  
update عبر FormRequest: موافقة أو رفض مع سبب → إيميل قبول/رفض + قرار + Wasl عند الحاجة.

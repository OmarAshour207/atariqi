# الجامعات

**الكلاس:** `UniversityController` · **Service:** `ActionsLogService`  
**مسارات يدوية** — لا show/edit/update

### GET universities — index
قائمة مع المدينة وخدمات الربط

### GET create | POST store
- Validation: أسماء، `city_id`, موقع/إحداثيات، `service_ids`  
- إنشاء جامعة `country=SA` + روابط `UniDrivingService` + سجلات

### GET|POST universities/{university}/services
عرض/مزامنة الخدمات المرتبطة (حذف غير المحدد + إضافة الجديد)

### DELETE universities/{university}
- يتطلب `reason`  
- يُرفض إن وُجد مستخدمون مرتبطون بالجامعة  
- وإلا حذف الروابط ثم الجامعة

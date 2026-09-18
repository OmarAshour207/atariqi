# المدن والأحياء

**الكلاس:** `CityController` · **Service:** `ActionsLogService`

### GET cities | GET create | POST store
إنشاء مدينة مع أحياء اختيارية؛ يمنع تكرار اسم المدينة

### POST cities/{city}/neighborhoods — storeNeighborhood
إضافة حي؛ يمنع التكرار داخل المدينة

### PUT neighborhoods/{neighborhood} — updateNeighborhood
تحديث الاسم AR/EN + سجل

### DELETE neighborhoods/{neighborhood}
- يتطلب `reason`  
- يُمنع إن الحي مستخدم في `DriverNeighborhood` (بحث في حقول to/from)  
- وإلا حذف + سجل

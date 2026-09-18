# الميزات (Features)

**الكلاس:** `FeatureController` · Resource كامل بما فيه `show`

### GET features — index
فلاتر `name`, `service_id` + خدمات للقائمة

### GET create | POST store
- Validation: أسماء AR/EN، أوصاف، `service_id` اختياري  
- سجل `feature_created` في `subscription_employee_log`  
- **لا إيميل عند الإنشاء**

### GET show | edit
عرض/تعديل

### PUT update
- سجل `feature_updated`  
- **Side effects:** `FeatureUpdatedNotificationMail` لكل السائقين ذوي إيميل

### DELETE destroy
حذف فقط — **بدون** سجل موظف و**بدون** إيميل (من الكود الحالي)

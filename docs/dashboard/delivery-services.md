# خدمات التوصيل (Delivery Services)

**الكلاس:** `DeliveryServiceController`  
**Model binding:** `{deliveryService}` → `Service`  
**مسارات:** index / edit / update فقط

### GET delivery-services — index
paginate 20

### GET|PUT delivery-services/{deliveryService}
- Validation: أسماء الخدمة الثلاث، `cost` numeric min:0، `road-way` nullable  
- يحدّث `date-of-edit` + ActionsLog  
- **أثر:** تغيير `cost` يغيّر سعر الكتالوج الحي؛ لا يعدّل `trip_cost` الملتقط على الرحلات القديمة

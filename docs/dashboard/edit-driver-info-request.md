# طلبات تعديل بيانات السائق

**الكلاس:** `EditDriverInfoRequestController`  
**FormRequest:** `UpdateDriverInfoRequest`  
**حقن:** `WaslService`  
**Resource `edit-info-request`:** index/show/update · **ناقص:** create/store/edit/destroy

---

### GET /dashboard/edit-info-request — index

- قائمة `NewUserInfo` حيث `user-type=driver`  
- View: `dashboard.drivers_info_requests.index`

### GET /dashboard/edit-info-request/{driver} — show

- `{driver}` = **user id**  
- مقارنة قديم/جديد (مستخدم، معلومات قيادة، سيارة) + معاينة أهلية Wasl  
- View: `dashboard.drivers_info_requests.show`

### PUT|PATCH /dashboard/edit-info-request/{driver} — update

- **Validation:** `approval` in 1,3؛ `rejection-reason` required_if approval=3 max:255  

**رفض (3):** محاولة إعادة تسجيل Wasl بالبيانات الحالية؛ حذف السجلات المعلّقة؛ `approval=1`؛ قرار مرفوض؛ `DriverEditInfoRejectedMail`

**قبول (1):** دمج الحقول المتغيّرة في الجداول الحية داخل transaction؛ Wasl register إن وُجدت هوية؛ حذف المعلّق؛ قرار موافق؛ `DriverInfoAcceptedMail`

# ملف الأدمن الشخصي

**الكلاس:** `ProfileController`  
**FormRequest:** `UpdateProfileRequest`  
**معفى من ACL الصفحة**

### GET profile/edit
نموذج تغيير كلمة المرور فقط

### POST profile/update
- Validation: كلمة قديمة + جديدة ≥8 + تأكيد  
- يتحقق يدويًا من الـ hash القديم ثم يحدّث  
- **لا** يحدّث الاسم/الإيميل من هذه الشاشة

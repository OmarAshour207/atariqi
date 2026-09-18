# مصادقة الراكب والملف الشخصي

**الملف:** `app/Http/Controllers/Api/UserController.php`

---

## POST /api/user/register

- **الغرض:** إنشاء راكب + إرسال OTP
- **Auth:** ضيف
- **Validation:** أسماء، جوال سعودي فريد للراكب، جامعة، مرحلة، إيميل، `user-type=passenger`, …
- **حظر:** جدول `passenger_banned` على رقم الجوال
- **W:** `users` + `code`
- **Side effects:** OTP
- **Response:** `{ user: ... }`

---

## POST /api/user/send

- **الغرض:** إعادة إرسال OTP
- **Request:** `phone-no`
- **يشترط:** مستخدم راكب موجود وغير محظور
- **W:** تحديث `code`

---

## POST /api/user/verify

- **الغرض:** التحقق وإصدار توكن
- **Request:** `phone-no`, `code`, `fcm_token`
- **ملاحظة من الكود:** لا يوجد early-return واضح عند فشل Validator قبل `validated()` — راقب السلوك الفعلي
- **W:** مسح code، fcm، `user_logins`
- **Response:** `UserResource` + `token`

---

## POST /api/profile/edit

- **Auth:** راكب
- **الغرض:** طلب تعديل بيانات للموافقة الإدارية
- **W:** `new_user_info`؛ `users.approval=2`
- **Response:** رسالة قيد المعالجة

# تسجيل ودخول السائق

## السطح
driver · ملفات: `RegisterController.php`, `LoginController.php`

---

## POST /api/driver/register

- **الاسم في الكود:** `RegisterController@register`
- **الغرض:** إنشاء حساب سائق جديد بانتظار الموافقة
- **Auth:** ضيف (`locale` فقط)
- **Validation (أبرز الحقول):** أسماء، جوال سعودي فريد لنوع السائق، جنس، جامعة، إيميل فريد، `user-type=driver`، نوع سائق، مفتاح اتصال، مرحلة، صور سيارة/رخصة مطلوبة، `identity_number` فريد، `sequence_number`، …
- **الخطوات:**
  1. تحقق  
  2. Transaction: إنشاء `users` بـ `approval=0` + `DriverInfo` + `DriversCar` + رفع صور  
  3. تعيين باقة مجانية (`Package` بسعر 0 و status FREE) كـ `UserPackage` نشط سنوي  
  4. `WaslService::registerDriver`
- **جداول W:** `users`, `driver-info`, `drivers-cars`, `user_packages`, ملفات uploads  
- **Response:** موارد معلومات السائق/السيارة + كائن المستخدم  
- **Side effects:** استدعاء Wasl  
- **من يستدعيه:** تطبيق السائق — شاشة التسجيل

---

## POST /api/driver/login

- **الاسم:** `LoginController@login`
- **الغرض:** إرسال OTP لسائق معتمد
- **Auth:** ضيف
- **Request:** `phone-no` مطلوب (سعودي)
- **شروط approval:** `3` أو `4` → حظر؛ غير `1` → غير معتمد (401) — راجع الملف للدقة الحالية
- **W:** `users.code`
- **Side effects:** OTP + حد معدل عبر `GuardsOtpSending`
- **Response:** رسالة `s_codeSent`
- **من يستدعيه:** شاشة دخول السائق

---

## POST /api/driver/verify

- **الاسم:** `LoginController@verify`
- **الغرض:** التحقق من OTP وإصدار توكن Sanctum
- **Request:** `phone-no`, `code`, `fcm_token`
- **W:** مسح `code`، حفظ `fcm_token`، صف `user_logins` (`login-logout=1`)
- **Response data:** `token`, `driver` (`DriverResource`), `welcome_message`, `requires_abshir_update`, `abshir_message`
- **ملاحظة:** يسمح بمسار أبشر عند `approval===4` مع إعلام التطبيق
- **من يستدعيه:** شاشة إدخال رمز التحقق

# مصادقة الداشبورد

**الملف:** `Dashboard\Auth\LoginController`

## GET /dashboard/login — `dashboard.loginForm`

نموذج الدخول · عام

## POST /dashboard/login — `dashboard.login`

- Middleware: `login.throttle`
- Validation: إيميل موجود في `admins`، كلمة مرور ≥6
- يشترط أدمن نشط
- Response: JSON نجاح جلسة `admin`
- عند تجاوز المحاولات: تعطيل مؤقت + `UnauthorizedLoginAttempt` mail (من middleware)

## POST /dashboard/logout — `dashboard.logout`

إنهاء جلسة الأدمن · ضمن مجموعة الداشبورد ومعفى من فحص الصفحة

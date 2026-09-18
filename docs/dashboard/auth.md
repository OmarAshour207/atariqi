# مصادقة الداشبورد

**الكلاس:** `App\Http\Controllers\Dashboard\Auth\LoginController`  
**السطح:** dashboard  
**الملف:** `app/Http/Controllers/Dashboard/Auth/LoginController.php`

---

### GET /dashboard/login — `dashboard.loginForm`

- **الكود:** `LoginController@showLogin`
- **الغرض:** عرض نموذج دخول الموظفين
- **Auth:** ضيف (خارج مجموعة `is_admin`)
- **View:** `dashboard.auth.login`
- **Side effects:** لا

---

### POST /dashboard/login — `dashboard.login`

- **الكود:** `LoginController@login`
- **Middleware إضافي:** `login.throttle`
- **الغرض:** مصادقة أدمن نشط وإرجاع JSON مع رابط التحويل
- **Validation:** `email` required|email|exists:admins,email؛ `password` required|string|min:6
- **الخطوات:** `Auth::guard('admin')->attempt(..., ['is_active'=>1])`
- **Response نجاح:** JSON يتضمن `redirect_url` نحو `/dashboard/index`
- **فشل:** JSON 401
- **Side effects عند تجاوز المحاولات:** من `LoginThrottle` — تعطيل مؤقت + بريد `UnauthorizedLoginAttempt` (راجع الميدلوير)

---

### POST /dashboard/logout — `dashboard.logout`

- **الكود:** `LoginController@logout`
- **Auth:** داخل مجموعة الداشبورد (معفى من ACL الصفحة)
- **الغرض:** إنهاء الجلسة
- **الخطوات:** `Session::flush()` + `Auth::guard('admin')->logout()`
- **Redirect:** نموذج الدخول

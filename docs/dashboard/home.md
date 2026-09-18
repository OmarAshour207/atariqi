# الصفحة الرئيسية للداشبورد

**الكلاس:** `App\Http\Controllers\Dashboard\HomeController`  
**الملف:** `app/Http/Controllers/Dashboard/HomeController.php`

---

### GET /dashboard/index — `dashboard.index`

- **الكود:** `HomeController@index`
- **Auth:** `is_admin` (معفى من فحص صلاحية الصفحة)
- **الغرض:** شاشة البداية بعد الدخول
- **View:** `dashboard.home`
- **Models:** قراءة أدمن الجلسة فقط
- **Side effects:** لا

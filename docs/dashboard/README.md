# الداشبورد — فهرس

**البادئة:** `/dashboard`  
**Middleware:** `web` + `is_admin` + `admin.page:view` (مع استثناءات)  
**الكود:** `app/Http/Controllers/Dashboard/`

| الملف | يغطي |
|-------|------|
| [auth.md](auth.md) | دخول/خروج الأدمن |
| [home-cms.md](home-cms.md) | أقسام الصفحة، أرقامنا، شهادات، إنجازات |
| [packages-features.md](packages-features.md) | باقات وميزات |
| [drivers.md](drivers.md) | السائقون، الباقات، الرحلات، الأرباح، الحظر |
| [passengers-users.md](passengers-users.md) | الركاب، الشكاوى، الرحلات |
| [support-announcements.md](support-announcements.md) | تذاكر وإعلانات |
| [geo-services-docs.md](geo-services-docs.md) | جامعات، مدن، خدمات، مستندات |
| [employees-roles-logs-settings.md](employees-roles-logs-settings.md) | موظفون، أدوار، سجلات، إعدادات، ملف شخصي |

**ملاحظة Resource ناقصة:** بعض مسارات `create/store/show/destroy` مسجّلة عبر `Route::resource` بدون ميثود في الكنترولر — ستُرجع خطأ إن استُدعيت. موثّقة في كل ملف.

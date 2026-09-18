# إعدادات التطبيق العامة (راكب/مشترك)

**الملف:** `app/Http/Controllers/Api/HomeController.php`  
**Auth:** عامة (`locale`)

---

## GET /api/get/config

- **الكود:** `get`
- **الغرض:** تهيئة التطبيق: خدمات، فتحات، مفاتيح اتصال، جامعات، مراحل، مستندات، اجتماعيات، أنواع سائقين
- **Resources:** مجموعة Resources مخصّصة لكل نوع

---

## GET /api/get/announce

- إعلانات الركاب من جدول `announce`

---

## GET /api/get/contacts

- جهات الاتصال من جدول/إعداد `contact`

---

## POST /api/test/notify

- **الكود:** `test`
- **الغرض:** اختبار FCM بتوكن ثابت
- **تحذير:** مسار تجريبي في الإنتاج إن كان مفعّلاً — وثّق كما هو في `routes/api.php`

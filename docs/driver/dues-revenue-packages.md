# المستحقات والإيرادات والباقات والاشتراك

**الملفات:** `DuesController`, `RevenueController`, `PackageController`, `SubscriptionController`

---

## GET /api/driver/dues

- **الكود:** `DuesController@getData`
- **الغرض:** ملخص مستحقات السائق
- **Response:** من `DriverDuesService::summary()` — يشمل نسبًا، إيراد الفترة، `can_accept_trips`, أعلام أبشر
- **من يستدعيه:** شاشة المستحقات

---

## POST /api/driver/dues/pay

- **الكود:** `DuesController@payDues`
- **الغرض:** بدء دفع مستحقات عبر Telr
- **W:** `orders` نوع `pay_due` status pending + `payment_gateway_id`
- **Response مخصص:** `{ success, payment_url, order_ref }` أو خطأ 400
- **أوتوماتيك لاحقًا:** webhook Telr يكتب `FinancialDue` عند النجاح

---

## POST /api/driver/revenue

- **الكود:** `RevenueController@get`
- **Request:** `start_date`, `end_date` بصيغة Y-m-d
- **الغرض:** إيرادات منتهية في الفترة (فوري action 5؛ يومي/أسبوعي 6)
- **Response:** `immediate`, `daily`, `weekly`, `total`, `total_dues`

---

## POST /api/driver/packages

- **الكود:** `PackageController@index`
- **Auth:** **عام** (خارج sanctum في `routes/api.php`)
- **الغرض:** قائمة الباقات مع الميزات
- **Response:** `PackageResource` collection
- **ملاحظة:** المسار POST رغم أنه قراءة قائمة

---

## POST /api/driver/subscribe

- **الكود:** `SubscriptionController@subscribe`
- **Request:** `package_id`, `type` in monthly|yearly
- **يرفض:** نفس الباقة+الفترة الحالية؛ باقة بحالة `SOON`
- **W:** `Order` (subscription أو upgrade حسب وجود باقة سابقة) + جلسة Telr
- **Response:** رابط الدفع

---

## POST /api/driver/cancel

- **الكود:** `SubscriptionController@cancel`
- **الغرض:** إلغاء الاشتراك النشط والانتقال لباقة مجانية سنوية
- **W:** history ملغى + حذف النشط + إنشاء FREE

---

## مسارات معلّقة / ناقصة

| المسار في التعليق | الحالة |
|-------------------|--------|
| `POST driver/renew` | معلّق — **لا ميثود renew** |
| `POST driver/upgrade` | معلّق — الميثود `upgrade` **موجود** في الكلاس لكن بلا route نشط |
| `POST driver/downgrade` | معلّق — **لا ميثود downgrade** |

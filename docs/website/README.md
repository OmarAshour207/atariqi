# الموقع العام (Website)

**الكود:** `HomeController`, `SupportController`  
**Middleware:** `locale` حيث ذُكر

## GET /

- `HomeController@home` — الصفحة التسويقية مع سلايدر من `HomepageSection`

## GET /homepage-sections

- JSON لكتل CMS: أقسام، أرقام، شهادات، إنجازات شركاء

## GET /locale/{locale}

- تبديل لغة الجلسة `en|ar`

## GET|POST /support

- نموذج تذكرة عامة + إنشاء `Ticket` بأنواع inquiry/complaint/technical + مرفقات حسب القواعد

## GET|POST /support/track

- تتبع تذكرة برقم + إيميل

لا إيميلات من كنترولرات الموقع نفسها عند الإنشاء (الإيميلات لاحقًا من ردود الداشبورد).

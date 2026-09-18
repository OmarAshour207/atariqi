# محتوى الصفحة الرئيسية (CMS)

## HomepageSectionController — Resource `homepage-sections`

| Method | Path | ميثود | ملاحظات |
|--------|------|-------|---------|
| GET | `/dashboard/homepage-sections` | index | قائمة؛ فلتر `?section=` |
| GET | `.../{id}/edit` | edit | `{id}` يُعامل كمفتاح قسم |
| PUT | `.../{id}` | update | عنوان/محتوى/أيقونة |

**ميثودات Resource غير منفّذة:** create, store, show, destroy.

## HomepageStatController — `homepage-stats` (Our Numbers)

CRUD كامل ما عدا `show`.  
عنوان واجهة الإنشاء يجب أن يكون «Our Numbers» (مفتاح ترجمة).

## TestimonialController / PartnerAchievementController

CRUD ناقص `show`. فلتر `?type=` لإنجازات الشركاء.

الجداول: `homepage_sections`, `homepage_stats`, `testimonials`, `partner_achievements`.

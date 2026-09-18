# أقسام الصفحة الرئيسية (Homepage Sections)

**الكلاس:** `HomepageSectionController`  
**Model:** `HomepageSection`  
**Resource:** `homepage-sections.*`  
**ناقص في الكود:** `create`, `store`, `show`, `destroy` (مسجّلة في Resource)

---

### GET /dashboard/homepage-sections — `homepage-sections.index`

- **الغرض:** قائمة الأقسام؛ فلتر اختياري `?section=` على `section_key`
- **View:** `dashboard.homepage_sections.index`

### GET /dashboard/homepage-sections/{id}/edit — `homepage-sections.edit`

- **الغرض:** نموذج تعديل
- **ملاحظة من الكود:** `{id}` يُبحث كـ `section_key` وليس المفتاح الرقمي
- **View:** `dashboard.homepage_sections.edit`

### PUT|PATCH /dashboard/homepage-sections/{id} — `homepage-sections.update`

- **Validation:** `title`, `title_ar` nullable max:255؛ `content`, `content_ar` nullable؛ `icon` nullable image
- **W:** تحديث بالـ id الرقمي؛ رفع أيقونة إلى `homepage-sections/` على disk public
- **Redirect:** `back()` بنجاح

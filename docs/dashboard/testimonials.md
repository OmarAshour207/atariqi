# الشهادات (Testimonials)

**الكلاس:** `TestimonialController` · **Model:** `Testimonial` · **ناقص:** `show`

| Method | Path name | الوظيفة |
|--------|-----------|---------|
| GET index | testimonials.index | قائمة paginate 20 |
| GET create | testimonials.create | نموذج |
| POST store | testimonials.store | إنشاء؛ رفع أيقونة لمجلد `testimonails` (إملاء كما في الكود) |
| GET edit | testimonials.edit | تعديل |
| PUT update | testimonials.update | تحديث؛ رفع أيقونة قد يذهب لمجلد `homepage-sections` (اختلاف عن store — موثّق من الكود) |
| DELETE destroy | testimonials.destroy | حذف |

**Validation الشائعة:** `name`, `title`, `title_ar` nullable؛ `description`, `description_ar` nullable؛ `icon` nullable image.

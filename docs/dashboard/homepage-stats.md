# أرقامنا (Homepage Stats)

**الكلاس:** `HomepageStatController`  
**Model:** `HomepageStat`  
**Resource:** `homepage-stats.*`  
**ناقص:** `show`

واجهة القائمة/التعديل تستخدم ترجمة **Our Numbers**؛ صفحة الإنشاء يجب أن تطابق ذلك.

---

### GET /dashboard/homepage-stats — index
قائمة paginate 20 → `dashboard.homepage_stats.index` + زر إنشاء

### GET .../create — create
نموذج → `dashboard.homepage_stats.create`

### POST /dashboard/homepage-stats — store
- **Validation:** `number`, `label`, `label_ar` nullable؛ `icon` nullable image  
- **W:** إنشاء + رفع أيقونة إلى `homepage-stats/`  
- **Redirect:** `homepage-stats.index`

### GET .../{id}/edit | PUT .../{id} — edit / update
نفس قواعد التحقق؛ تحديث أيقونة اختياري

### DELETE .../{id} — destroy
حذف الصف (ملف الأيقونة قد يبقى على القرص — من الكود الحالي)

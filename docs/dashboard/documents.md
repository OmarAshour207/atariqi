# مستندات التطبيق

**الكلاس:** `DocumentController`

### GET documents — index
قائمة مرتبة بـ `date-of-edit`

### GET documents/{document}/download
تنزيل من المسار المخزّن؛ 404 إن الملف مفقود

### POST documents/{document}/replace
- Validation: PDF ≤ 10MB  
- استبدال الملف + `DocumentUpdateLog` + ActionsLog  
- **Side effects ثقيلة:** `DocumentUpdatedMail` لكل مستخدمي `users` ذوي إيميل + `PlatformEmailLog` لكل إرسال

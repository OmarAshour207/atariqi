# إعدادات التواصل ولغة الداشبورد

**الكلاس:** `SettingController`  
**Service:** `ContactSettingsService` + helper `setting()`

### GET /dashboard/settings — index
عرض الإعدادات المدمجة → View `dashboard.settings.edit`

### POST /dashboard/settings/store
- Validation: عنوان، هاتف، إيميل، وصف، روابط متاجر/اجتماعية  
- حفظ عبر `setting()->save()` + `ContactSettingsService::sync`

### GET /dashboard/language/{locale} — `language`
تبديل جلسة اللغة `en|ar` (معفى من ACL الصفحة)

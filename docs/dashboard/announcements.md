# الإعلانات

**الكلاس:** `AnnouncementController` · **Service:** `ActionsLogService`

### GET announcements — index
قائمة موحّدة من `Announce` (ركاب) + `DriverAnnounce` (سائقين) مع حقل `source`

### GET create | POST store
- Validation: عناوين ومحتوى AR/EN؛ `target_app` in passengers|drivers|both  
- `both` يكتب في الجدولين  
- سجل إضافة عبر ActionsLog

### DELETE announcements/{source}/{id}
- `source` passengers|drivers  
- يتطلب `reason`  
- سجل حذف مع لقطة قديمة

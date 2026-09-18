# إنجازات الشركاء

**الكلاس:** `PartnerAchievementController` · **Model:** `PartnerAchievement` · **ناقص:** `show`

### GET partner-achievements — index
فلتر `?type=` + paginate 20

### create / store / edit / update / destroy
- Validation: عناوين ووصف AR/EN، `type`، `icon`  
- رفع إلى `partners/`  
- بعد الحفظ/الحذف يعيد التوجيه مع الحفاظ على `type` في الـ query

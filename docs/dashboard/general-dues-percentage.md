# نسبة المستحقات العامة

**الكلاس:** `GeneralDuesPercentageController`  
**Model:** `Subscription` عبر `generalDuesPercentage()` (`type = TYPE_GENERAL_DUES_PERCENTAGE` = 2)

---

### GET /dashboard/general-dues-percentage — `general-dues-percentage.show`

- عرض القيمة الحالية  
- View: `dashboard.general-dues-percentage.show`

### PATCH /dashboard/general-dues-percentage — `general-dues-percentage.update`

- **Validation:** `cost` required|numeric|min:0|max:100  
- إن القيمة بعد التقريب مساوية للقديمة → نجاح بدون كتابة  
- وإلا تحديث `cost` + سجل `subscription_employee_log` (`updated`)  
- **أثر لاحق:** اللقطات الجديدة على sug تستخدم القيمة عبر `SnapshotsAtariqiPercentage` — الرحلات القديمة تحتفظ بلقطتها

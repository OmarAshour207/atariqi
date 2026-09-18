# الباقات والميزات (داشبورد)

## PackageController — Resource `packages`

| الفعل | أثر جانبي |
|-------|-----------|
| store | بريد `NewPackageNotificationMail` لكل السائقين ذوي إيميل + `PlatformEmailLog` + `subscription_employee_log` |
| update | `PackageUpdateNotificationMail` للسائقين |
| destroy | `PackageDeletedNotificationMail` للسائقين + سجل حذف |

Validation الأسعار والحالة `0..3` والميزات اختيارية.

**ناقص:** `show`.

## FeatureController — Resource `features` كامل

- update يرسل `FeatureUpdatedNotificationMail` للسائقين  
- store/update يكتبان `subscription_employee_log`

## GeneralDuesPercentageController

- GET/PATCH `general-dues-percentage` — قراءة/تحديث نسبة المستحقات العامة (`Subscription` type مستحقات) مع سجل موظف

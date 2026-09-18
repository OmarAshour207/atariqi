# API الراكب — فهرس القسم

**Auth للرحلات:** `auth:sanctum` + `is_passenger`  
**تسجيل/OTP:** عام تحت `locale`  
**الكود:** `app/Http/Controllers/Api/{User,Immediate,Daily,Weekly,Trip,Home}Controller.php`

| الملف | المحتوى |
|-------|---------|
| [auth-profile.md](auth-profile.md) | تسجيل، OTP، تعديل ملف |
| [home-config.md](home-config.md) | إعدادات عامة وإعلانات |
| [immediate.md](immediate.md) | رحلات فورية |
| [daily.md](daily.md) | رحلات يومية |
| [weekly.md](weekly.md) | رحلات أسبوعية + group-id |
| [trip-current-location.md](trip-current-location.md) | الرحلة الحالية وتحديث الموقع |

Concerns: `ChecksPassengerRideConflicts`, `GuardsOtpSending`.

# التكاملات الخارجية

## 1. Firebase Cloud Messaging

- عبر HTTP من `sendNotification` في `helpers.php`  
- إعدادات: `config/services.php` / مفاتيح السيرفر (راجع الملفات البيئية — لا تُدرج أسرار في التوثيق)

## 2. OTP / SMS

- Twilio SDK و/أو Vonage حسب مسار `deliver_otp_code`  
- حماية سعودية عبر `SaudiPhone` و `GuardsOtpSending`

## 3. Telr (مدفوعات)

- `TelrService` + إعداد `config/telr.php` (أو مكافئه)  
- Webhook + صفحات رجوع — انظر `payments-dues-packages.md`

## 4. Wasl / رفقني (وزارة / ELM)

| المكوّن | الدور |
|---------|------|
| `app/Services/WaslService.php` | تسجيل سائق، أهلية، مواقع، رحلات، مقاطعات |
| `config/wasl.php` | `WASL_ENABLED`, مفاتيح العميل، رابط API |
| أوامر Artisan | أهلية يومية، مزامنة مواقع كل دقيقة، مقاطعات يدويًا |
| Trait `ChecksDriverWaslStatus` | يمنع تشغيل الرحلات إن `approval` غير صالح |
| Resources تحت `Http/Resources/Driver/Wasl/` | أجسام طلبات Wasl |
| سجل | قناة Log `wasl` → `storage/logs/wasl/` |

استدعاء `storeTrip` يظهر بعد تقييم الراكب للسائق في مسار التقييم الفوري (وغيره حسب الربط في `ImmediateDriverController@rate`).

## 5. إعدادات الموقع

- `anlutro/l4-settings` + `ContactSettingsService` لمزامنة بيانات التواصل الظاهرة في الموقع/التطبيق.

# الركاب والمستخدمون والرحلات والشكاوى

## PassengerController

| Path | الوظيفة |
|------|---------|
| GET passengers | قائمة |
| GET passengers/{id} | تفاصيل |
| GET passengers/{id}/trips | رحلات راكب (أسبوعي بـ group-id) |
| GET passengers-trips | كل الرحلات مع فلاتر |
| POST ban / update-approval | حظر أو حالة موافقة + إيميل |
| profile-update-requests + approve/reject/assign | دورة طلب تعديل الملف + إيميلات |

## UserController (داشبورد)

| Path | الوظيفة |
|------|---------|
| GET users / user/trips | فهرسة ركاب/رحلات (نفس index) |
| GET users/{id} | عرض |
| GET user/rates | ⚠️ المسار بلا `{user}` بينما الميثود تتوقع User — **تعارض محتمل في التوجيه** |
| GET user/unride-rates | تقييمات عدم الركوب |
| GET passengers/{id}/complaints | شكاوى راكب؛ رقم الرحلة الأسبوعية من `group-id` عبر الحجز |

**Resource users:** create/store/edit/update/destroy غير منفّذة.

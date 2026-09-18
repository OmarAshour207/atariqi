# Queues / Jobs / Events

## النتيجة من جرد المشروع

| المكوّن | الحالة |
|---------|--------|
| `app/Jobs` | **المجلد غير موجود** — لا Jobs مخصّصة |
| `app/Events` | **غير موجود** |
| `app/Listeners` | **غير موجود** |
| `EventServiceProvider` | ربط افتراضي فقط لتفعيل بريد التحقق من Laravel عند `Registered` (غير مستخدم كمسار أعمال رحلات) |

## ماذا يعني تشغيليًا؟

- الآثار الجانبية (إشعار، بريد، تحديث حالات، دفع) تتم **متزامنة داخل الـ Controllers / Commands / Services**.
- بعض أصناف `app/Mail\*` قد تنفّذ واجهة الطابور إن وُجدت `ShouldQueue` في الملف — راجع كل Mailable على حدة إن لزم تتبع async البريد.
- لا يوجد `dispatch()` ظاهر لنمط Job رحلات في الجرد الحالي.

ملف المراقبين وhooks النماذج: `observers-and-model-hooks.md`.

# إدارة الركاب — PassengerController

**الكلاس:** `PassengerController`  
**الملف:** `app/Http/Controllers/Dashboard/PassengerController.php`

---

### GET /dashboard/passengers — `passengers.index`

- فلاتر: اسم، جوال، جامعة، مرحلة، تقييم تحذيري (`rate < 2`)، ترتيب  
- يحسب أعداد الرحلات  
- View: `dashboard.passengers.index`

### GET /dashboard/passengers/{passenger} — show

- تفاصيل + أدمن قابلون للإسناد + حالة حظر  
- محظور إن `approval===3`  
- View: `dashboard.passengers.show`

### GET .../trips — `passengers.trips`

- فوري/يومي/أسبوعي للراكب  
- رقم الأسبوعي عبر `trip_display_id` → group-id  
- View: `dashboard.passengers.trips`

### GET /dashboard/passengers-trips — `passengers.all-trips`

- متصفح عام بثلاث paginations مستقلة + فلاتر راكب/سائق/تاريخ  
- View: `dashboard.passengers.all-trips`

### POST .../ban — `passengers.ban`

- `ban_reason` required  
- يمنع إن محظور مسبقًا؛ `approval=3`؛ `PassengerBanned`؛ قرار؛ `PassengerBannedMail`  
- ملاحظة: أعمدة `PlatformEmailLog.driver_id/email` تُستخدم لتخزين بيانات الراكب

### POST .../update-approval — `passengers.updateApproval`

- `approval` in 1,2,3 + `PassengerStatusMail`

### GET profile-update-requests

- طابور `NewUserInfo` لركاب `approval=2`

### POST profile-updates/{newUserInfo}/approve | reject

- موافقة: نسخ الحقول إلى User، حذف الطلب، إيميل قبول، قرار  
- رفض: يتطلب `rejection_reason`، حذف الطلب، إيميل رفض  
- `approval` يبقى 2 إن بقيت طلبات معلّقة وإلا 1

### POST .../assign-to-admin

- يتطلب طلب معلّق؛ لا إسناد للذات؛ `PassengerRequestAssignment` + إيميل للمدير

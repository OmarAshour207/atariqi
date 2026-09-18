# المستخدمون / التقييمات / الشكاوى — UserController

**الكلاس:** `UserController` (Dashboard)  
**Resource `users`:** index/show فقط · **ناقص:** create/store/edit/update/destroy

---

### GET /dashboard/users و GET /dashboard/user/trips

- كلاهما → `index`  
- قائمة ركاب مع فلاتر تسجيل وعدادات رحلات  
- View: `dashboard.users.index`

### GET /dashboard/users/{user} — show

- تفاصيل + عدادات  
- View: `dashboard.users.show`

### GET /dashboard/user/rates — `users.rates`

- **تعارض محتمل:** المسار بلا `{user}` بينما الميثود تتوقع `User $user`  
- الغرض المقصود: تقييمات قدّمها الراكب عبر جداول Del*Info  
- View: `dashboard.users.rates`

### GET /dashboard/user/unride-rates — `users.unride-rates`

- شكاوى/تقييمات عدم ركوب لكل الأنواع على مستوى المنصة  
- View: `dashboard.users.unride-rates`

### GET /dashboard/passengers/{passenger}/complaints — `passengers.complaints`

- شكاوى راكب محدد + إمكانية الحظر إن العدد ≥ 5 في الواجهة  
- رقم الرحلة المعروض: `booking-id` للفوري/اليومي و **`group-id`** للأسبوعي  
- View: `dashboard.users.complaints`

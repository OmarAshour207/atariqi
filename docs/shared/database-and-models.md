# قاعدة البيانات والنماذج الأساسية

> الجداول بأسمائها كما في `$table` أو migrations. العلاقات من ملفات Models.

## 1. المستخدمون والحسابات

| نموذج | جدول | ملاحظات |
|-------|------|---------|
| `User` | `users` | سائق/راكب؛ Sanctum |
| `Admin` | `admins` | داشبورد |
| `DriverInfo` | `driver-info` | هوية، موقع حالي، تقييم |
| `DriversCar` | `drivers-cars` | بيانات السيارة |
| `DriverNeighborhood` | `drivers-neighborhoods` | أحياء to/from مفصولة بـ `\|` |
| `DriverSchedule` | `drivers-schedule` | أوقات الأسبوع |
| `DriversServices` | `drivers-services` | خدمات السائق |
| `NewUserInfo` / `NewDriverInfo` / `NewDriverCar` | جداول طلبات التعديل | موافقة الأدمن |
| `PassengerBanned` / `DriverBanned` | حظر | |
| `UserLogin` | سجل دخول | |

---

## 2. الرحلات

انظر أيضًا `trip-actions-and-statuses.md`.

| نوع | حجز | اقتراح | توصيل |
|-----|-----|--------|-------|
| فوري | `RideBooking` → `ride-booking` | `SuggestionDriver` → `suggestions-drivers` | `DelImmediateInfo` |
| يومي | `DayRideBooking` → `day-ride-booking` | `SugDayDriver` → `sug-day-drivers` | `DelDailyInfo` |
| أسبوعي | `WeekRideBooking` → `week-ride-booking` | `SugWeekDriver` → `sug-week-drivers` | `DelWeekInfo` |

علاقة نمطية: `booking` BelongsTo ← `sugDriver` HasOne.

---

## 3. لقطة السعر والعمولة

Trait: `app/Models/Concerns/SnapshotsAtariqiPercentage.php`  
على: `SuggestionDriver`, `SugDayDriver`, `SugWeekDriver`.

عند **`creating`**:

1. `atariqi_percentage` ← نسبة المستحقات العامة (`Subscription::generalDuesPercentageValue()`) إن كانت null  
2. `trip_cost` ← `booking.service.cost` إن كانت null  

الدوال: `snapshottedTripCost()`, `companyAmount()`, `driverAmount()`.

في الـ API Resources يظهر الحقل `trip_cost` على كائن الرحلة؛ `service_id.cost` يبقى سعر الكتالوج الحالي (`ResolvesTripCost`).

---

## 4. الخدمات والموقع

| نموذج | دور |
|-------|-----|
| `Service` | أنواع التوصيل + `cost` + `road-way` |
| `University` / `City` / `Neighbour` | جغرافيا |
| `UniDrivingService` | ربط جامعة↔خدمة |
| `CallingKey` / `Stage` / `DriverType` / `Opening` / `Document` / `Social` | إعدادات التطبيق |

---

## 5. الاشتراكات والدفع

| نموذج | دور |
|-------|-----|
| `Package` / `Feature` | باقات وميزات |
| `UserPackage` / `UserPackageHistory` | اشتراك السائق |
| `Order` | طلبات Telr (`subscription` / `upgrade` / `pay_due`) |
| `FinancialDue` | دفعات المستحقات المسجّلة |
| `Subscription` | إعدادات نسب (منها نسبة المستحقات العامة `type=2`) |
| `PaymentReminder` | تذكيرات المستحقات |

ثوابت Order (من الكود): `STATUS_PENDING=1`, `COMPLETED=2`, `FAILED=3`.

---

## 6. التقييمات والشكاوى

| نموذج | دور |
|-------|-----|
| `PassengerRate` | متوسط تقييم الراكب |
| `DayUnrideRate` / `WeekUnrideRate` / `ImmediateUnrideRate` | شكاوى/تقييم عدم ركوب مرتبطة بـ `sug-id` |
| جداول `Del*Info` | `passenger-rate`, ETA، وصول |

---

## 7. المحتوى والدعم

`HomepageSection`, `HomepageStat`, `Testimonial`, `PartnerAchievement`, `Announce`, `DriverAnnounce`, `Ticket` (+ replies/attachments/logs), `Document`, `WaslProvince`, `PlatformEmailLog`, `ActionsLog`, جداول إسناد الطلبات…

---

## 8. فلاتر الاستعلام

تحت `app/Models/Support/QueryFilters/`:

- `SortByDate` — ترتيب ملخص السائق حسب نوع الرحلة  
- `SortByRate` — ترتيب حسب التقييم  

تُستخدم مع Spatie QueryBuilder في `SummaryController@summary`.

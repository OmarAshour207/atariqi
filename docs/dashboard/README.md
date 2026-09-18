# الداشبورد — فهرس مفصّل

**البادئة:** `/dashboard`  
**Middleware الافتراضي:** `web` + `is_admin` + `admin.page:view`  
**Guard:** `admin`  
**الكود:** `app/Http/Controllers/Dashboard/`

## الملفات (ملف لكل مجال / كنترولر رئيسي)

| الملف | الكنترولر(ات) |
|-------|----------------|
| [auth.md](auth.md) | `Auth\LoginController` |
| [home.md](home.md) | `HomeController` |
| [homepage-sections.md](homepage-sections.md) | `HomepageSectionController` |
| [homepage-stats.md](homepage-stats.md) | `HomepageStatController` |
| [testimonials.md](testimonials.md) | `TestimonialController` |
| [partner-achievements.md](partner-achievements.md) | `PartnerAchievementController` |
| [packages.md](packages.md) | `PackageController` |
| [features.md](features.md) | `FeatureController` |
| [general-dues-percentage.md](general-dues-percentage.md) | `GeneralDuesPercentageController` |
| [drivers.md](drivers.md) | `DriverController` (كامل) |
| [edit-driver-info-request.md](edit-driver-info-request.md) | `EditDriverInfoRequestController` |
| [passengers.md](passengers.md) | `PassengerController` |
| [users.md](users.md) | `UserController` |
| [support-tickets.md](support-tickets.md) | `SupportTicketController` |
| [announcements.md](announcements.md) | `AnnouncementController` |
| [universities.md](universities.md) | `UniversityController` |
| [cities.md](cities.md) | `CityController` |
| [delivery-services.md](delivery-services.md) | `DeliveryServiceController` |
| [documents.md](documents.md) | `DocumentController` |
| [employees.md](employees.md) | `EmployeeController` |
| [roles.md](roles.md) | `RoleController` |
| [logs.md](logs.md) | `LogsManagementController` |
| [profile.md](profile.md) | `ProfileController` |
| [settings.md](settings.md) | `SettingController` |

## ملاحظات عامة

- صلاحيات الصفحة تُحسم عبر `EnsureAdminPageAccess` + `AdminAuthorizationService`.
- مسارات معفاة من فحص الصفحة: `dashboard.index`, `dashboard.logout`, `profile.edit`, `profile.update`, `language`.
- بعض `Route::resource` تسجّل أفعالاً بلا ميثود في الكنترولر → استدعاؤها يفشل؛ موثّق في كل ملف.
- الملفات القديمة المختصرة (`home-cms.md`, `packages-features.md`, …) أُلغيت دلاليًا لصالح الملفات أعلاه — راجع هذا الفهرس فقط.

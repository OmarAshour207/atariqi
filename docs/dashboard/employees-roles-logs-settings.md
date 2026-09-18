# الموظفون والأدوار والسجلات والإعدادات

## EmployeeController

CRUD موظفي `Admin` مع دور Spatie وصلاحيات صفحات و`is_active`.

## RoleController

إدارة الأدوار والصلاحيات؛ قيود على دور admin الأساسي.

## LogsManagementController

عرض سجلات متعددة الجداول (إيميل، قرارات، حظر، باقات، تذاكر، …) وتفاصيل صف.

## ProfileController (داشبورد)

تغيير كلمة مرور الأدمن الحالي (معفى من ACL الصفحة).

## SettingController

إعدادات التواصل عبر `ContactSettingsService` + تبديل لغة الداشبورد `/dashboard/language/{locale}`.

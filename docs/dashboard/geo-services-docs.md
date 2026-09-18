# الجغرافيا والخدمات والمستندات

## UniversityController

قائمة/إنشاء جامعة، ربط خدمات، حذف مشروط بعدم وجود مستخدمين مرتبطين (+ سبب).

## CityController

مدن وأحياء: إنشاء مدينة مع أحياء، إضافة/تحديث/حذف حي (الحذف يُمنع إن استُخدم في `DriverNeighborhood`).

## DeliveryServiceController

تعديل خدمات التوصيل (`Service`) بما فيها `cost` و`road-way` — يؤثر على السعر الحيّ للكتالوج وليس على `trip_cost` الملتقط سابقًا.

## DocumentController

عرض مستندات التطبيق، تنزيل، استبدال PDF مع إيميل `DocumentUpdatedMail` لجميع المستخدمين ذوي إيميل + سجل تحديث.

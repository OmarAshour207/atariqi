# الموظفون

**الكلاس:** `EmployeeController` · Spatie roles على guard `admin`  
**ناقص:** show, destroy

### GET employees | create | POST store | edit | PUT update
- Validation: اسم، إيميل فريد في admins، كلمة مرور (مطلوبة عند الإنشاء)، دور موجود، `is_active`  
- تعيين دور + مزامنة صفحات الصلاحيات + ActionsLog  
- View تحت `dashboard.employees.*`

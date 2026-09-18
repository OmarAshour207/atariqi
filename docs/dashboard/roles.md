# الأدوار والصلاحيات

**الكلاس:** `RoleController` · Spatie `Role` guard `admin`

### index / create / store / edit / update / destroy
- صلاحيات من `Admin::permissionsMatrix()` / `allPermissionNames()`  
- دور باسم Admin محمي (لا حذف، قيود على إعادة التسمية)  
- لا يُحذف دور مرتبط بموظفين  
- بعد التغيير: مزامنة صفحات الموظفين + نسيان cache الصلاحيات  
- **ناقص:** show

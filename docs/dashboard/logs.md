# إدارة السجلات (Logs)

**الكلاس:** `LogsManagementController`  
**قراءة فقط**

### GET /dashboard/logs — index
عشرة أقسام تقريبًا (ActionsLog، تذاكر، إيميل منصة، قرارات ركاب/سائقين، حظر، باقات موظفين، subscription_employee_log، …)  
كل قسم paginate مستقل + فلاتر query حسب المفتاح

### GET /dashboard/logs/{table}/{id} — show
تفاصيل صف واحد؛ 404 إن الجدول غير معروف

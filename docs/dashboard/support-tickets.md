# تذاكر الدعم

**الكلاس:** `SupportTicketController`  
**قيد الصفحة:** `{page}` ∈ `complaints` | `inquiries` | `technical`

---

### GET /dashboard/support-tickets/{page} — index

- فلاتر: status, email, ticket_number, تواريخ, sort  
- View: `dashboard.support_tickets.index`

### GET .../{page}/{ticket} — show

- خيط الردود والمرفقات والسجل + موظفون قابلون للإسناد  
- نوع التذكرة يجب أن يطابق الصفحة وإلا 404  
- View: `dashboard.support_tickets.show`

### POST .../reply

- Validation: `message` max:5000؛ مرفقات حتى 5 (jpeg/jpg/png/pdf ≤5MB)  
- NEW → IN_PROGRESS  
- إيميل `TicketReplyMail` للعميل

### POST .../assign

- موظف نشط بدور admin أو support  
- إيميل `TicketAssignedMail` للمسند إليه

### POST .../close

- يتطلب وجود رد موظف سابق  
- CLOSED + `TicketClosedMail`  
- لا يُغلق مرتين

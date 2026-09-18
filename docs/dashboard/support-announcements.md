# الدعم والإعلانات

## SupportTicketController

`{page}` ∈ complaints|inquiries|technical

| Path | الوظيفة | إيميل |
|------|---------|-------|
| GET index/show | قائمة/تفاصيل | — |
| POST reply | رد + مرفقات | TicketReplyMail للعميل |
| POST assign | إسناد لموظف | TicketAssignedMail |
| POST close | إغلاق (يتطلب ردًا سابقًا) | TicketClosedMail |

## AnnouncementController

- إنشاء لإعلانات ركاب و/أو سائقين حسب `target_app`  
- حذف يتطلب `reason` ويسجّل ActionsLog

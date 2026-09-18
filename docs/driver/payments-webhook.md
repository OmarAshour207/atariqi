# Webhook Telr وصفحات الرجوع

## POST /api/webhook/telr

- **الكود:** `WebhookController@handleWebhook`
- **Auth:** لا يوجد (عام تحت `locale`)
- **الغرض:** تأكيد دفع Telr
- **الخطوات:**
  1. إيجاد `Order` معلّق بـ `tran_cartid`
  2. إن `tran_status === 'A'`: تفريع حسب نوع الطلب (اشتراك / ترقية / دفع مستحقات)
  3. وإلا: وضع الطلب `STATUS_FAILED`
- **Side effects:** تفعيل باقات، `FinancialDue`، سجلات قناة `payment`
- **من يستدعيه:** خوادم Telr

تفاصيل الأنواع: `docs/shared/payments-dues-packages.md`

---

## صفحات الويب (ليست JSON API)

| المسار | الكود | الوظيفة |
|--------|-------|---------|
| `GET /payment/telr/success` | `PaymentController@success` | عرض Blade نجاح |
| `GET /payment/telr/failed` | `PaymentController@failed` | Blade فشل |
| `GET /payment/telr/declined` | `PaymentController@declined` | Blade رفض |

لا منطق أعمال داخل هذه الصفحات — التفعيل الحقيقي من الـ webhook.

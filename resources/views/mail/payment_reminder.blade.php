<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Payment Reminder') }}</title>
</head>

<body style="margin:0;padding:0;background-color:#f4f6f9;font-family:Arial,Helvetica,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f9;padding:40px 0;">
    <tr>
        <td align="center">

            <table width="620" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #e5e7eb;">

                <!-- Header -->
                <tr>
                    <td align="center" style="background:#0F5CC0;padding:35px 30px;">
                        <h1 style="margin:0;color:#ffffff;font-size:28px;font-weight:bold;">
                            {{ __('Payment Reminder') }}
                        </h1>
                        <p style="margin:10px 0 0;color:#DBEAFE;font-size:15px;">
                            Atariqi | عطريقي
                        </p>
                    </td>
                </tr>

                <!-- English Section -->
                <tr>
                    <td style="padding:35px;color:#333333;line-height:1.8;">

                        <p style="margin-top:0;">
                            <strong>Greetings from the Atariqi Team!</strong>
                        </p>

                        <h2 style="margin:15px 0 20px;font-size:22px;">
                            Hello, {{ $details['name'] }}
                        </h2>

                        <p>
                            This is a friendly reminder that your outstanding payment is due. To ensure uninterrupted access to our services, please complete your payment at your earliest convenience.
                        </p>

                        <table width="100%" cellpadding="10" cellspacing="0" style="margin-top:20px;border:1px solid #e5e7eb;border-collapse:collapse;">

                            <tr style="background:#f8fafc;">
                                <td width="35%"><strong>Outstanding Amount</strong></td>
                                <td>{{ $details['amount'] }} SAR</td>
                            </tr>

                        </table>

                        <p style="margin-top:25px;">
                            Completing your payment on time will help avoid any interruption to your service.
                        </p>

                    </td>
                </tr>

                <!-- Divider -->
                <tr>
                    <td style="padding:0 35px;">
                        <hr style="border:none;border-top:1px solid #e5e7eb;">
                    </td>
                </tr>

                <!-- Arabic Section -->
                <tr>
                    <td dir="rtl" style="padding:35px;text-align:right;color:#333333;line-height:2;font-family:Tahoma,Arial,sans-serif;">

                        <p style="margin-top:0;">
                            <strong>تحية طيبة من فريق عطريقي (Atariqi)</strong>
                        </p>

                        <h2 style="margin:15px 0 20px;font-size:22px;">
                            مرحباً، {{ $details['name'] }}
                        </h2>

                        <p>
                            نود تذكيركم بوجود مستحقات مالية مستحقة على حسابكم. ولضمان استمرار الخدمة دون انقطاع، نأمل سداد المبلغ المستحق في أقرب وقت ممكن.
                        </p>

                        <table width="100%" cellpadding="10" cellspacing="0" style="margin-top:20px;border:1px solid #e5e7eb;border-collapse:collapse;text-align:right;">

                            <tr style="background:#f8fafc;">
                                <td width="35%"><strong>المبلغ المستحق</strong></td>
                                <td>{{ $details['amount'] }} ريال سعودي</td>
                            </tr>

                        </table>

                        <p style="margin-top:25px;">
                            سداد المستحقات في الوقت المحدد يضمن استمرار تقديم خدماتنا لك دون أي انقطاع.
                        </p>

                    </td>
                </tr>
<!-- Footer -->
                <tr>
                    <td align="center" style="background:#f8fafc;padding:30px;border-top:1px solid #e5e7eb;">

                        <p style="margin:0;color:#555555;font-size:15px;">
                            Thank you for choosing Atariqi.
                        </p>

                        <p style="margin:10px 0 0;color:#555555;font-size:15px;">
                            شكراً لاختياركم عطريقي.
                        </p>

                        <p style="margin:20px 0 5px;color:#555555;font-size:15px;">
                            For any inquiries, please contact our technical support team.<br>
                            لأي استفسارات، يرجى التواصل مع فريق الدعم الفني.
                        </p>

                        <p style="margin:10px 0 5px;font-size:16px;font-weight:bold;color:#0F5CC0;">
                            info@atariqi.com
                        </p>

                        <p style="margin:15px 0 0;color:#888888;font-size:13px;">
                            © {{ date('Y') }} Atariqi. All Rights Reserved.
                        </p>

                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>
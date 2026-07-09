<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Driver Info Update Rejected') }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f6f9;font-family:Arial,Helvetica,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f9;padding:40px 0;">
    <tr>
        <td align="center">

            <table width="620" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #e5e7eb;">

                <tr>
                    <td align="center" style="background:#D32F2F;padding:35px 30px;">
                        <h1 style="margin:0;color:#ffffff;font-size:28px;font-weight:bold;">
                            {{ __('Driver Info Update Rejected') }}
                        </h1>
                        <p style="margin:10px 0 0;color:#FEE2E2;font-size:15px;">
                            Rafiqni | رافقني
                        </p>
                    </td>
                </tr>

                <tr>
                    <td style="padding:35px;color:#333333;line-height:1.8;">
                        <p style="margin-top:0;">
                            <strong>Greetings from the Rafiqni Team!</strong>
                        </p>

                        <h2 style="margin:15px 0 20px;font-size:22px;">
                            {{ __('Hello') }}, {{ $driver->fullName ?? $driver->email }}
                        </h2>

                        <p style="font-size:18px;font-weight:bold;color:#D32F2F;">
                            Your driver information update could not be approved.
                        </p>

                        <p>
                            After reviewing the changes you submitted, we were unable to approve your driver profile update at this time. Your current approved information remains unchanged.
                        </p>

                        <table width="100%" cellpadding="10" cellspacing="0" style="margin-top:20px;border:1px solid #e5e7eb;border-collapse:collapse;">
                            <tr style="background:#f8fafc;">
                                <td width="30%"><strong>Reason</strong></td>
                                <td>{{ $reason }}</td>
                            </tr>
                        </table>

                        <p style="margin-top:25px;">
                            Please review the reason above, make the necessary corrections, and submit your update request again from the app. If you believe this decision was made in error, please contact our support team.
                        </p>
                    </td>
                </tr>

                <tr>
                    <td style="padding:0 35px;">
                        <hr style="border:none;border-top:1px solid #e5e7eb;">
                    </td>
                </tr>

                <tr>
                    <td dir="rtl" style="padding:35px;text-align:right;color:#333333;line-height:2;font-family:Tahoma,Arial,sans-serif;">
                        <p style="margin-top:0;">
                            <strong>تحية طيبة من فريق تطبيق رافقني (Rafiqni)</strong>
                        </p>

                        <h2 style="margin:15px 0 20px;font-size:22px;">
                            مرحباً، {{ $driver->fullName ?? $driver->email }}
                        </h2>

                        <p style="font-size:18px;font-weight:bold;color:#D32F2F;">
                            تعذر الموافقة على طلب تحديث بياناتك كسائق.
                        </p>

                        <p>
                            بعد مراجعة التعديلات التي أرسلتها، تعذر اعتماد طلب تحديث بياناتك في الوقت الحالي. بياناتك المعتمدة حالياً لم تتغير.
                        </p>

                        <table width="100%" cellpadding="10" cellspacing="0" style="margin-top:20px;border:1px solid #e5e7eb;border-collapse:collapse;text-align:right;">
                            <tr style="background:#f8fafc;">
                                <td width="30%"><strong>سبب الرفض</strong></td>
                                <td>{{ $reason }}</td>
                            </tr>
                        </table>

                        <p style="margin-top:25px;">
                            يرجى مراجعة سبب الرفض الموضح أعلاه، وإجراء التعديلات المطلوبة، ثم إعادة إرسال طلب التحديث من التطبيق. وإذا كنت تعتقد أن هذا القرار قد صدر عن طريق الخطأ، فيسعد فريق الدعم الفني بمساعدتك.
                        </p>
                    </td>
                </tr>

                <tr>
                    <td align="center" style="background:#f8fafc;padding:30px;border-top:1px solid #e5e7eb;">
                        <p style="margin:0;color:#555555;font-size:15px;">
                            If you have any questions, our support team will be happy to assist you.
                        </p>
                        <p style="margin:10px 0 0;color:#555555;font-size:15px;">
                            إذا كانت لديك أي استفسارات، يسعد فريق الدعم الفني بخدمتك.
                        </p>
                        <p style="margin:20px 0 5px;font-size:16px;font-weight:bold;color:#0F5CC0;">
                            info@atariqi.com
                        </p>
                        <p style="margin:15px 0 0;color:#888888;font-size:13px;">
                            © {{ date('Y') }} Rafiqni. All Rights Reserved.
                        </p>
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>

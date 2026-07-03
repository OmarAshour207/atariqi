<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Profile Update Rejected') }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f6f9;font-family:Arial,Helvetica,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f9;padding:40px 0;">
    <tr>
        <td align="center">

            <table width="620" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #e5e7eb;">

                <!-- Header -->
                <tr>
                    <td align="center" style="background:#D32F2F;padding:35px 30px;">
                        <h1 style="margin:0;color:#ffffff;font-size:28px;font-weight:bold;">
                            {{ __('Profile Update Rejected') }}
                        </h1>
                        <p style="margin:10px 0 0;color:#FEE2E2;font-size:15px;">
                            Rafiqni | رافقني
                        </p>
                    </td>
                </tr>

                <!-- English Section -->
                <tr>
                    <td style="padding:35px;color:#333333;line-height:1.8;">

                        <p style="margin-top:0;">
                            <strong>Greetings from the Rafiqni Team!</strong>
                        </p>

                        <h2 style="margin:15px 0 20px;font-size:22px;">
                            {{ __('Hello') }}, {{ $passenger->fullName ?? $passenger->email }}
                        </h2>

                        <p style="font-size:18px;font-weight:bold;color:#D32F2F;">
                            Your profile update could not be approved.
                        </p>

                        <p>
                            After reviewing the information you submitted, we were unable to approve your profile update at this time.
                        </p>

                        <table width="100%" cellpadding="10" cellspacing="0" style="margin-top:20px;border:1px solid #e5e7eb;border-collapse:collapse;">

                            <tr style="background:#f8fafc;">
                                <td width="30%"><strong>Reason</strong></td>
                                <td>{{ $reason }}</td>
                            </tr>

                        </table>

                        <p style="margin-top:25px;">
                            Please review the reason above, make the necessary corrections, and submit your profile update again. If you believe this decision was made in error, please contact our support team.
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
                            <strong>تحية طيبة من فريق تطبيق رافقني (Rafiqni)</strong>
                        </p>

                        <h2 style="margin:15px 0 20px;font-size:22px;">
                            مرحباً، {{ $passenger->fullName ?? $passenger->email }}
                        </h2>

                        <p style="font-size:18px;font-weight:bold;color:#D32F2F;">
                            تعذر الموافقة على تحديث بيانات حسابك.
                        </p>

                        <p>
                            بعد مراجعة المعلومات التي قمت بإرسالها، تعذر اعتماد طلب تحديث بيانات الحساب في الوقت الحالي.
                        </p>

                        <table width="100%" cellpadding="10" cellspacing="0" style="margin-top:20px;border:1px solid #e5e7eb;border-collapse:collapse;text-align:right;">
<tr style="background:#f8fafc;">
                                <td width="30%"><strong>سبب الرفض</strong></td>
                                <td>{{ $reason }}</td>
                            </tr>

                        </table>

                        <p style="margin-top:25px;">
                            يرجى مراجعة سبب الرفض الموضح أعلاه، وإجراء التعديلات المطلوبة، ثم إعادة إرسال طلب تحديث البيانات. وإذا كنت تعتقد أن هذا القرار قد صدر عن طريق الخطأ، فيسعد فريق الدعم الفني بمساعدتك.
                        </p>

                    </td>
                </tr>

                <!-- Footer -->
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
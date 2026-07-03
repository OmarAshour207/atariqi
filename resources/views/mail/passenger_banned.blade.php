<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Passenger Account Suspended') }}</title>
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
                            {{ __('Passenger Account Suspended') }}
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
                            Your account has been suspended.
                        </p>

                        <p>
                            Following a review of your account, it has been suspended due to a violation of our platform policies.
                        </p>

                        <table width="100%" cellpadding="10" cellspacing="0" style="margin-top:20px;border:1px solid #e5e7eb;border-collapse:collapse;">

                            <tr style="background:#f8fafc;">
                                <td width="30%"><strong>Reason</strong></td>
                                <td>{{ $reason }}</td>
                            </tr>

                        </table>

                        <p style="margin-top:25px;">
                            If you believe this action was taken in error, please contact our support team for further assistance.
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
                            تم إيقاف حسابك.
                        </p>

                        <p>
                            نود إشعارك بأنه تم إيقاف حسابك بعد مراجعة حالته، وذلك لمخالفته سياسات وشروط استخدام منصة رافقني.
                        </p>

                        <table width="100%" cellpadding="10" cellspacing="0" style="margin-top:20px;border:1px solid #e5e7eb;border-collapse:collapse;text-align:right;">

<tr style="background:#f8fafc;">
                                <td width="30%"><strong>سبب الإيقاف</strong></td>
                                <td>{{ $reason }}</td>
                            </tr>

                        </table>

                        <p style="margin-top:25px;">
                            إذا كنت ترى أن هذا الإجراء قد تم عن طريق الخطأ، فيرجى التواصل مع فريق الدعم الفني، وسيتم مراجعة طلبك بكل اهتمام.
                        </p>

                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td align="center" style="background:#f8fafc;padding:30px;border-top:1px solid #e5e7eb;">

                        <p style="margin:0;color:#555555;font-size:15px;">
                            Our support team is always available to assist you.
                        </p>

                        <p style="margin:10px 0 0;color:#555555;font-size:15px;">
                            يسعد فريق الدعم الفني بخدمتك والإجابة عن جميع استفساراتك.
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


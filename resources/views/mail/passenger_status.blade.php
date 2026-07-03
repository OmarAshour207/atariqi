<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Account Status Update') }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f6f9;font-family:Arial,Helvetica,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f9;padding:40px 0;">
    <tr>
        <td align="center">

            <table width="620" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #e5e7eb;">

                <!-- Header -->
                <tr>
                    <td align="center" style="background:{{ $status === 'approved' ? '#16A34A' : '#D32F2F' }};padding:35px 30px;">
                        <h1 style="margin:0;color:#ffffff;font-size:28px;font-weight:bold;">
                            {{ __('Account Status Update') }}
                        </h1>
                        <p style="margin:10px 0 0;color:#F3F4F6;font-size:15px;">
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
                            Hello, {{ $passenger->{'user-first-name'} }} {{ $passenger->{'user-last-name'} }}
                        </h2>

                        @if($status === 'approved')
                            <p style="font-size:18px;font-weight:bold;color:#16A34A;">
                                🎉 Congratulations! Your account has been approved.
                            </p>

                            <p>
                                We are pleased to inform you that your account has been successfully approved. You can now enjoy all the services and features available through the Rafiqni platform.
                            </p>

                        @elseif($status === 'rejected')

                            <p style="font-size:18px;font-weight:bold;color:#D32F2F;">
                                Your account application was not approved.
                            </p>

                            <p>
                                After reviewing your application, we were unable to approve your account at this time.
                            </p>

                        @endif

                        @if($info)
                        <table width="100%" cellpadding="10" cellspacing="0" style="margin-top:20px;border:1px solid #e5e7eb;border-collapse:collapse;">
                            <tr style="background:#f8fafc;">
                                <td width="30%"><strong>Details</strong></td>
                                <td>{{ $info }}</td>
                            </tr>
                        </table>
                        @endif

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
                            مرحباً، {{ $passenger->{'user-first-name'} }} {{ $passenger->{'user-last-name'} }}
                        </h2>

                        @if($status === 'approved')
<p style="font-size:18px;font-weight:bold;color:#16A34A;">
                                🎉 مبارك! تمت الموافقة على حسابك.
                            </p>

                            <p>
                                يسعدنا إبلاغك بأنه تمت الموافقة على حسابك بنجاح، ويمكنك الآن الاستفادة من جميع خدمات ومزايا منصة رافقني.
                            </p>

                        @elseif($status === 'rejected')

                            <p style="font-size:18px;font-weight:bold;color:#D32F2F;">
                                تعذر الموافقة على طلب إنشاء حسابك.
                            </p>

                            <p>
                                بعد مراجعة طلبك، تعذر اعتماد الحساب في الوقت الحالي. يمكنك الاطلاع على التفاصيل أدناه، وفي حال احتجت إلى أي مساعدة، يسعد فريق الدعم الفني بخدمتك.
                            </p>

                        @endif

                        @if($info)
                        <table width="100%" cellpadding="10" cellspacing="0" style="margin-top:20px;border:1px solid #e5e7eb;border-collapse:collapse;text-align:right;">
                            <tr style="background:#f8fafc;">
                                <td width="30%"><strong>التفاصيل</strong></td>
                                <td>{{ $info }}</td>
                            </tr>
                        </table>
                        @endif

                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td align="center" style="background:#f8fafc;padding:30px;border-top:1px solid #e5e7eb;">

                        <p style="margin:0;color:#555555;font-size:15px;">
                            Thank you for choosing Rafiqni.
                        </p>

                        <p style="margin:10px 0 0;color:#555555;font-size:15px;">
                            شكراً لاختياركم تطبيق رافقني.
                        </p>

                        <p style="margin:20px 0 5px;color:#555555;font-size:15px;">
                            For any inquiries, please contact our technical support team.<br>
                            لأي استفسارات، يرجى التواصل مع فريق الدعم الفني.
                        </p>

                        <p style="margin:10px 0 5px;font-size:16px;font-weight:bold;color:#0F5CC0;">
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

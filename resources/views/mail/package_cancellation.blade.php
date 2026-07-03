<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Package Cancellation') }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f6f9;font-family:Arial,Helvetica,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f9;padding:40px 0;">
    <tr>
        <td align="center">

            <table width="620" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #e5e7eb;">

                <!-- Header -->
                <tr>
                    <td align="center" style="background:#D97706;padding:35px 30px;">
                        <h1 style="margin:0;color:#ffffff;font-size:28px;font-weight:bold;">
                            {{ __('Package Cancellation') }}
                        </h1>
                        <p style="margin:10px 0 0;color:#FEF3C7;font-size:15px;">
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
                            {{ __('Hello') }}, {{ $driver->fullName ?? $driver->{'user-first-name'} }}
                        </h2>

                        <p style="font-size:18px;font-weight:bold;color:#D97706;">
                            Your paid subscription has been cancelled.
                        </p>

                        <p>
                            Your account has been automatically moved to the Free Plan. Please find your updated subscription details below.
                        </p>

                        <table width="100%" cellpadding="10" cellspacing="0" style="margin-top:20px;border:1px solid #e5e7eb;border-collapse:collapse;">

                            <tr style="background:#f8fafc;">
                                <td width="35%"><strong>Previous Package</strong></td>
                                <td>{{ $oldPackage?->name_en ?? 'N/A' }}</td>
                            </tr>

                            <tr>
                                <td><strong>Current Package</strong></td>
                                <td>{{ $freePackage->name_en }}</td>
                            </tr>

                            <tr style="background:#f8fafc;">
                                <td><strong>Cancellation Date</strong></td>
                                <td>{{ now()->format('Y-m-d H:i') }}</td>
                            </tr>

                        </table>

                        <p style="margin-top:25px;">
                            You can log in to your account at any time to review your subscription details and explore the features available with your current plan.
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
                            مرحباً، {{ $driver->fullName ?? $driver->{'user-first-name'} }}
                        </h2>

                        <p style="font-size:18px;font-weight:bold;color:#D97706;">
                            تم إلغاء اشتراكك المدفوع.
                        </p>
<p>
                            نود إشعارك بأنه تم تحويل حسابك تلقائياً إلى <strong>الباقة المجانية</strong>، ويمكنك الاستمرار في استخدام التطبيق والاستفادة من المزايا المتوفرة ضمن هذه الباقة.
                        </p>

                        <p>
                            فيما يلي تفاصيل اشتراكك الحالية:
                        </p>

                        <table width="100%" cellpadding="10" cellspacing="0" style="margin-top:20px;border:1px solid #e5e7eb;border-collapse:collapse;text-align:right;">

                            <tr style="background:#f8fafc;">
                                <td width="35%"><strong>الباقة السابقة</strong></td>
                                <td>{{ $oldPackage?->name_ar ?? 'غير متوفر' }}</td>
                            </tr>

                            <tr>
                                <td><strong>الباقة الحالية</strong></td>
                                <td>{{ $freePackage->name_ar }}</td>
                            </tr>

                            <tr style="background:#f8fafc;">
                                <td><strong>تاريخ الإلغاء</strong></td>
                                <td>{{ now()->format('Y-m-d H:i') }}</td>
                            </tr>

                        </table>

                        <p style="margin-top:25px;">
                            يمكنك تسجيل الدخول إلى حسابك في أي وقت للاطلاع على تفاصيل اشتراكك الحالي، والتعرف على المزايا المتاحة، كما يمكنك الترقية إلى إحدى الباقات المدفوعة متى رغبت بذلك.
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
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Package Assignment Notification') }}</title>
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
                            {{ __('Package Assignment Notification') }}
                        </h1>
                        <p style="margin:10px 0 0;color:#dbeafe;font-size:15px;">
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

                        <p style="font-size:18px;font-weight:bold;color:#16a34a;">
                            🎉 Congratulations! You have been selected to receive a complimentary subscription upgrade.
                        </p>

                        <p>
                            We are pleased to inform you that your subscription has been upgraded. Below are your new subscription details:
                        </p>

                        <table width="100%" cellpadding="10" cellspacing="0" style="margin-top:20px;border:1px solid #e5e7eb;border-collapse:collapse;">

                            <tr style="background:#f8fafc;">
                                <td width="35%"><strong>{{ __('Package Name') }}</strong></td>
                                <td>{{ $package->name_en ?? $package->name_ar }}</td>
                            </tr>

                            <tr>
                                <td><strong>{{ __('Subscription') }}</strong></td>
                                <td>{{ $interval === 'yearly' ? ('Annual') : ('Monthly') }}</td>
                            </tr>

                            <tr style="background:#f8fafc;">
                                <td><strong>{{ __('Activation Date') }}</strong></td>
                                <td>{{ now()->format('Y-m-d H:i') }}</td>
                            </tr>

                        </table>

                        <p style="margin-top:25px;">
                            Please log in to your account to explore your upgraded package and enjoy its exclusive benefits.
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
<p style="font-size:18px;font-weight:bold;color:#16a34a;">
                            🎉 مبارك! تم اختيارك للحصول على ترقية مجانية لاشتراكك.
                        </p>

                        <p>
                            يسعدنا إبلاغك بأنه تمت ترقية اشتراكك، وفيما يلي تفاصيل الاشتراك الجديد:
                        </p>

                        <table width="100%" cellpadding="10" cellspacing="0" style="margin-top:20px;border:1px solid #e5e7eb;border-collapse:collapse;text-align:right;">

                            <tr style="background:#f8fafc;">
                                <td width="35%"><strong>اسم الباقة</strong></td>
                                <td>{{ $package->name_ar ?? $package->name_en }}</td>
                            </tr>

                            <tr>
                                <td><strong>نوع الاشتراك</strong></td>
                                <td>{{ $interval === 'yearly' ? 'سنوي' : 'شهري' }}</td>
                            </tr>

                            <tr style="background:#f8fafc;">
                                <td><strong>تاريخ التفعيل</strong></td>
                                <td>{{ now()->format('Y-m-d H:i') }}</td>
                            </tr>

                        </table>

                        <p style="margin-top:25px;">
                            يمكنك الآن تسجيل الدخول إلى حسابك للاطلاع على تفاصيل الباقة والاستفادة من جميع المزايا التي يوفرها اشتراكك الجديد.
                        </p>

                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td align="center" style="background:#f8fafc;padding:30px;border-top:1px solid #e5e7eb;">

                        <p style="margin:0;color:#555555;font-size:15px;">
                            If you have any questions, please contact our technical support team.
                        </p>

                        <p style="margin:10px 0 0;color:#555555;font-size:15px;">
                            إذا كان لديك أي استفسار، يرجى التواصل مع فريق الدعم الفني.
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
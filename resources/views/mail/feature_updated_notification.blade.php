<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Feature Updated Notification') }}</title>
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
                            {{ __('Feature Updated Notification') }}
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
                            {{ __('Hello') }}, {{ $customer->{"user-first-name"} }} {{ $customer->{"user-last-name"} }}
                        </h2>

                        <p>
                            {{ __('The feature you are interested in has been updated. Here are the latest details:') }}
                        </p>

                        <table width="100%" cellpadding="10" cellspacing="0" style="margin-top:20px;border:1px solid #e5e7eb;border-collapse:collapse;">

                            <tr style="background:#f8fafc;">
                                <td width="35%"><strong>{{ __('Feature Name') }}</strong></td>
                                <td>{{ $feature->name_en }}</td>
                            </tr>

                            <tr>
                                <td><strong>{{ __('Description') }}</strong></td>
                                <td>{{ $feature->description_en }}</td>
                            </tr>

                        </table>

                        <p style="margin-top:25px;">
                            {{ __('Visit our platform to learn more and discover the latest updates.') }}
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
                            مرحباً، {{ $customer->{"user-first-name"} }} {{ $customer->{"user-last-name"} }}
                        </h2>

                        <p>
                            يسعدنا إبلاغك بأنه تم تحديث إحدى الميزات التي أبديت اهتمامك بها. فيما يلي أحدث التفاصيل:
                        </p>

                        <table width="100%" cellpadding="10" cellspacing="0" style="margin-top:20px;border:1px solid #e5e7eb;border-collapse:collapse;text-align:right;">

                            <tr style="background:#f8fafc;">
                                <td width="35%"><strong>اسم الميزة</strong></td>
                                <td>{{ $feature->name_ar }}</td>
                            </tr>
<tr>
                                <td><strong>الوصف</strong></td>
                                <td>{{ $feature->description_ar }}</td>
                            </tr>

                        </table>

                        <p style="margin-top:25px;">
                            ندعوك لزيارة منصتنا للاطلاع على المزيد من التفاصيل والاستفادة من أحدث المزايا المتوفرة.
                        </p>

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

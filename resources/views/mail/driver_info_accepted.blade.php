<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Driver Information Update Accepted') }}</title>
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
                            {{ __('Driver Information Update Accepted') }}
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
                            {{ __('Hello') }}, {{ $driver->fullName ?? $driver->email }}
                        </h2>

                        <p>
                            {{ __('Your driver information has been successfully reviewed and accepted.') }}
                        </p>

                        <p>
                            Your updated information has been verified and is now active in our system.
                        </p>

                        <p>
                            Thank you for keeping your account information accurate and up to date.
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
                            مرحباً، {{ $driver->fullName ?? $driver->email }}
                        </h2>

                        <p>
                            يسعدنا إبلاغك بأنه تمت <strong>مراجعة وقبول بيانات السائق الخاصة بك.</strong>
                        </p>

                        <p>
                            تم اعتماد البيانات المحدثة وأصبحت فعالة في نظامنا.
                        </p>

                        <p>
                            نشكرك على حرصك على تحديث بيانات حسابك، ونتمنى لك تجربة موفقة مع رافقني.
                        </p>

                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td align="center" style="background:#f8fafc;padding:30px;border-top:1px solid #e5e7eb;">

                        <p style="margin:0;color:#555555;font-size:15px;">
                            {{ __('If you believe this is a mistake, please contact support.') }}
                        </p>

                        <p style="margin:10px 0 0;color:#555555;font-size:15px;">
                            إذا كنت تعتقد أن هذا الإجراء تم بالخطأ، فيرجى التواصل مع فريق الدعم الفني.
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
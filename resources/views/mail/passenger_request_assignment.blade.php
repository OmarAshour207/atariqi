<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Passenger Profile Update Assignment') }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f6f9;font-family:Arial,Helvetica,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f9;padding:40px 0;">
    <tr>
        <td align="center">

            <table width="620" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #e5e7eb;">

                <!-- Header -->
                <tr>
                    <td align="center" style="background:#2563EB;padding:35px 30px;">
                        <h1 style="margin:0;color:#ffffff;font-size:28px;font-weight:bold;">
                            {{ __('Passenger Profile Update Assignment') }}
                        </h1>
                        <p style="margin:10px 0 0;color:#DBEAFE;font-size:15px;">
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
                            Hello, {{ $admin->name }}
                        </h2>

                        <p>
                            A passenger profile update request has been assigned to you for review. Please review the request and take the appropriate action.
                        </p>

                        <table width="100%" cellpadding="10" cellspacing="0" style="margin-top:20px;border:1px solid #e5e7eb;border-collapse:collapse;">

                            <tr style="background:#f8fafc;">
                                <td width="35%"><strong>Passenger</strong></td>
                                <td>{{ $passenger->{'user-first-name'} }} {{ $passenger->{'user-last-name'} }} ({{ $passenger->email }})</td>
                            </tr>

                            <tr>
                                <td><strong>Assigned By</strong></td>
                                <td>{{ $assignedBy->name }}</td>
                            </tr>

                            <tr style="background:#f8fafc;">
                                <td><strong>Note</strong></td>
                                <td>{{ $note }}</td>
                            </tr>

                        </table>

                        <p style="margin-top:25px;">
                            Please log in to the administration dashboard to review the request and complete the required action.
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
                            مرحباً، {{ $admin->name }}
                        </h2>

                        <p>
                            تم إسناد طلب تحديث بيانات أحد الركاب إليك لمراجعته. نأمل الاطلاع على الطلب واتخاذ الإجراء المناسب وفقاً لسياسات المنصة.
                        </p>

                        <table width="100%" cellpadding="10" cellspacing="0" style="margin-top:20px;border:1px solid #e5e7eb;border-collapse:collapse;text-align:right;">

<tr style="background:#f8fafc;">
                                <td width="35%"><strong>الراكب</strong></td>
                                <td>{{ $passenger->{'user-first-name'} }} {{ $passenger->{'user-last-name'} }} ({{ $passenger->email }})</td>
                            </tr>

                            <tr>
                                <td><strong>تم الإسناد بواسطة</strong></td>
                                <td>{{ $assignedBy->name }}</td>
                            </tr>

                            <tr style="background:#f8fafc;">
                                <td><strong>الملاحظة</strong></td>
                                <td>{{ $note }}</td>
                            </tr>

                        </table>

                        <p style="margin-top:25px;">
                            يرجى تسجيل الدخول إلى لوحة التحكم لمراجعة الطلب واستكمال الإجراء المطلوب في أقرب وقت ممكن.
                        </p>

                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td align="center" style="background:#f8fafc;padding:30px;border-top:1px solid #e5e7eb;">

                        <p style="margin:0;color:#555555;font-size:15px;">
                            This is an automated notification from the Rafiqni Platform.
                        </p>

                        <p style="margin:10px 0 0;color:#555555;font-size:15px;">
                            هذه رسالة إشعار آلية صادرة من منصة رافقني.
                        </p>

                        <p style="margin:20px 0 5px;color:#555555;font-size:15px;">
                            For technical support, please contact:<br>
                            للدعم الفني، يرجى التواصل عبر:
                        </p>

                        <p style="margin:10px 0 5px;font-size:16px;font-weight:bold;color:#2563EB;">
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
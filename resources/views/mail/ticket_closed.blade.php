<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Support Ticket Closed') }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f6f9;font-family:Arial,Helvetica,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f9;padding:40px 0;">
    <tr>
        <td align="center">
            <table width="620" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #e5e7eb;">
                <tr>
                    <td align="center" style="background:#0F5CC0;padding:35px 30px;">
                        <h1 style="margin:0;color:#ffffff;font-size:26px;">{{ __('Support Ticket Closed') }}</h1>
                        <p style="margin:10px 0 0;color:#dbeafe;">Rafiqni | رافقني</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:35px;color:#333;line-height:1.8;">
                        <p>{{ __('Hello') }}, {{ $ticket->customer_name ?: $ticket->customer_email }}</p>
                        <p>{{ __('Your support ticket has been closed.') }}</p>
                        <table width="100%" cellpadding="8" cellspacing="0" style="border:1px solid #e5e7eb;border-collapse:collapse;">
                            <tr style="background:#f8fafc;">
                                <td width="35%"><strong>{{ __('Ticket Number') }}</strong></td>
                                <td>{{ $ticket->ticket_number }}</td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('Ticket Title') }}</strong></td>
                                <td>{{ $ticket->title }}</td>
                            </tr>
                            <tr style="background:#f8fafc;">
                                <td><strong>{{ __('Closed At') }}</strong></td>
                                <td>{{ optional($ticket->closed_at)->format('Y-m-d H:i') }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td dir="rtl" style="padding:35px;text-align:right;color:#333;line-height:2;font-family:Tahoma,Arial,sans-serif;border-top:1px solid #e5e7eb;">
                        <p>مرحباً، {{ $ticket->customer_name ?: $ticket->customer_email }}</p>
                        <p>تم إغلاق تذكرة الدعم الخاصة بك.</p>
                        <p><strong>رقم التذكرة:</strong> {{ $ticket->ticket_number }}</p>
                        <p><strong>وقت الإغلاق:</strong> {{ optional($ticket->closed_at)->format('Y-m-d H:i') }}</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>

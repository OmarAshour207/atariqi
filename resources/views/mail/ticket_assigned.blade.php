<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Support Ticket Assigned') }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f6f9;font-family:Arial,Helvetica,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f9;padding:40px 0;">
    <tr>
        <td align="center">
            <table width="620" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #e5e7eb;">
                <tr>
                    <td align="center" style="background:#38B2AC;padding:35px 30px;">
                        <h1 style="margin:0;color:#ffffff;font-size:26px;">{{ __('Support Ticket Assigned') }}</h1>
                        <p style="margin:10px 0 0;color:#e6fffb;">Rafiqni | رافقني</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:35px;color:#333;line-height:1.8;">
                        <p>{{ __('Hello') }}, {{ $assignee->name }}</p>
                        <p>{{ __('A support ticket has been assigned to you.') }}</p>
                        <table width="100%" cellpadding="8" cellspacing="0" style="border:1px solid #e5e7eb;border-collapse:collapse;">
                            <tr style="background:#f8fafc;">
                                <td width="35%"><strong>{{ __('Ticket Number') }}</strong></td>
                                <td>{{ $ticket->ticket_number }}</td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('Type') }}</strong></td>
                                <td>{{ $ticket->type_label }}</td>
                            </tr>
                            <tr style="background:#f8fafc;">
                                <td><strong>{{ __('Customer Email') }}</strong></td>
                                <td>{{ $ticket->customer_email }}</td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('Assigned By') }}</strong></td>
                                <td>{{ $assignedBy->name }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td dir="rtl" style="padding:35px;text-align:right;color:#333;line-height:2;font-family:Tahoma,Arial,sans-serif;border-top:1px solid #e5e7eb;">
                        <p>مرحباً، {{ $assignee->name }}</p>
                        <p>تم إسناد تذكرة دعم فني إليك.</p>
                        <p><strong>رقم التذكرة:</strong> {{ $ticket->ticket_number }}</p>
                        <p><strong>النوع:</strong> {{ $ticket->type_label }}</p>
                        <p><strong>تم الإسناد بواسطة:</strong> {{ $assignedBy->name }}</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>

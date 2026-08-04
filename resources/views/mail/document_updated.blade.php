<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>{{ __('Document Updated') }}</title></head>
<body style="font-family:Arial,sans-serif;line-height:1.7;color:#333;">
<p>{{ __('Hello') }}, {{ $user->{'user-first-name'} }} {{ $user->{'user-last-name'} }}</p>
<p>{{ __('A platform document has been updated.') }}</p>
<p><strong>{{ $document->{'title-ar'} ?? $document->{'title-eng'} }}</strong></p>
<p>{{ __('Please review the updated document in the app.') }}</p>
<hr>
<p dir="rtl">تم تحديث أحد مستندات المنصة. يرجى مراجعته داخل التطبيق.</p>
</body>
</html>

<!DOCTYPE html>

<html>

<head>

    <title>atariqi.com</title>

</head>

<body>

<h1 style="text-align: right">اشعار تذكير بدفع المستحقات</h1>

<div style="font-family: Helvetica,Arial,sans-serif;min-width:1000px;overflow:auto;line-height:2; text-align: right">
    <div style="margin:50px auto;width:70%;padding:20px 0">
        <div style="border-bottom:1px solid #eee">
            <a href="https://atariqi.com" style="font-size:1.4em;color: #00466a;text-decoration:none;font-weight:600">عطريقي</a>
        </div>
        <p style="font-size:1.1em">مرحبا , {{ $details['name'] }}</p>
        <p>من فضلك أدفع المستحقات المطلوبه لتجنب ايقاف الخدمة</p>
        <h2 style="background: #00466a;margin: 0 auto;width: max-content;padding: 0 10px;color: #fff;border-radius: 4px;">
            {{ $details['amount'] . ' ' . 'ريال سعودي' }}
        </h2>
        <p style="font-size:0.9em;">مع تحياتي,<br />عطريقي</p>
        <hr style="border:none;border-top:1px solid #eee" />
    </div>
</div>

<hr style="border:none;border-top:1px solid #eee" />

<h1>{{ __('Payment reminder notify') }}</h1>

<div style="font-family: Helvetica,Arial,sans-serif;min-width:1000px;overflow:auto;line-height:2">
    <div style="margin:50px auto;width:70%;padding:20px 0">
        <div style="border-bottom:1px solid #eee">
            <a href="https://atariqi.com" style="font-size:1.4em;color: #00466a;text-decoration:none;font-weight:600">Atariqi</a>
        </div>
        <p style="font-size:1.1em">{{ __('Hi') }}, {{ $details['name'] }}</p>
        <p>{{ __('please pay your due to avoid stop the service') }}</p>
        <h2 style="background: #00466a;margin: 0 auto;width: max-content;padding: 0 10px;color: #fff;border-radius: 4px;">
            {{ $details['amount'] . ' ' . __('SAR') }}
        </h2>
        <p style="font-size:0.9em;">Regards,<br />Atariqi</p>
        <hr style="border:none;border-top:1px solid #eee" />
    </div>
</div>

</body>

</html>

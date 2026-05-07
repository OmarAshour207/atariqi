<!DOCTYPE html>
<html>
<head>
    <title>Unauthorized Login Attempt</title>
</head>
<body>
    <h1>Unauthorized Login Attempt Detected</h1>
    <p>An unauthorized login attempt has been blocked due to too many failed attempts.</p>
    <ul>
        <li><strong>IP Address:</strong> {{ $ip }}</li>
        <li><strong>User Agent:</strong> {{ $userAgent }}</li>
        <li><strong>URL:</strong> {{ $url }}</li>
        <li><strong>Time:</strong> {{ $time }}</li>
    </ul>
    <p>Please investigate this activity.</p>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>User Verification</title>
</head>
<body>
    <h2>Hello!</h2>
    <p>Thank you for registering with us.</p>
    <p>Your verification code is:</p>

    <h1 style="background-color:#f0f0f0; padding:10px; display:inline-block;">
        {{ $mailData['code'] }}
    </h1>

    <p>Please enter this code in the app to verify your account.</p>

    <br><br>
    <p>Regards,<br>{{ config('app.name') }}</p>
</body>
</html>

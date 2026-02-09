<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Verify your email</title>
</head>
<body>
    <p>Hello {{ $user->name }},</p>
    <p>Your verification code is:</p>
    <h2 style="letter-spacing: 4px;">{{ $code }}</h2>
    <p>This code will expire in 10 minutes.</p>
    <p>If you did not create an account, you can ignore this email.</p>
    <p>— Library System</p>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Password Reset Request</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f7;
            color: #51545E;
            padding: 20px;
        }
        .container {
            background-color: #ffffff;
            max-width: 600px;
            margin: auto;
            padding: 30px;
            border-radius: 6px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .btn {
            display: inline-block;
            padding: 12px 20px;
            background-color: #3869D4;
            color: #ffffff;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #6B6E76;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Password Reset Request</h2>
    <p>Hello,</p>
    <p>You are receiving this email because we received a password reset request for your account.</p>
    <p>Click the button below to reset your password:</p>

{{--    <a href="{{ url('/reset-password?token=' . $token) }}" class="btn">Reset Password</a>--}}

    <p>If you did not request a password reset, no further action is required.</p>

    <p>Thanks,<br>The {{ config('app.name') }} Team</p>
    <p>Pin Code: {{ $token }} </p>

    <div class="footer">
        If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:<br>
{{--        <a href="{{ url('/reset-password?token=' . $token) }}">{{ url('/reset-password?token=' . $token) }}</a>--}}
    </div>
</div>
</body>
</html>

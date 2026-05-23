<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .card { background-color: #ffffff; padding: 30px; border-radius: 10px; border: 2px solid #fce206; max-width: 500px; margin: 0 auto; }
        h1 { color: #00628f; text-align: center; }
        .code { font-size: 32px; font-weight: bold; color: #f19d19; text-align: center; letter-spacing: 5px; margin: 20px 0; }
        p { color: #333; line-height: 1.6; }
        .footer { text-align: center; font-size: 12px; color: #888; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Krusty Krab Verification</h1>
        <p>Hello! Thank you for joining the Krusty Krab. Please use the verification code below to complete your registration:</p>
        <div class="code">{{ $code }}</div>
        <p>If you did not request this, please ignore this email.</p>
        <div class="footer">
            &copy; {{ date('Y') }} Krusty Krab. Home of the world-famous Krabby Patty!
        </div>
    </div>
</body>
</html>

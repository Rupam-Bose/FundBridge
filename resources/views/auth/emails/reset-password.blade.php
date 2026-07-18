<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password — FundBridge</title>
    <style>
        body { margin:0; padding:0; background:#061426; font-family:'Inter',Arial,sans-serif; color:#fff; }
        .wrapper { max-width:560px; margin:40px auto; }
        .card { background:#0b1c34; border:1px solid rgba(255,255,255,.1); border-radius:20px; overflow:hidden; }
        .header { background:linear-gradient(135deg,rgba(0,217,156,.3),rgba(37,99,235,.2)); padding:36px; text-align:center; }
        .logo { font-size:28px; font-weight:900; color:#fff; }
        .logo span { color:#00d99c; }
        .body { padding:36px; }
        .body h1 { font-size:22px; font-weight:700; margin:0 0 12px; }
        .body p { color:#9eacc2; line-height:1.7; margin:0 0 20px; }
        .btn { display:inline-block; padding:14px 32px; background:linear-gradient(135deg,#00d99c,#00b982); color:#000; font-weight:700; border-radius:50px; text-decoration:none; font-size:15px; }
        .url-box { background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.1); border-radius:10px; padding:12px 16px; margin:20px 0; word-break:break-all; font-size:12px; color:#9eacc2; }
        .footer { padding:20px 36px; border-top:1px solid rgba(255,255,255,.08); text-align:center; font-size:12px; color:#9eacc2; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="card">
        <div class="header">
            <div class="logo"><span>Fund</span>Bridge</div>
        </div>
        <div class="body">
            <h1>🔐 Reset Your Password</h1>
            <p>We received a request to reset the password for your FundBridge account associated with <strong>{{ $userEmail }}</strong>.</p>
            <p>Click the button below to choose a new password. This link will expire in <strong>60 minutes</strong>.</p>
            <p style="text-align:center;">
                <a href="{{ $resetUrl }}" class="btn">Reset My Password</a>
            </p>
            <p>If the button doesn't work, copy and paste this link into your browser:</p>
            <div class="url-box">{{ $resetUrl }}</div>
            <p>If you didn't request a password reset, no action is needed — your account is safe.</p>
        </div>
        <div class="footer">
            © {{ date('Y') }} FundBridge. All rights reserved. &nbsp;|&nbsp; Bridging Ideas, Capital &amp; Opportunities
        </div>
    </div>
</div>
</body>
</html>

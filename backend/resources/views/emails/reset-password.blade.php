<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Reset Your Password</title>
</head>
<body style="margin:0;padding:0;background:#f5f4ff;font-family:'Segoe UI',sans-serif;">

  <table width="100%" cellpadding="0" cellspacing="0" style="padding:40px 20px;">
    <tr>
      <td align="center">
        <table width="520" cellpadding="0" cellspacing="0"
          style="background:#ffffff;border-radius:16px;overflow:hidden;
                 border:1px solid #e2e0ff;box-shadow:0 4px 24px rgba(124,58,237,0.08);">

          <!-- Header -->
          <tr>
            <td style="background:linear-gradient(135deg,#7c3aed,#9333ea);padding:36px 40px;text-align:center;">
              <div style="width:48px;height:48px;background:rgba(255,255,255,0.15);
                border-radius:12px;display:inline-flex;align-items:center;
                justify-content:center;margin-bottom:16px;">
                <span style="font-size:22px;">✦</span>
              </div>
              <h1 style="margin:0;color:#ffffff;font-size:22px;font-weight:700;letter-spacing:-0.5px;">
                BNP Dashboard
              </h1>
            </td>
          </tr>

          <!-- Body -->
          <tr>
            <td style="padding:40px;">
              <h2 style="margin:0 0 8px;color:#1e1b4b;font-size:20px;font-weight:700;">
                Reset your password
              </h2>
              <p style="margin:0 0 24px;color:#64748b;font-size:14px;line-height:1.6;">
                We received a request to reset the password for your account.
                Click the button below to set a new password. This link will expire in
                <strong style="color:#7c3aed;">60 minutes</strong>.
              </p>

              <!-- Button -->
              <div style="text-align:center;margin:32px 0;">
                <a href="{{ $resetUrl }}"
                  style="display:inline-block;background:linear-gradient(135deg,#7c3aed,#9333ea);
                    color:#ffffff;text-decoration:none;font-size:15px;font-weight:600;
                    padding:14px 36px;border-radius:12px;
                    box-shadow:0 4px 16px rgba(124,58,237,0.35);">
                  Reset Password
                </a>
              </div>

              <p style="margin:24px 0 0;color:#94a3b8;font-size:13px;line-height:1.6;">
                If the button doesn't work, copy and paste this link into your browser:
              </p>
              <p style="margin:8px 0 24px;word-break:break-all;">
                <a href="{{ $resetUrl }}"
                  style="color:#7c3aed;font-size:12px;text-decoration:none;">
                  {{ $resetUrl }}
                </a>
              </p>

              <div style="border-top:1px solid #f1f5f9;padding-top:24px;margin-top:8px;">
                <p style="margin:0;color:#94a3b8;font-size:13px;line-height:1.6;">
                  If you didn't request a password reset, you can safely ignore this email.
                  Your password will remain unchanged.
                </p>
              </div>
            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td style="background:#f8f7ff;padding:20px 40px;text-align:center;
              border-top:1px solid #ede9fe;">
              <p style="margin:0;color:#94a3b8;font-size:12px;">
                © {{ date('Y') }} BNP Dashboard. All rights reserved.
              </p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>

</body>
</html>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{ $appName }}</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="color: white; margin: 0; font-size: 28px;">Welcome to {{ $appName }}!</h1>
    </div>
    
    <div style="background: #ffffff; padding: 40px; border-radius: 0 0 10px 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <p style="font-size: 16px; margin-bottom: 20px;">Hi {{ $user->name }},</p>
        
        <p style="font-size: 16px; margin-bottom: 20px;">
            Thank you for signing up! We're excited to have you on board.
        </p>
        
        <p style="font-size: 16px; margin-bottom: 20px;">
            Your account has been successfully created. You can now start using all the features of {{ $appName }}.
        </p>
        
        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 30px 0;">
            <p style="margin: 0; font-size: 14px; color: #666;">
                <strong>Account Details:</strong><br>
                Email: {{ $user->email }}<br>
                @if($user->phone)
                Phone: {{ $user->phone }}<br>
                @endif
            </p>
        </div>
        
        <p style="font-size: 16px; margin-bottom: 20px;">
            If you have any questions or need assistance, feel free to reach out to our support team.
        </p>
        
        <p style="font-size: 16px; margin-top: 30px;">
            Best regards,<br>
            <strong>The {{ $appName }} Team</strong>
        </p>
    </div>
    
    <div style="text-align: center; margin-top: 20px; color: #999; font-size: 12px;">
        <p>This is an automated message. Please do not reply to this email.</p>
    </div>
</body>
</html>


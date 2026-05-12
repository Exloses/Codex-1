@php
    $brandName = $data['brandName'] ?? 'GlobalDropship';
    $logoText = $data['logoText'] ?? 'GD';
    $supportEmail = $data['supportEmail'] ?? config('mail.from.address');
    $unsubscribeUrl = $data['unsubscribeUrl'] ?? url('/newsletter/unsubscribe/preferences');
    $companyAddress = $data['companyAddress'] ?? 'GlobalDropship, Jakarta, Indonesia';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $brandName }}</title>
    <style>
        body { margin: 0; padding: 0; background: #eff6ff; color: #0f172a; font-family: Arial, Helvetica, sans-serif; }
        .shell { width: 100%; padding: 24px 12px; }
        .email { max-width: 600px; margin: 0 auto; overflow: hidden; border-radius: 8px; background: #ffffff; border: 1px solid #dbeafe; }
        .header { padding: 22px 28px; background: #0f5ed7; color: #ffffff; }
        .brand { color: #ffffff; text-decoration: none; font-size: 20px; font-weight: 700; }
        .logo { display: inline-block; width: 38px; height: 38px; margin-right: 10px; border-radius: 8px; background: #ffffff; color: #0f5ed7; line-height: 38px; text-align: center; font-weight: 700; vertical-align: middle; }
        .content { padding: 28px; }
        h1 { margin: 0 0 12px; font-size: 24px; line-height: 1.25; color: #0f172a; }
        h2 { margin: 24px 0 10px; font-size: 16px; color: #0f172a; }
        p { margin: 0 0 14px; font-size: 15px; line-height: 1.6; color: #334155; }
        .muted { color: #64748b; }
        .panel { margin: 18px 0; padding: 16px; border: 1px solid #bfdbfe; border-radius: 8px; background: #f8fbff; }
        .metric { margin: 0; color: #0f5ed7; font-size: 22px; font-weight: 700; }
        .button { display: inline-block; margin-top: 8px; padding: 12px 18px; border-radius: 6px; background: #0f5ed7; color: #ffffff !important; font-weight: 700; text-decoration: none; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { padding: 10px 0; border-bottom: 1px solid #e2e8f0; font-size: 14px; text-align: left; vertical-align: top; }
        th { color: #475569; font-weight: 700; }
        .right { text-align: right; }
        .address { white-space: pre-line; }
        .footer { padding: 20px 28px; background: #f8fafc; border-top: 1px solid #e2e8f0; }
        .footer p { margin: 0 0 8px; font-size: 12px; line-height: 1.5; color: #64748b; }
        .footer a { color: #0f5ed7; }
        @media (max-width: 640px) {
            .shell { padding: 0; }
            .email { border-radius: 0; border-left: 0; border-right: 0; }
            .header, .content, .footer { padding-left: 18px; padding-right: 18px; }
            h1 { font-size: 22px; }
            th, td { font-size: 13px; }
            .button { display: block; text-align: center; }
        }
    </style>
</head>
<body>
    <div class="shell">
        <div class="email">
            <div class="header">
                <a class="brand" href="{{ $data['storeUrl'] ?? url('/') }}">
                    <span class="logo">{{ $logoText }}</span>{{ $brandName }}
                </a>
            </div>

            <div class="content">
                @yield('content')
            </div>

            <div class="footer">
                <p>Need help? Contact <a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a>.</p>
                <p>{{ $companyAddress }}</p>
                <p><a href="{{ $unsubscribeUrl }}">Unsubscribe from email notifications</a></p>
            </div>
        </div>
    </div>
</body>
</html>

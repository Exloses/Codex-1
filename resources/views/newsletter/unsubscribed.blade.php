<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Newsletter Preferences</title>
    <style>
        body { margin: 0; font-family: Arial, sans-serif; background: #f4f4f5; color: #18181b; }
        main { max-width: 560px; margin: 12vh auto; padding: 32px; background: #fff; border: 1px solid #e4e4e7; border-radius: 8px; }
        a { color: #047857; font-weight: 700; }
    </style>
</head>
<body>
    <main>
        <h1>Newsletter preferences updated</h1>
        <p>{{ $message }}</p>
        <p><a href="{{ url('/') }}">Return to GlobalDrop</a></p>
    </main>
</body>
</html>

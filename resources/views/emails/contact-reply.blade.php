<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: 0 auto;">
    <h2>Balasan atas pesan Anda</h2>
    <p>Halo {{ $contactMessage->name }},</p>
    <p>Terima kasih telah menghubungi kami perihal "<strong>{{ $contactMessage->subject }}</strong>". Berikut balasan dari tim kami:</p>
    <div style="background: #f5f5f5; padding: 16px; border-radius: 8px; margin: 16px 0;">
        {{ $contactMessage->admin_reply }}
    </div>
    <hr>
    <p style="color: #888; font-size: 13px;"><strong>Pesan awal Anda:</strong><br>{{ $contactMessage->message }}</p>
    <p>Salam,<br>{{ config('app.name') }} Team</p>
</body>
</html>

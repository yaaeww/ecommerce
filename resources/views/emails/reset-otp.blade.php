<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode OTP Reset Kata Sandi</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 0;
            color: #334155;
        }
        .container {
            max-width: 540px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
        }
        .header {
            background: linear-gradient(135deg, #0d281e 0%, #1b4d3e 100%);
            padding: 32px 24px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            font-size: 22px;
            margin: 12px 0 0 0;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .header span {
            color: #f59e0b;
        }
        .content {
            padding: 32px 28px;
            text-align: center;
        }
        .content h2 {
            font-size: 18px;
            color: #0f172a;
            margin-top: 0;
            margin-bottom: 12px;
        }
        .content p {
            font-size: 14px;
            color: #64748b;
            line-height: 1.6;
            margin: 0 0 24px 0;
        }
        .otp-box {
            background: #ecfdf5;
            border: 2px dashed #10b981;
            border-radius: 12px;
            padding: 18px 24px;
            display: inline-block;
            margin: 8px 0 24px 0;
        }
        .otp-code {
            font-size: 32px;
            font-weight: 800;
            letter-spacing: 8px;
            color: #065f46;
            font-family: 'Courier New', Courier, monospace;
        }
        .alert-box {
            background: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 12px 16px;
            text-align: left;
            border-radius: 6px;
            font-size: 12px;
            color: #92400e;
            margin-bottom: 24px;
        }
        .footer {
            background: #f8fafc;
            padding: 20px 24px;
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1 style="color:#ffffff;">Juragan<span style="color:#f59e0b;">Pelem</span></h1>
            <p style="color:#a7f3d0; font-size:11px; margin:4px 0 0 0; text-transform:uppercase; letter-spacing:1px;">Marketplace Mangga & UMKM Indramayu</p>
        </div>

        <!-- Content -->
        <div class="content">
            <h2>Verifikasi Permintaan Reset Sandi</h2>
            <p>Halo <strong>{{ $userName }}</strong>, kami menerima permintaan untuk mengatur ulang kata sandi akun Juragan Pelem Anda. Gunakan kode OTP berikut untuk melanjutkan:</p>

            <div class="otp-box">
                <div class="otp-code">{{ $otp }}</div>
            </div>

            <div class="alert-box">
                ⚠️ <strong>Perhatian:</strong> Kode OTP ini hanya berlaku selama <strong>10 menit</strong>. Jangan pernah membagikan kode rahasia ini kepada pihak mana pun, termasuk pihak yang mengatasnamakan Juragan Pelem.
            </div>

            <p style="font-size: 12px; color: #94a3b8; margin-bottom: 0;">
                Jika Anda tidak merasa melakukan permintaan ini, abaikan email ini. Akun Anda tetap aman.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            &copy; {{ date('Y') }} Juragan Pelem Indramayu. Seluruh hak cipta dilindungi.<br>
            Email ini dikirimkan otomatis oleh sistem keamanan platform.
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Pementor Disetujui</title>
    <style>
        body {
            font-family: sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .header {
            font-size: 24px;
            font-weight: bold;
            color: #007BFF;
            text-align: center;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin: 20px 0;
            background-color: #007BFF;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">Selamat, {{ $user->name }}!</div>
        <p>
            Kami dengan gembira memberitahukan bahwa aplikasi Anda untuk menjadi pementor di BOM-PAI UNISBA telah kami setujui.
        </p>
        <p>
            Peran akun Anda di sistem telah diubah menjadi 'Mentor'. Anda kini dapat masuk ke portal mentoring untuk mengakses dasbor dan fitur khusus pementor.
        </p>
        <p style="text-align: center;">
            <a href="{{ route('login') }}" class="button">Masuk ke Portal Mentoring</a>
        </p>
        <p>
            Terima kasih atas kesediaan Anda untuk berkontribusi.
        </p>
        <p>
            Salam,<br>
            Tim Departemen Tutorial BOM-PAI UNISBA
        </p>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background-color: #f4fafe;
        color: #333;
        padding: 30px;
    }
    .email-container {
        background-color: #ffffff;
        padding: 40px;
        border-radius: 10px;
        max-width: 600px;
        margin: auto;
        border: 1px solid #d6e9f8;
    }
    .header {
        text-align: center;
        margin-bottom: 30px;
    }
    .header img {
        max-width: 180px;
        margin-bottom: 10px;
    }
    .title {
        font-size: 20px;
        font-weight: bold;
        color: #007bc7;
    }
    .panel {
        background-color: #e9f5fc;
        padding: 20px;
        border-left: 5px solid #007bc7;
        margin: 20px 0;
        font-size: 18px;
        text-align: center;
        font-weight: bold;
        letter-spacing: 2px;
        color: #007bc7;
    }
    .footer {
        font-size: 12px;
        color: #777;
        text-align: center;
        margin-top: 30px;
    }
    a {
        color: #007bc7;
    }
</style>
</head>
<body>
<div class="email-container">
    <div class="header">
        <img src="https://dreamland-movie.netlify.app/images/logo.png" alt="Logo Website Phim">
        <div class="title">XÁC MINH TÀI KHOẢN</div>
    </div>

    <p>Chào <strong>{{ $name }}</strong>,</p>

    <p>Bạn vừa yêu cầu đăng nhập vào tài khoản trên <strong>{{ config('setting.site.name') }}</strong>. Mã OTP của bạn như sau:</p>

    <div class="panel">
        {{ $code }}
    </div>

    <p>Mã OTP này có hiệu lực trong <strong>{{ $time }} giờ</strong>. Vui lòng không chia sẻ mã này với bất kỳ ai.</p>

    <p>Nếu bạn không yêu cầu mã này, vui lòng bỏ qua email này.</p>

    <p>Chúc bạn xem phim vui vẻ! — <strong>{{ config('setting.site.name') }}</strong> 🎬</p>

    <div class="footer">
        © {{ now()->year }} {{ config('setting.site.name') }}. All rights reserved.<br>
        Trụ sở: Quận 1, TP.HCM<br>
        Email: support@movie-site.vn | Hotline: 1800 0000
    </div>
</div>
</body>
</html>

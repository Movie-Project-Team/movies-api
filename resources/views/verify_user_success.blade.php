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
            color: #007bc7;
            letter-spacing: 1px;
        }
        .footer {
            font-size: 12px;
            color: #777;
            text-align: center;
            margin-top: 30px;
        }
    </style>
</head>
<body>
<div class="email-container">
    <div class="header">
        <img src="{{ asset('images/logo.png') }}" alt="Logo Website Phim">
        <div class="title">XÁC THỰC TÀI KHOẢN THÀNH CÔNG</div>
    </div>

    <p>Xin chúc mừng! Tài khoản của bạn trên <strong>{{ config('setting.site.name') }}</strong> đã được xác thực thành công.</p>

    <p>Chúng tôi đã tạo một hồ sơ mặc định cho bạn với thông tin đăng nhập sau:</p>

    <div class="panel">
        Mật khẩu: {{ $password }}
    </div>

    <p>Vui lòng đăng nhập và thay đổi mật khẩu của bạn ngay lập tức để đảm bảo an toàn.</p>

    <p>Nếu bạn không thực hiện hành động này, vui lòng liên hệ với bộ phận hỗ trợ của chúng tôi ngay lập tức.</p>

    <p>Chúc bạn có những phút giây giải trí tuyệt vời! — <strong>{{ config('setting.site.name') }}</strong></p>

    <div class="footer">
        © {{ now()->year }} {{ config('setting.site.name') }}. All rights reserved.<br>
        Website xem phim chất lượng cao, miễn phí, không quảng cáo!<br>
        Liên hệ: support@movie-site.vn | Hotline: 1800 0000
    </div>
</div>
</body>
</html>

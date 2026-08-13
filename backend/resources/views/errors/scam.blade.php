<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông báo quan trọng - Cảnh báo Scam</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #ff4757;
            --secondary-color: #2f3542;
            --text-color: #57606f;
            --bg-color: #f1f2f6;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            width: 100%;
            background: #fff;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
            border-top: 10px solid var(--primary-color);
        }

        .icon-box {
            width: 80px;
            height: 80px;
            background: rgba(255, 71, 87, 0.1);
            color: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            font-size: 40px;
        }

        h1 {
            color: var(--secondary-color);
            font-weight: 800;
            font-size: 28px;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        p {
            color: var(--text-color);
            line-height: 1.6;
            margin-bottom: 25px;
            font-size: 16px;
        }

        .info-card {
            background: #f9f9f9;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: left;
        }

        .info-item {
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            font-size: 14px;
        }

        .info-item .label {
            font-weight: 600;
            color: var(--secondary-color);
        }

        .info-item .value {
            color: var(--primary-color);
            font-family: monospace;
            font-weight: bold;
        }

        .warning-text {
            background: #fff5f5;
            color: #d63031;
            padding: 15px;
            border-radius: 8px;
            font-weight: 600;
            border: 1px dashed var(--primary-color);
            margin-bottom: 30px;
        }

        .btn {
            display: inline-block;
            background: var(--secondary-color);
            color: #fff;
            padding: 12px 30px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #a4b0be;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon-box">
            <i class="fas fa-shield-virus"></i>
        </div>
        <h1>Cảnh báo lừa đảo</h1>
        <p>Hệ thống của chúng tôi đã phát hiện và xác minh website này có dấu hiệu lừa đảo khách hàng.</p>
        
        <div class="warning-text">
            Hệ thống thuộc site con của miền mẹ <strong>{{ $parent_domain }}</strong> đã bị khóa do có hành vi Scam lừa đảo chiếm đoạt tài sản.
        </div>

        <div class="info-card">
            <div class="info-item">
                <span class="label">Website bị khóa:</span>
                <span class="value">{{ $domain }}</span>
            </div>
            <div class="info-item">
                <span class="label">Hệ thống quản lý:</span>
                <span class="value">{{ $parent_domain }}</span>
            </div>
        </div>

        <p>Nếu bạn có giao dịch tại đây, vui lòng liên hệ quản trị viên miền mẹ để được hỗ trợ.</p>
        
        <a href="https://{{ $parent_domain }}" class="btn">Quay lại miền mẹ</a>

        <div class="footer">
            &copy; {{ date('Y') }} {{ $parent_domain }} - Bảo vệ người dùng là ưu tiên hàng đầu.
        </div>
    </div>
</body>
</html>

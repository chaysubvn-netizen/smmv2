<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Thanh toán hóa đơn') }} | {{ s('title') }}</title>
    <link rel="shortcut icon" href="{{ asset(s('favicon')) }}" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7f6;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .payment-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 800px;
            overflow: hidden;
            padding: 40px;
        }

        .header-title {
            font-weight: 800;
            font-size: 1.5rem;
            color: #1a1a1a;
            margin-bottom: 40px;
            text-align: left;
        }

        .info-label {
            color: #6c757d;
            font-size: 0.85rem;
            font-weight: 500;
            width: 150px;
        }

        .info-value {
            color: #1a1a1a;
            font-weight: 700;
            font-size: 0.95rem;
        }

        .bank-name {
            color: #28a745;
            font-weight: 800;
            margin-right: 10px;
        }

        .qr-section {
            background: linear-gradient(135deg, #1abc9c 0%, #3498db 100%);
            border-radius: 16px;
            padding: 40px 20px;
            color: white;
            text-align: center;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .qr-section::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .qr-section::after {
            content: '••••\n••••';
            position: absolute;
            bottom: 20px;
            left: 20px;
            font-size: 1.5rem;
            color: rgba(255, 255, 255, 0.2);
            line-height: 0.5;
            white-space: pre;
        }

        .qr-box {
            background: white;
            padding: 20px;
            border-radius: 20px;
            margin: 20px 0;
            display: inline-block;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            max-width: 90%;
        }

        .qr-box img {
            width: 100%;
            height: auto;
            display: block;
        }

        .qr-instruction {
            font-size: 0.85rem;
            font-weight: 500;
            line-height: 1.4;
            max-width: 250px;
            margin: 0 auto;
        }

        .qr-instruction strong {
            font-weight: 800;
        }

        .copy-btn {
            background: #e9ecef;
            border: none;
            border-radius: 4px;
            padding: 2px 8px;
            color: #28a745;
            font-size: 0.8rem;
            margin-left: 10px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .copy-btn:hover {
            background: #28a745;
            color: white;
        }

        .countdown-container {
            background: #fff9db;
            border-radius: 8px;
            padding: 15px;
            margin-top: 30px;
            text-align: center;
            font-weight: 700;
            color: #495057;
        }

        .countdown-time {
            color: #fa5252;
            margin-left: 5px;
        }

        .cancel-btn {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #fa5252;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .cancel-btn:hover {
            text-decoration: underline;
        }

        .payment-info-row {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .payment-card {
                padding: 20px;
            }
            .qr-section {
                margin-top: 30px;
            }
            .payment-info-row {
                flex-direction: column;
                align-items: flex-start;
            }
            .info-label {
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>

<div class="payment-card">
    <h1 class="header-title">{{ s('title') ?: 'Cung cấp smm panel giá rẻ uy tín  nhất Việt Nam' }}</h1>

    <div class="row g-0">
        <div class="col-md-7 pe-md-4">
            <h5 class="fw-bold mb-4">{{ __('Thông tin thanh toán') }}</h5>
            
            <div class="payment-info-row">
                <span class="info-value"><span class="bank-name">{{ strtoupper($bank->bank_name) }}</span> Ngân hàng</span>
            </div>

            <div class="payment-info-row">
                <span class="info-label">{{ __('Chủ tài khoản:') }}</span>
                <span class="info-value">{{ strtoupper($bank->account_name) }}</span>
            </div>

            <div class="payment-info-row">
                <span class="info-label">{{ __('Số tài khoản:') }}</span>
                <span class="info-value">{{ $bank->account_number }}</span>
                <button class="copy-btn" onclick="copyToClipboard('{{ $bank->account_number }}')"><i class="fa-regular fa-copy"></i></button>
            </div>

            <div class="payment-info-row">
                <span class="info-label">{{ __('Số tiền:') }}</span>
                <span class="info-value">{{ number_format($recharge->amount) }}đ</span>
                <button class="copy-btn" onclick="copyToClipboard('{{ $recharge->amount }}')"><i class="fa-regular fa-copy"></i></button>
            </div>

            <div class="payment-info-row">
                <span class="info-label">{{ __('Nội dung chuyển khoản:') }}</span>
                <span class="info-value">{{ $recharge->transaction_id }}</span>
                <button class="copy-btn" onclick="copyToClipboard('{{ $recharge->transaction_id }}')"><i class="fa-regular fa-copy"></i></button>
            </div>

            <p class="text-muted fst-italic mt-3" style="font-size: 0.8rem;">({{ __('Vui lòng nhập chính xác nội dung và số tiền') }})</p>
        </div>

        <div class="col-md-5">
            <div class="qr-section">
                <p class="fw-bold mb-0 fs-5">{{ __('Quét mã QR để thanh toán') }}</p>
                <div class="qr-box">
                    <img src="https://img.vietqr.io/image/{{ $bank->bank_code }}-{{ $bank->account_number }}-compact2.png?amount={{ $recharge->amount }}&addInfo={{ $recharge->transaction_id }}&accountName={{ urlencode($bank->account_name) }}" alt="QR Code">
                </div>
                <div class="qr-instruction">
                    {{ __('Sử dụng ứng dụng') }} <strong>{{ __('ngân hàng') }}</strong> {{ __('hoặc') }} <strong>{{ __('ví điện tử') }}</strong> {{ __('để quét mã') }}
                </div>
            </div>
        </div>
    </div>

    <div class="countdown-container">
        {{ __('Đơn hàng sẽ hết hạn sau:') }} <span class="countdown-time" id="countdown">--:--</span>
    </div>

    <a href="{{ route('addfuns') }}" class="cancel-btn">{{ __('Hủy giao dịch') }}</a>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function startTimer(duration, display) {
        var timer = duration, minutes, seconds;
        var interval = setInterval(function () {
            minutes = parseInt(timer / 60, 10);
            seconds = parseInt(timer % 60, 10);

            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            display.textContent = minutes + ":" + seconds;

            if (--timer < 0) {
                clearInterval(interval);
                display.textContent = "00:00";
                Swal.fire({
                    title: '{{ __("Hết hạn") }}',
                    text: '{{ __("Hóa đơn đã hết hạn, vui lòng tạo hóa đơn mới") }}',
                    icon: 'error',
                    confirmButtonText: '{{ __("Quay lại") }}'
                }).then(() => {
                    window.location.href = "{{ route('addfuns') }}";
                });
            }
        }, 1000);
    }

    $(document).ready(function () {
        let duration = {{ now()->diffInSeconds($recharge->expired_at, false) }};
        const display = document.querySelector('#countdown');
        
        if (duration > 0) {
            startTimer(duration, display);
        } else {
            display.textContent = "00:00";
        }

        setInterval(checkPaymentStatus, 5000);
    });

    function checkPaymentStatus() {
        $.ajax({
            url: "{{ route('recharge.status', $recharge->id) }}",
            type: "GET",
            success: function(res) {
                if (res.status === 'completed') {
                    Swal.fire({
                        title: '{{ __("Thành công") }}',
                        text: '{{ __("Thanh toán thành công!") }}',
                        icon: 'success',
                        confirmButtonText: '{{ __("Tuyệt vời") }}'
                    }).then(() => {
                        window.location.href = "{{ route('addfuns') }}";
                    });
                }
            }
        });
    }

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            Swal.fire({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                icon: 'success',
                title: '{{ __("Đã sao chép") }}'
            });
        });
    }
</script>

</body>
</html>

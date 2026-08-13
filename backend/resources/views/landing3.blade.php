<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ s('title') }}</title>
    @include('components.seo-header')

    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <style>
.loader-box {
  display: inline-flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 25px 40px;
  background: rgba(25, 25, 35, 0.8);
  border-radius: 12px;
  box-shadow: 0 0 20px rgba(0, 140, 255, 0.3);
  backdrop-filter: blur(6px);
  animation: fadeIn 0.3s ease;
}

.loader-box span {
  margin-top: 10px;
  color: #b0d8ff;
  font-size: 14px;
  font-weight: 500;
  font-family: "Inter", sans-serif;
}

.loader-spinner {
  width: 40px;
  height: 40px;
  border: 4px solid rgba(255, 255, 255, 0.2);
  border-top: 4px solid #00bfff;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

/* Hiệu ứng quay và mờ */
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}
</style>
    <style>
        h3 {
            color: #000;
            /* màu đen */
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #19002c, #0b0120);
            color: white;
            overflow-x: hidden;
        }

        /* HEADER */
        /* ==== RESET & BASE ==== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            background: linear-gradient(135deg, #19002c, #0b0120);
            color: #fff;
            overflow-x: hidden;
        }

        /* ==== HEADER ==== */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 25px 80px;
        }

        .logo {
            font-size: 22px;
            font-weight: 700;
            color: #ff0077;
        }

        nav a {
            color: #fff;
            margin-left: 30px;
            text-decoration: none;
            font-weight: 500;
            transition: 0.3s;
        }

        nav a:hover {
            color: #ff0077;
        }

        .btn-register {
            background: #ff0077;
            border: none;
            padding: 10px 24px;
            border-radius: 25px;
            color: white;
            font-weight: 600;
            margin-left: 30px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-register:hover {
            background: #ff3399;
        }

        /* ==== HERO SECTION ==== */
        .hero {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 80px;
            flex-wrap: wrap;
            position: relative;
            z-index: 1;
        }

        .hero-text {
            max-width: 550px;
            z-index: 2;
        }

        .hero-text h1 {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .hero-text span {
            color: #ff0077;
        }

        .hero-text p {
            color: #cfcfcf;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        .hero-text .btn {
            background: #ff0077;
            color: white;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 30px;
            font-weight: 600;
            transition: 0.3s;
            display: inline-block;
        }

        .hero-text .btn:hover {
            background: #ff3399;
        }

        /* ==== HERO IMAGE SIDE ==== */
        .hero-image {
            position: relative;
            width: 400px;
            height: 400px;
        }

        .hero-image img.main {
            width: 100%;
            border-radius: 20px;
            position: relative;
            z-index: 2;
        }

        /* ==== SOCIAL ICONS ==== */
        .social-icon {
            position: absolute;
            width: 55px;
            height: 55px;
            background: white;
            border-radius: 15px;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            animation: float 5s ease-in-out infinite;
        }

        .social-icon img {
            width: 30px;
            height: 30px;
            object-fit: contain;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-12px);
            }
        }

        /* ==== ICON POSITIONS (dịch vào trong hơn) ==== */
        .social-icon:nth-child(2) {
            top: 10px;
            left: 10px;
            animation-delay: 0s;
        }

        .social-icon:nth-child(3) {
            top: -15px;
            right: 25px;
            animation-delay: 0.4s;
        }

        .social-icon:nth-child(4) {
            bottom: 35px;
            left: -10px;
            animation-delay: 0.8s;
        }

        .social-icon:nth-child(5) {
            bottom: -10px;
            right: 20px;
            animation-delay: 1.2s;
        }

        .social-icon:nth-child(6) {
            top: 140px;
            left: -20px;
            animation-delay: 1.6s;
        }

        .social-icon:nth-child(7) {
            top: 150px;
            right: -10px;
            animation-delay: 2s;
        }

        /* ==== ORDER + RATING BOX ==== */
        .rating-box {
            position: absolute;
            bottom: 40px;
            /* nâng lên chút cho gần hình */
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            gap: 25px;
            background: #fff;
            color: #222;
            border-radius: 50px;
            padding: 10px 28px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
            font-size: 15px;
            font-weight: 600;
            z-index: 3;
        }

        .rating-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .icon-circle {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: #d1fae5;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .text-green-600 {
            stroke: #16a34a;
        }

        /* ==== RESPONSIVE ==== */
        @media (max-width: 900px) {
            header {
                padding: 20px;
            }

            .hero {
                flex-direction: column;
                text-align: center;
                padding: 40px 20px;
            }

            .hero-image {
                margin-top: 40px;
            }

            .rating-box {
                bottom: -25px;
                flex-direction: column;
                gap: 10px;
            }
        }



        /* RESPONSIVE */
        @media (max-width: 900px) {
            header {
                padding: 20px;
            }

            .hero {
                flex-direction: column;
                text-align: center;
                padding: 40px 20px;
            }

            .hero-image {
                margin-top: 40px;
            }
        }

        .why-us {
            text-align: center;
            padding: 80px 20px;
            background: #f9fafb;
        }

        .why-us h2 {
            font-size: 32px;
            font-weight: 700;
            color: #111;
            margin-bottom: 10px;
        }

        .why-us h2 span {
            color: #e91e63;
        }

        .why-us p {
            color: #666;
            margin-bottom: 40px;
            font-size: 15px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 25px;
            max-width: 1100px;
            margin: 0 auto;
        }

        .box {
            background: #fff;
            border-radius: 16px;
            padding: 30px 20px;
            text-align: center;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
            transition: 0.3s;
        }

        .box:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        }

        .icon {
            width: 55px;
            height: 55px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 22px;
            margin: 0 auto 15px;
        }

        .icon.green {
            background: #22c55e;
        }

        .icon.pink {
            background: #ec4899;
        }

        .icon.yellow {
            background: #fbbf24;
        }

        .icon.orange {
            background: #fb923c;
        }

        .icon.purple {
            background: #8b5cf6;
        }

        .icon.violet {
            background: #a855f7;
        }

        .box h3 {
            font-size: 17px;
            font-weight: 600;
            color: #111;
            margin-bottom: 6px;
        }

        .box p {
            font-size: 14px;
            color: #666;
            line-height: 1.4;
            margin: 0;
        }


        /* === GUIDE SECTION === */
        .guide-section {
            background: #fff;
            padding: 100px 20px;
            text-align: center;
        }

        .guide-section .container {
            max-width: 1100px;
            margin: auto;
        }

        .guide-section h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #111;
        }

        .guide-section h2 span {
            color: #e91e63;
        }

        .guide-section p {
            color: #777;
            margin-top: 10px;
            margin-bottom: 30px;
            font-size: 15px;
        }

        .guide-section .btn {
            display: inline-block;
            background: #e91e63;
            color: #fff;
            padding: 12px 30px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .guide-section .btn:hover {
            background: #c2185b;
            transform: translateY(-3px);
        }

        .steps {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 25px;
            margin-top: 50px;
        }

        .step {
            background: #f9f9f9;
            padding: 30px 20px;
            border-radius: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.04);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .step:hover {
            background: #fff;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            transform: translateY(-5px);
        }

        .step .icon {
            font-size: 32px;
            background: #ffe4ef;
            color: #e91e63;
            width: 60px;
            height: 60px;
            margin: auto;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .step h3 {
            font-size: 17px;
            margin: 15px 0 10px;
            font-weight: 600;
        }

        .step p {
            font-size: 14px;
            color: #666;
            line-height: 1.6;
        }




        .testimonials h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #000;
            /* 👈 Thêm dòng này để chữ màu đen */
        }

        /* === TESTIMONIALS === */
        .testimonials {
            background: #fafafa;
            padding: 100px 20px;
            text-align: center;
        }

        .testimonials .container {
            max-width: 1100px;
            margin: auto;
        }

        .testimonials h2 {
            font-size: 2rem;
            font-weight: 700;
        }

        .testimonials h2 span {
            color: #e91e63;
        }

        .testimonials p {
            color: #777;
            font-size: 15px;
            margin-top: 10px;
            margin-bottom: 40px;
        }

        .reviews {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }

        .review {
            background: auto;
            border-radius: 20px;
            padding: 25px;
            display: flex;
            align-items: center;
            gap: 15px;
            text-align: left;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .review:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }

        .avatar {
            width: 100px;
            height: 50px;
            background: auto;
            color: #e91e63;
            font-weight: 600;
            font-size: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .review h4 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            color: #000;
            font-weight: 600;
        }

        .review p {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }

        .testimonial-card h4 {
            font-size: 1rem;
            font-weight: 600;
            color: #000;
            /* 👈 thêm dòng này để đổi màu chữ thành đen */
            margin-bottom: 0.5rem;
        }



        /* === PAYMENT GATEWAY SECTION === */
        .payment-section {
            background: #fff;
            padding: 100px 20px;
            position: relative;
            overflow: hidden;
        }

        .payment-section .container {
            max-width: 1100px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            align-items: center;
            gap: 50px;
        }

        .payment-section img {
            width: 100%;
            max-width: 420px;
            height: auto;
            object-fit: contain;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }

        .payment-section img:hover {
            transform: scale(1.03);
        }

        .payment-section h1 {
            font-size: 2.2rem;
            font-weight: 700;
            color: #111;
            line-height: 1.4;
            margin-bottom: 15px;
        }

        .payment-section h1 span {
            color: #e91e63;
        }

        .payment-section p {
            color: #555;
            font-size: 15px;
            line-height: 1.7;
            max-width: 520px;
        }

        /* RESPONSIVE */
        @media (max-width: 900px) {
            .payment-section .container {
                grid-template-columns: 1fr;
                text-align: center;
                gap: 40px;
            }

            .payment-section img {
                margin: 0 auto;
            }

            .payment-section p {
                margin: 0 auto;
            }
        }

        /* Font Awesome */
        @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css');


        /* Header */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 25px;
        }

        .logo {
            font-weight: 700;
            font-size: 1.2rem;
        }

        nav a {
            margin-right: 20px;
            text-decoration: none;
            color: #fff;
            font-weight: 500;
        }

        .btn-register {
            background: #ff0077;
            border: none;
            padding: 8px 18px;
            border-radius: 20px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-register:hover {
            opacity: 0.9;
        }

        /* Menu toggle icon */
        .menu-toggle {
            display: none;
            font-size: 26px;
            cursor: pointer;
        }

        /* Overlay nền mờ */
        .overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            backdrop-filter: blur(6px);
            opacity: 0;
            visibility: hidden;
            transition: 0.3s ease;
            z-index: 90;
        }

        .overlay.active {
            opacity: 1;
            visibility: visible;
        }

        /* Hộp menu mobile */
        .mobile-menu {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #fff;
            color: #111;
            border-top-left-radius: 24px;
            border-top-right-radius: 24px;
            box-shadow: 0 -4px 25px rgba(0, 0, 0, 0.25);
            transform: translateY(100%);
            transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            z-index: 100;

            /* ✅ fix chiều cao để thấy nút dưới cùng */
            max-height: 85vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow-y: auto;
            padding: 25px 20px 35px;
            text-align: center;
        }

        /* Khi bật menu */
        .mobile-menu.active {
            transform: translateY(0);
        }

        /* Header trong menu */
        .mobile-menu header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        /* Link menu */
        .mobile-menu nav {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 15px;
        }

        .mobile-menu nav a {
            color: #444;
            font-weight: 500;
            font-size: 16px;
            text-decoration: none;
            transition: 0.2s;
        }

        .mobile-menu nav a.active {
            color: #ff0077;
            font-weight: 700;
        }

        /* Hai nút Login / Register */
        .mobile-buttons {
            margin-top: 25px;
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .mobile-buttons button {
            border-radius: 25px;
            padding: 10px 28px;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-login {
            border: 2px solid #ff0077;
            color: #ff0077;
            background: transparent;
        }

        .btn-login:hover {
            background: #ff0077;
            color: #fff;
        }

        .btn-register {
            background: #ff0077;
            color: #fff;
            border: 2px solid #ff0077;
        }

        .btn-register:hover {
            opacity: 0.9;
        }

        /* Nền mờ khi mở menu */
        .overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            backdrop-filter: blur(6px);
            opacity: 0;
            visibility: hidden;
            transition: 0.3s ease;
            z-index: 90;
        }

        .overlay.active {
            opacity: 1;
            visibility: visible;
        }

        /* Menu mobile */
        .mobile-menu {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            background: #fff;
            border-top-left-radius: 28px;
            border-top-right-radius: 28px;
            box-shadow: 0 -6px 30px rgba(0, 0, 0, 0.25);
            transform: translateY(105%);
            transition: transform 0.45s cubic-bezier(0.34, 1.56, 0.64, 1);
            z-index: 100;

            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 25px 20px 50px;
            text-align: center;
            max-height: 85vh;
            overflow-y: auto;
        }

        .mobile-menu.active {
            transform: translateY(0);
            animation: slideUpBounce 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        /* Header trong menu */
        .mobile-menu header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        /* Nav links */
        .mobile-menu nav {
            display: flex;
            flex-direction: column;
            gap: 18px;
            margin-top: 10px;
        }

        .mobile-menu nav a {
            font-size: 16px;
            color: #555;
            font-weight: 500;
            text-decoration: none;
            transition: 0.2s;
        }

        .mobile-menu nav a.active {
            color: #ff0077;
            font-weight: 700;
        }

        /* Nút đăng nhập / đăng ký */
        .mobile-buttons {
            margin-top: 25px;
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
            padding-bottom: 30px;
            /* ✅ khoảng trắng dưới cùng */
        }

        .mobile-buttons button {
            border-radius: 25px;
            padding: 10px 28px;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-login {
            border: 2px solid #ff0077;
            color: #ff0077;
            background: transparent;
        }

        .btn-login:hover {
            background: #ff0077;
            color: #fff;
        }

        .btn-register {
            background: #ff0077;
            color: #fff;
            border: 2px solid #ff0077;
        }

        .btn-register:hover {
            opacity: 0.9;
        }

        /* 👇 hiệu ứng bật lên nhẹ */
        @keyframes slideUpBounce {
            0% {
                transform: translateY(100%);
            }

            70% {
                transform: translateY(-10%);
            }

            100% {
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            header nav {
                display: none;
            }

            .menu-toggle {
                display: block;
            }
        }

        .top-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: rgba(6, 95, 70, 0.2);
            /* xanh nhạt trong suốt */
            color: #34d399;
            /* xanh ngọc nhạt */
            border-radius: 9999px;
            padding: 6px 12px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .top-badge .icon {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #34d399;
            border-radius: 50%;
            height: 18px;
            width: 18px;
        }

        .top-badge .icon svg {
            color: #064e3b;
            width: 10px;
            height: 10px;
        }

        .top-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: rgba(6, 95, 70, 0.2);
            /* xanh nhạt */
            color: #34d399;
            /* chữ xanh */
            border-radius: 9999px;
            padding: 6px 12px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .top-badge .icon {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #34d399;
            border-radius: 50%;
            height: 18px;
            width: 18px;
            color: #064e3b;
            font-weight: 700;
            font-size: 12px;
        }
        .logo img {
  height: 60px;            /* chiều cao logo */
  width: auto;             /* tự co theo tỉ lệ */
  object-fit: contain;     /* đảm bảo không méo */
  transition: transform 0.3s ease;
}

/* Hiệu ứng hover nhẹ */
.logo img:hover {
  transform: scale(1.05);
}



    </style>
    <style>


/* Container chính */
#login {
  width: 100%;
  padding: 40px 16px;
  position: relative;
  z-index: 10;
  box-sizing: border-box;
}

/* Khung form */
#login .form-box {
  backdrop-filter: blur(8px);
  border: 1px solid rgba(128, 0, 128, 0.5);
  background: rgba(88, 28, 135, 0.3);
  border-radius: 16px;
  padding: 32px;
  max-width: 700px;
  margin: 0 auto;
  box-shadow: 0 0 28px rgba(234, 37, 109, 0.35);
}

/* Logo + tiêu đề */
#login .text-center {
  text-align: center;
  margin-bottom: 24px;
}

#login .text-center img {
  width: 90px;
  height: auto;
  margin-bottom: 12px;
}

#login .text-center h2 {
  color: #fff;
  font-size: 24px;
  margin-bottom: 8px;
}

#login .text-center h3 {
  color: rgba(255, 255, 255, 0.8);
  font-size: 14px;
  font-weight: 400;
}

/* Form */
#login form {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

/* Grid chia 3 phần (username, password, login) */
#login .form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr 100px;
  gap: 16px;
}

@media (max-width: 768px) {
  #login .form-grid {
    grid-template-columns: 1fr;
  }
}

/* Input */
#login input[type="text"],
#login input[type="password"] {
  width: 100%;
  border: 1px solid rgba(128, 0, 128, 0.5);
  background: rgba(47, 13, 81, 0.7);
  border-radius: 8px;
  padding: 10px 14px;
  color: white;
  outline: none;
  transition: 0.3s;
}

#login input::placeholder {
  color: rgba(255, 255, 255, 0.5);
}

#login input:focus {
  border-color: #ea256d;
  box-shadow: 0 0 0 2px rgba(234, 37, 109, 0.3);
}

/* Nút ẩn/hiện password */
.password-wrapper {
  position: relative;
}

.password-wrapper span {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  cursor: pointer;
  color: rgba(255, 255, 255, 0.6);
  font-size: 18px;
}

/* Nút Login */
#login button[type="submit"] {
  background: #ea256d;
  border: none;
  color: white;
  font-weight: 500;
  border-radius: 8px;
  padding: 10px 24px;
  cursor: pointer;
  transition: background 0.3s;
}

#login button[type="submit"]:hover {
  background: #ff4582;
}

/* Remember + Forgot + Register */
#login .options {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 10px;
}

#login .options label,
#login .options span {
  color: rgba(255, 255, 255, 0.7);
  font-size: 14px;
}

#login .options a {
  color: #ea256d;
  text-decoration: none;
}

#login .options a:hover {
  text-decoration: underline;
}
</style>
</head>

<body>

    <!-- HEADER -->
    <header>
        <div class="logo">  
            <img src="{{ asset(s('logo')) }}" alt="Smm Panel" title="">
        </div>
        <nav>
            <a href="/new">Home</a>
            <a href="/services">Service</a>
            <a href="/apidoc">API</a>
            <a href="/auth/register" class="btn-register">Register</a>
        </nav>
        <div class="menu-toggle" id="menu-toggle">☰</div>
    </header>

    <!-- MOBILE MENU -->
    <div class="overlay" id="overlay"></div>

    <div class="mobile-menu" id="mobile-menu">
        <header>
            <h3>Menu</h3>
            <span id="close-menu" style="font-size:24px;cursor:pointer;">✕</span>
        </header>

        <nav>
            <a href="/new" class="active">Home</a>
            <a href="/services">Service</a>
            <a href="/apidoc">API</a>
        </nav>

        <div class="mobile-buttons">
        <button class="btn-login">Login</button>
            <a href="/auth/register" class="btn-register">Register</a>
        </div>
    </div>


    <!-- HERO SECTION -->
    <section class="hero">
        <div class="hero-text">
            <div
                class="top-badge inline-flex items-center gap-2 rounded-full bg-green-900/30 px-3 py-1.5 text-sm text-green-400">
                <div class="icon flex h-4 w-4 items-center justify-center rounded-full bg-green-400">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-check h-3 w-3 text-green-900">
                        <path d="M20 6 9 17l-5-5"></path>
                    </svg>
                </div>
                <span>Top In Every Market</span>
            </div>
            <h1><span>Supreme SMM</span> Service<br>Reseller Panel</h1>
            <p>Boost Your Online Presence Effortlessly! Instantly Gain
                Followers, Likes, Views, Website Traffic, And More — All In
                Just A Few Clicks.</p>
            <a href="/new" class="btn">Go To Dashboard →</a>
        </div>

        <div class="hero-image">
            <img src="https://minsmm.com/layer-image.png" alt="Person" class="main">

            <div class="social-icon"><img src="https://cdn-icons-png.flaticon.com/512/733/733547.png" alt="Facebook">
            </div>
            <div class="social-icon"><img src="https://cdn-icons-png.flaticon.com/512/733/733558.png" alt="Instagram">
            </div>
            <div class="social-icon"><img src="https://cdn-icons-png.flaticon.com/512/1384/1384060.png" alt="YouTube">
            </div>
            <div class="social-icon"><img src="https://cdn-icons-png.flaticon.com/512/1384/1384017.png" alt="TikTok">
            </div>
            <div class="social-icon"><img src="https://cdn-icons-png.flaticon.com/512/733/733579.png" alt="Telegram">
            </div>
            <div class="social-icon"><img src="https://cdn-icons-png.flaticon.com/512/1384/1384055.png" alt="Snapchat">
            </div>

            <div class="rating-box">
                <div class="rating-item">
                    <div class="icon-circle">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="text-green-600">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                    </div>
                    <span>10000+ Order</span>
                </div>

                <div class="rating-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-star">
                        <path
                            d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"
                            fill="#FFD43B" stroke="#FFD43B"></path>
                    </svg>
                    <span>9.8/10 Rating</span>
                </div>
            </div>
    </section>

    @if (!Auth::check())
<section id="login">
  <div class="form-box">
    <div class="text-center">
      <h2>Welcome Back</h2>
      <h3>Please enter your login credentials below to access your account.</h3>
    </div>

    <form id="form-login" method="POST" action="{{ route('auth.login.post') }}">
      @csrf
      <div class="form-grid">
        <div>
          <input type="text" name="username" placeholder="Username" value="{{ old('username') }}" required>
        </div>

        <div class="password-wrapper">
          <input id="password" name="password" type="password" placeholder="Password" required>
          <span id="togglePassword"></span>
        </div>

        <div class="flex items-center justify-center">
          <button type="submit" id="login-btn">Login</button>
        </div>
      </div>

      <div class="options">
        <div>
          <input type="checkbox" id="remember" name="remember">
          <label for="remember">Remember me</label>
        </div>

        <div>
          <span>Don’t have an account?</span>
          <a href="/auth/register">Register</a>
        </div>
      </div>
    </form>
  </div>

</section>
@else

@endif

    <section class="why-us">
        <h2>Why Order <span>SMM Services</span> From Us?</h2>
        <p>Let Us Help You Build Your Online Presence Quickly And
            Efficiently.</p>

        <div class="grid">
            <div class="box">
                <div class="icon green"><i class="fas fa-crown"></i></div>
                <h3>High quality</h3>
                <p>The highest quality SMM services to meet your
                    needs.</p>
            </div>

            <div class="box">
                <div class="icon pink"><i class="fas fa-tags"></i></div>
                <h3>Incredible prices</h3>
                <p>SMM services on our panel are unbelievably cheap.</p>
            </div>

            <div class="box">
                <div class="icon yellow"><i class="fas fa-coins"></i></div>
                <h3>Numerous payment options</h3>
                <p>The highest quality SMM services to meet your
                    needs.</p>
            </div>

            <div class="box">
                <div class="icon orange"><i class="fas fa-truck-fast"></i></div>
                <h3>Fast delivery</h3>
                <p>SMM services on our panel are unbelievably cheap.</p>
            </div>

            <div class="box">
                <div class="icon purple"><i class="fas fa-code"></i></div>
                <h3>API support</h3>
                <p>We provide API support for easy service
                    reselling.</p>
            </div>

            <div class="box">
                <div class="icon violet"><i class="fas fa-headset"></i></div>
                <h3>Support 24/7</h3>
                <p>Technical support for all our services 24/7 to help
                    you.</p>
            </div>
        </div>
    </section>
    <section class="guide-section">
        <div class="container">
            <h2>Your Ultimate Guide To <span>Getting Started</span></h2>
            <p>Give Your Business A New Height Following These 4 Easy
                Steps.</p>
            <a href="/new" class="btn">Sign Up Now →</a>

            <div class="steps">
                <div class="step">
                    <div class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 34 34" fill="none">
                            <path
                                d="M6.99997 9.5C6.99997 7.76942 7.51315 6.0777 8.47461 4.63876C9.43607 3.19983 10.8026 2.07833 12.4015 1.41606C14.0003 0.753791 15.7597 0.580512 17.457 0.918133C19.1543 1.25575 20.7134 2.08911 21.9372 3.31282C23.1609 4.53653 23.9942 6.09563 24.3318 7.79296C24.6695 9.4903 24.4962 11.2496 23.8339 12.8485C23.1716 14.4473 22.0501 15.8139 20.6112 16.7754C19.1723 17.7368 17.4806 18.25 15.75 18.25C13.4307 18.2456 11.2076 17.3223 9.56765 15.6823C7.92766 14.0423 7.00438 11.8193 6.99997 9.5ZM33.6666 25.3333C33.6666 26.8991 33.2023 28.4297 32.3324 29.7316C31.4625 31.0335 30.2261 32.0482 28.7795 32.6474C27.333 33.2466 25.7412 33.4034 24.2055 33.0979C22.6698 32.7924 21.2592 32.0384 20.152 30.9313C19.0449 29.8241 18.2909 28.4135 17.9854 26.8778C17.68 25.3421 17.8367 23.7503 18.4359 22.3038C19.0351 20.8572 20.0498 19.6208 21.3517 18.7509C22.6536 17.881 24.1842 17.4167 25.75 17.4167C26.7896 17.4167 27.8191 17.6214 28.7795 18.0193C29.74 18.4171 30.6128 19.0003 31.3479 19.7354C32.083 20.4705 32.6662 21.3433 33.064 22.3038C33.4619 23.2643 33.6666 24.2937 33.6666 25.3333ZM29.3833 25.3333C29.3833 25.0018 29.2516 24.6839 29.0172 24.4495C28.7828 24.215 28.4648 24.0833 28.1333 24.0833H26.8833V22.8333C26.8833 22.5018 26.7516 22.1839 26.5172 21.9495C26.2828 21.715 25.9648 21.5833 25.6333 21.5833C25.3018 21.5833 24.9838 21.715 24.7494 21.9495C24.515 22.1839 24.3833 22.5018 24.3833 22.8333V24.0833H23.1333C22.8018 24.0833 22.4838 24.215 22.2494 24.4495C22.015 24.6839 21.8833 25.0018 21.8833 25.3333C21.8833 25.6649 22.015 25.9828 22.2494 26.2172C22.4838 26.4516 22.8018 26.5833 23.1333 26.5833H24.3833V27.8333C24.3833 28.1649 24.515 28.4828 24.7494 28.7172C24.9838 28.9516 25.3018 29.0833 25.6333 29.0833C25.9648 29.0833 26.2828 28.9516 26.5172 28.7172C26.7516 28.4828 26.8833 28.1649 26.8833 27.8333V26.5833H28.1333C28.4619 26.579 28.7756 26.4453 29.0064 26.2114C29.2373 25.9774 29.3667 25.662 29.3666 25.3333H29.3833ZM17.1 19.5333C16.987 19.3902 16.8425 19.2751 16.6777 19.1971C16.5129 19.119 16.3323 19.0801 16.15 19.0833H11.7833C9.29069 19.0935 6.86846 19.911 4.87941 21.4133C2.89036 22.9156 1.44155 25.0219 0.749971 27.4167C0.560123 28.0988 0.530311 28.8156 0.66286 29.5111C0.795408 30.2067 1.08674 30.8623 1.51416 31.4268C1.94157 31.9913 2.49354 32.4495 3.12707 32.7657C3.76059 33.082 4.45857 33.2477 5.16664 33.25H17.2C17.3747 33.2513 17.5479 33.2165 17.7085 33.1476C17.8691 33.0788 18.0137 32.9775 18.1333 32.85C18.1946 32.7706 18.2503 32.6871 18.3 32.6C16.621 30.8967 15.5826 28.6651 15.3608 26.2837C15.1391 23.9023 15.7476 21.5174 17.0833 19.5333H17.1Z"
                                fill="#EA1261"></path>
                        </svg>
                    </div>
                    <h3>Create An Account</h3>
                    <p>Start With Registering On Our Panel And Logging
                        In.</p>
                </div>
                <div class="step">
                    <div class="icon">
                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M20 2C10.0593 2 2 10.0593 2 20C2 29.9407 10.0593 38 20 38C29.9407 38 38 29.9407 38 20C38 10.0593 29.9407 2 20 2ZM25.1704 27.0963C24.3852 28.0519 23.363 28.6963 22.1704 29.0222C21.6519 29.163 21.4148 29.437 21.4444 29.9778C21.4667 30.5111 21.4444 31.037 21.437 31.5704C21.437 32.0444 21.1926 32.2963 20.7259 32.3111C20.4222 32.3185 20.1185 32.3259 19.8148 32.3259C19.5481 32.3259 19.2815 32.3259 19.0148 32.3185C18.5111 32.3111 18.2741 32.0222 18.2741 31.5333C18.2667 31.1482 18.2667 30.7556 18.2667 30.3704C18.2593 29.5111 18.2296 29.4815 17.4074 29.3482C16.3556 29.1778 15.3185 28.9407 14.3556 28.4741C13.6 28.1037 13.5185 27.9185 13.7333 27.1185C13.8963 26.5259 14.0593 25.9333 14.2444 25.3482C14.3778 24.9185 14.5037 24.7259 14.7333 24.7259C14.8667 24.7259 15.037 24.7926 15.2667 24.9111C16.3333 25.4667 17.4667 25.7778 18.6593 25.9259C18.8593 25.9482 19.0593 25.963 19.2593 25.963C19.8148 25.963 20.3556 25.8593 20.8815 25.6296C22.2074 25.0519 22.4148 23.5185 21.2963 22.6C20.9185 22.2889 20.4815 22.0593 20.0296 21.8593C18.8667 21.3482 17.6593 20.963 16.563 20.3037C14.7852 19.237 13.6593 17.7778 13.7926 15.6148C13.9407 13.1704 15.3259 11.6444 17.5704 10.8296C18.4963 10.4963 18.5037 10.5037 18.5037 9.54074C18.5037 9.21482 18.4963 8.88889 18.5111 8.55556C18.5333 7.82963 18.6519 7.7037 19.3778 7.68148H20.2222C21.6 7.68148 21.6 7.74074 21.6074 9.22963C21.6148 10.3259 21.6148 10.3259 22.7037 10.4963C23.5407 10.6296 24.3333 10.8741 25.1037 11.2148C25.5259 11.4 25.6889 11.6963 25.5556 12.1481C25.363 12.8148 25.1778 13.4889 24.9704 14.1481C24.837 14.5481 24.7111 14.7333 24.4741 14.7333C24.3407 14.7333 24.1778 14.6815 23.9704 14.5778C22.9037 14.0593 21.7852 13.8074 20.6148 13.8074C20.4667 13.8074 20.3111 13.8148 20.163 13.8222C19.8148 13.8444 19.4741 13.8889 19.1481 14.0296C17.9926 14.5333 17.8074 15.8074 18.7926 16.5926C19.2889 16.9926 19.8593 17.2741 20.4444 17.5185C21.4667 17.9407 22.4889 18.3482 23.4593 18.8815C26.5111 20.5852 27.3407 24.4593 25.1704 27.0963Z"
                                fill="#EA1261"></path>
                        </svg>
                    </div>
                    <h3>Make A Deposit</h3>
                    <p>Pick The Most Suitable Payment Option & Add Funds
                        To Your Account.</p>
                </div>
                <div class="step">
                    <div class="icon"><svg width="40" height="40" viewBox="0 0 40 40" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M35.5403 23.2708C34.3731 22.5983 33.6481 21.3457 33.6481 20.0001C33.6481 18.6552 34.373 17.4025 35.5369 16.7307C36.7314 16.044 37.2282 14.6171 36.7177 13.3382C36.0779 11.7227 35.2068 10.2138 34.1348 8.85287C33.2806 7.7693 31.7969 7.48552 30.6024 8.17668C29.4386 8.85287 27.9888 8.85491 26.8216 8.18176C25.6577 7.50862 24.9363 6.25389 24.9397 4.90593C24.9397 3.52696 23.9528 2.38254 22.5882 2.18517C20.8705 1.93659 19.1259 1.9383 17.4087 2.19025C16.0474 2.389 15.0606 3.53277 15.0639 4.90898C15.0639 6.25527 14.3424 7.50927 13.1786 8.18176C12.0113 8.85418 10.565 8.8528 9.3978 8.17733C8.20335 7.48618 6.7196 7.77134 5.86542 8.85491C5.33051 9.53492 4.84491 10.2523 4.41231 11.0016C3.9801 11.7499 3.60235 12.5343 3.28248 13.3317C2.76858 14.6126 3.26549 16.0415 4.45994 16.73C5.62724 17.4018 6.35209 18.6551 6.35209 20C6.35209 21.3449 5.62724 22.5973 4.46336 23.27C3.2689 23.9568 2.77207 25.383 3.28248 26.6616C3.92121 28.2742 4.79131 29.7852 5.86542 31.1472C6.7196 32.2307 8.20335 32.5145 9.3978 31.8234C10.5617 31.1472 12.0114 31.1458 13.1786 31.8183C14.3424 32.4914 15.0639 33.7461 15.0639 35.0941C15.0605 36.4727 16.0508 37.6171 17.412 37.8149C18.2629 37.9381 19.1215 38 19.9813 38C20.8525 38 21.7237 37.9367 22.5915 37.8098C23.9527 37.6107 24.9396 36.4679 24.9396 35.0917C24.9362 33.7448 25.6576 32.4907 26.8215 31.8183C27.9887 31.1459 29.4351 31.1472 30.6023 31.8227C31.7967 32.5139 33.2805 32.2294 34.1347 31.1451C34.6693 30.4651 35.1548 29.748 35.5878 28.9991C36.0166 28.2508 36.4011 27.4664 36.7176 26.6684C37.2316 25.3874 36.7348 23.9581 35.5403 23.2708ZM24.63 22.6739C23.9187 23.9106 22.7651 24.7957 21.3835 25.1653C20.0053 25.5348 18.5623 25.3453 17.327 24.6306C14.7747 23.1564 13.8967 19.8803 15.3702 17.3262C16.3571 15.6131 18.1608 14.6541 20.0086 14.6541C20.9172 14.6541 21.8327 14.8842 22.6732 15.3695C25.2255 16.8433 26.1035 20.1205 24.63 22.6739Z"
                                fill="#EA1261"></path>
                        </svg></div>
                    <h3>Choose A Service</h3>
                    <p>See The List Of Our SMM Services And Place Your
                        Orders On Our Panel.</p>
                </div>
                <div class="step">
                    <div class="icon"><svg width="40" height="40" viewBox="0 0 40 40" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M4.6246 36.5006H8.37474C9.82229 36.5006 10.9998 35.323 10.9998 33.8755V16.6249C10.9998 15.1773 9.82229 13.9998 8.37474 13.9998H4.6246C3.17705 13.9998 1.99951 15.1773 1.99951 16.6249V33.8755C1.99951 35.323 3.17705 36.5006 4.6246 36.5006ZM21.1717 3.12439C19.6716 3.12439 18.9216 3.87442 18.9216 7.62455C18.9216 11.1887 15.47 14.0568 13.2499 15.5343V34.117C15.6515 35.2285 20.4592 36.8756 27.9219 36.8756H30.322C33.2471 36.8756 35.7372 34.7755 36.2322 31.8954L37.9123 22.1451C38.0614 21.2836 38.0204 20.4 37.7921 19.5561C37.5639 18.7122 37.1539 17.9284 36.5908 17.2596C36.0278 16.5908 35.3253 16.0532 34.5327 15.6845C33.74 15.3157 32.8763 15.1247 32.0021 15.1248H24.9218C24.9218 15.1248 26.0469 12.8747 26.0469 9.1246C26.0469 4.62444 22.6717 3.12439 21.1717 3.12439Z"
                                fill="#EA1261"></path>
                        </svg></div>
                    <h3>Enjoy Fast Results</h3>
                    <p>Once Your Order Is Complete, Enjoy The Growth Of
                        Your Social Media Account.</p>
                </div>
            </div>
        </div>
    </section>
    <section class="testimonials">
        <div class="container">
            <h2>What Our Customers <span>Say About Us</span></h2>
            <p>Give Your Business A New Height Following These 4 Easy
                Steps.</p>

            <div class="reviews">
                <div class="review">
                    <div class="avatar">J</div>
                    <div>
                        <h4>Jane Cooper</h4>
                        <p>It is a long established fact that a reader
                            will be distracted by the readable content
                            of a page.</p>
                    </div>
                </div>

                <div class="review">
                    <div class="avatar">S</div>
                    <div>
                        <h4>Savannah Nguyen</h4>
                        <p>The point of using Lorem Ipsum is that it has
                            a more-or-less normal distribution of
                            letters.</p>
                    </div>
                </div>

                <div class="review">
                    <div class="avatar">B</div>
                    <div>
                        <h4>Jerome Bell</h4>
                        <p>It is a long established fact that a reader
                            will be distracted by readable content when
                            looking at its layout.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="payment-section">
        <div class="container">
            <div class="image">
                <img class="hidden md:block" src="https://minsmm.com/payment_gatway.png" alt="Payment Gateway">

            </div>

            <div class="text">
                <h1>
                    We Have Most Popular <br>
                    Automatic <span>Payment Gateway</span> <br>
                    For Adding Funds.
                </h1>
                <p>
                    Get An Idea Of Our Numerous Payment Options From The Most Well-Known Providers.
                    Payments By Credit Card, Bitpay And Much More. Safety Is Very Important To Us!
                </p>
            </div>
        </div>
    </section>
    <section class="faq-section">
        <div class="faq-header">
            <img src="https://c.animaapp.com/rU1XU5Zx/img/happyasset-1-2x-3@2x.png" alt="icon" class="side-icon">
            <div class="text-center">
                <h2><span>FAQ</span> – Most Asked Question</h2>
                <p>Let Us Help You Build Your Online Presence Quickly And Efficiently.</p>
            </div>
            <img src="https://c.animaapp.com/rU1XU5Zx/img/happyasset-1-2x-3@2x.png" alt="icon" class="side-icon">
        </div>

        <div class="faq-list">
            <div class="faq-item">
                <button class="faq-question">
                    What Exactly Is An SMM Panel?
                    <span class="icon">+</span>
                </button>
                <div class="faq-answer">
                    <p>
                        An SMM Panel Is A Social Media Marketing Panel That Provides Various
                        Services To Help You Grow Your Social Media Presence.
                    </p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    What Types Of SMM Services Do You Sell On Your Panel?
                    <span class="icon">+</span>
                </button>
                <div class="faq-answer">
                    <p>
                        We Offer A Wide Range Of SMM Services, Including Likes, Followers, Comments, Views, And More For
                        Various Platforms.
                    </p>
                </div>
            </div>

            <div class="faq-item active">
                <button class="faq-question">
                    Is It Safe To Buy SMM Services On This Panel?
                    <span class="icon">−</span>
                </button>
                <div class="faq-answer">
                    <p>Yes, It Is Completely Safe To Buy SMM Services On Our Panel.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    What Does "Mass Order" Mean?
                    <span class="icon">+</span>
                </button>
                <div class="faq-answer">
                    <p>Mass Order Refers To Placing A Large Number Of Orders At Once.</p>
                </div>
            </div>
        </div>
    </section>

    <style>
        /* === FAQ SECTION === */
        /* === FAQ SECTION === */
        .faq-section {
            background-color: #0f172a;
            /* slate-900 */
            color: #fff;
            padding: 80px 20px;
            text-align: center;
        }

        .faq-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .faq-header h2 {
            font-size: 2rem;
            font-weight: 700;
        }

        .faq-header h2 span {
            color: #ec4899;
            /* pink-500 */
        }

        .faq-header p {
            color: #94a3b8;
            font-size: 0.9rem;
            margin-top: 8px;
        }

        .faq-header .side-icon {
            width: 120px;
            height: auto;
            object-fit: contain;
        }

        /* GRID FAQ */
        .faq-list {
            margin: 60px auto 0;
            max-width: 900px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 24px;
            align-items: stretch;
        }

        /* CARD FAQ */
        .faq-item {
            background: #fff;
            color: #111;
            border-radius: 16px;
            padding: 20px 24px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            min-height: 180px;
            /* để tất cả cao bằng nhau */
        }

        .faq-question {
            width: 100%;
            text-align: left;
            font-weight: 600;
            font-size: 1rem;
            background: none;
            border: none;
            outline: none;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
        }

        .faq-question .icon {
            color: #ec4899;
            font-size: 1.5rem;
            transition: transform 0.3s ease;
        }

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            color: #555;
            transition: max-height 0.3s ease;
        }

        .faq-item.active .faq-answer {
            max-height: 200px;
            margin-top: 10px;
        }

        .faq-item.active .faq-question .icon {
            transform: rotate(180deg);
        }

        /* Hover effect */
        .faq-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 16px rgba(236, 72, 153, 0.25);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .faq-header {
                flex-direction: column;
            }

            .faq-header .side-icon {
                width: 80px;
            }

            .faq-list {
                grid-template-columns: 1fr;
                width: 90%;
            }

            .faq-item {
                min-height: auto;
            }
        }
    </style>
   
    <script>
        document.querySelectorAll(".faq-question").forEach((btn) => {
            btn.addEventListener("click", () => {
                const item = btn.parentElement;
                item.classList.toggle("active");
                const icon = btn.querySelector(".icon");
                icon.textContent = item.classList.contains("active") ? "−" : "+";
            });
        });
    </script>
    <script>
        const menuToggle = document.getElementById("menu-toggle");
        const mobileMenu = document.getElementById("mobile-menu");
        const closeMenu = document.getElementById("close-menu");
        const overlay = document.getElementById("overlay");

        menuToggle.onclick = () => {
            mobileMenu.classList.add("active");
            overlay.classList.add("active");
        };

        closeMenu.onclick = () => {
            mobileMenu.classList.remove("active");
            overlay.classList.remove("active");
        };

        overlay.onclick = () => {
            mobileMenu.classList.remove("active");
            overlay.classList.remove("active");
        };
    </script>

    <!--end::Global Javascript Bundle-->

    <script src="/assets/plugins/global/plugins.bundle.js"></script>
    <script src="/assets/js/scripts.bundle.js"></script>
    <!--end::Global Javascript Bundle-->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>

    @if (session('success'))
        <script>
            Swal.fire(
                'Thành công',
                '{{ session('success') }}',
                'success'
            )
        </script>
    @endif

    <script>
        const notyf = new Notyf({
            position: {
                x: 'right',
                y: 'top'
            },
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#form-login').on('submit', function(e) {
                e.preventDefault();

                //     $(this).find('button[type="submit"]').prop('disabled', true).html(`
            // <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            // Đang xử lý...`).addClass('disabled');

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    beforeSend: function() {
                        $(this).find('button[type="submit"]').prop('disabled', true).html(`
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Đang xử lý...`).addClass('disabled');
                        $.blockUI({
    message: `
        <div class="loader-box">
            <div class="loader-spinner"></div>
            <span>Loading...</span>
        </div>
    `,
    css: {
        border: 'none',
        backgroundColor: 'transparent',
        color: '#fff',
        width: '100%',
        left: '0',
        top: '40%',
        textAlign: 'center'
    },
    overlayCSS: {
        backgroundColor: 'rgba(0, 0, 0, 0.8)',
        opacity: 1,
        cursor: 'wait'
    }
});
                    },
                    complete: function() {
                        $(this).find('button[type="submit"]').prop('disabled', false).html(
                            'Đăng nhập').removeClass('disabled');
                        $.unblockUI();
                    },
                    success: function(response) {
                        $.unblockUI();
                        if (response.status === true) {
                            if (response.two_factor_auth) {
                                $('#two-factor-auth').show();
                                notyf.error(response.message);
                            } else {
                                window.location.href = response.redirect;
                            }

                        } else {
                            Swal.close();
                            notyf.error(response.message ||
                                'Đã xảy ra lỗi. Vui lòng thử lại sau.');
                        }
                    },
                    error: function(xhr) {
                        notyf.error(xhr.responseJSON.message ||
                            'Đã xảy ra lỗi. Vui lòng thử lại sau.');
                    }
                });
            });
        });
    </script>
<script>
document.querySelector('.btn-login').addEventListener('click', function(e) {
    e.preventDefault(); // tránh reload trang
    const loginSection = document.getElementById('login');
    if (loginSection) {
        loginSection.scrollIntoView({ behavior: 'smooth' }); // cuộn mượt
    }
});
</script>
</body>

</html>
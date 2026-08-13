<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ s('title') }}</title>
    @include('components.seo-header')

    <link rel="canonical" href="{{ url()->current() }}">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: #0d0f1a;
      color: #fff;
    }
    section {
      padding: 60px 20px;
      max-width: 1200px;
      margin: auto;
    }
    .services {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
      gap: 30px;
    }
    .first-item {
      position: relative;
    }
    .first-card {
      background: #111429;
      border-radius: 20px;
      padding: 30px;
      overflow: hidden;
      position: relative;
      height: 100%;
      box-shadow: 0 0 25px rgba(0,0,0,0.5);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }
    .first-image {
      text-align: center;
      margin-bottom: 20px;
    }
    .first-image img {
      max-width: 100%;
      height: auto;
    }
    .first-card--body {
      flex: 1;
    }
    .first-content .label {
      font-size: 14px;
      color: #1da1f2;
      margin-bottom: 10px;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .first-content h3 {
      font-size: 22px;
      margin: 0 0 10px;
      font-weight: 600;
    }
    .first-content p {
      color: #ccc;
      line-height: 1.6;
      font-size: 15px;
    }
    /* Circle + shadow effect */
    .first-circle {
      position: absolute;
      width: 150px;
      height: 150px;
      background: rgba(29,161,242,0.1);
      border-radius: 50%;
      top: -50px;
      right: -50px;
    }
    .first-shadow {
      position: absolute;
      width: 200px;
      height: 200px;
      background: rgba(29,161,242,0.05);
      border-radius: 50%;
      bottom: -80px;
      left: -80px;
    }
  </style>
  <style>
    .social-slider {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 12px;
      margin: 40px 0;
    }

    .social-item {
      display: flex;
      align-items: center;
      gap: 6px;
      background: #111827;
      padding: 10px 16px;
      border-radius: 12px;
      color: #fff;
      font-size: 14px;
      transition: all .3s;
    }

    .social-item:hover {
      background: #1f2937;
      transform: translateY(-3px);
    }

    .first-cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin-top: 30px;
    }

    .first-card {
      background: #111827;
      border-radius: 16px;
      padding: 24px;
      text-align: center;
      transition: all .3s;
    }

    .first-card:hover {
      background: #1f2937;
      transform: translateY(-5px);
    }

    .feature-item {
      background: #111827;
      padding: 24px;
      border-radius: 12px;
      text-align: center;
      transition: all .3s;
    }

    .feature-item:hover {
      background: #1f2937;
    }


    /* Reset */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body[data-layout="guest"] {
  font-family: "Segoe UI", sans-serif;
  background: #0d0f1a;
  color: #fff;
}

/* Header */
.header {
  width: 100%;
  background: #0d0f1a;
  border-bottom: 1px solid rgba(255,255,255,0.08);
  position: sticky;
  top: 0;
  z-index: 100;
}

.header .container {
  max-width: 1200px;
  margin: auto;
  padding: 15px 20px;
}

.header-box {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

/* Logo */
.header-logo img {
  display: block;
  height: auto;
  max-height: 60px;
}

/* Menu */
.header-menu {
  display: flex;
  align-items: center;
  gap: 30px;
}

.header-menu ul {
  list-style: none;
  display: flex;
  gap: 25px;
}

.header-menu ul li a {
  text-decoration: none;
  color: #bbb;
  font-weight: 500;
  font-size: 15px;
  transition: color 0.3s ease;
}

.header-menu ul li.active a,
.header-menu ul li a:hover {
  color: #1da1f2;
}

/* Buttons */
.header-menu--buttons {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-left: 30px;
}

.btn {
  display: flex;
  align-items: center;
  gap: 8px;
  border-radius: 30px;
  font-size: 14px;
  font-weight: 500;
  padding: 8px 18px;
  cursor: pointer;
  text-decoration: none;
  transition: all 0.3s ease;
}

.btn-primary {
  background: #1da1f2;
  color: #fff;
}

.btn-primary:hover {
  background: #0d8ddc;
}

.btn-secondary {
  background: rgba(255,255,255,0.08);
  color: #fff;
}

.btn-secondary:hover {
  background: rgba(255,255,255,0.15);
}

/* Menu Toggle (Mobile) */
.header-menu--btn {
  display: none;
}

.header-menu--btn button {
  border: none;
  background: rgba(255,255,255,0.08);
  border-radius: 50%;
  padding: 10px 12px;
  color: #fff;
  cursor: pointer;
  font-size: 16px;
}

/* Responsive */
@media (max-width: 992px) {
  .header-menu {
    display: none; /* ẩn menu chính */
    position: absolute;
    top: 70px;
    left: 0;
    width: 100%;
    background: #111429;
    flex-direction: column;
    padding: 20px;
    gap: 20px;
  }

  .header-menu ul {
    flex-direction: column;
    gap: 15px;
  }

  .header-menu--buttons {
    flex-direction: column;
    margin-left: 0;
    width: 100%;
  }

  .header-menu--btn {
    display: block;
  }

  /* Khi active (toggle JS) thì hiện menu */
  .header-menu.active {
    display: flex;
  }
}
.label-main {
  text-align: center;
  margin-top: 30px;
}

.label-main--content .btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 14px 28px;
  font-size: 16px;
  font-weight: 600;
  border-radius: 40px;
  cursor: pointer;
  text-decoration: none;
  transition: all 0.3s ease;
}

/* Primary Button Style */
.btn-primary {
  background: linear-gradient(135deg, #1da1f2, #007bff);
  color: #fff;
  box-shadow: 0 6px 16px rgba(29, 161, 242, 0.4);
}

.btn-primary:hover {
  background: linear-gradient(135deg, #0d8ddc, #0066cc);
  box-shadow: 0 8px 20px rgba(13, 141, 220, 0.6);
  transform: translateY(-2px);
}

.btn-primary i {
  font-size: 14px;
  animation: spin 2s linear infinite;
}

/* Icon Snowflake quay */
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
  </style>
</head>

<body data-layout="guest">
    <div class="header">

    <div class="container">

        <div class="header-box">

            <a href="/" class="header-logo">

                <img src="{{ asset(s('logo')) }}" width="175px" alt="logo">

            </a>

            <div class="header-menu" id="menu">

                <div class="header-menu--mobile"></div>

                <ul>

                    <li class="active">

                        <a href="/auth/login">

                            Đăng Nhập

                        </a>

                    </li>

                    <li>

                        <a href="{{ route('services') }}">

                            Dịch Vụ

                        </a>

                    </li>

                    <li>

                        <a href="{{ route('apidoc') }}">

                            Tài Liệu API

                        </a>

                    </li>

                    <li>

                       

                    </li>

                </ul>

                <div class="header-menu--buttons">

                    <a href="/auth/login" class="btn btn-secondary">

                        Đăng Nhập

                        <div class="icon">

                            <i class="fas fa-sign-in-alt"></i>

                        </div>

                    </a>

                    <a href="/auth/register" class="btn btn-primary">

                        Đăng Ký

                        <div class="icon">

                            <i class="fas fa-user-plus"></i>

                        </div>

                    </a>

                </div>

            </div>

            <div class="header-menu--btn">

                <button class="btn btn-secondary" data-toggle="guest-menu">

                    <i class="far fa-bars"></i>

                </button>

            </div>

        </div>

    </div>

</div>

<main>

  <!-- Hero -->
  <section class="flex flex-col items-center text-center px-6 py-20">
    <span class="px-4 py-1 bg-gray-800 rounded-full text-sm">
      ⭐ Giải Pháp Quảng Cáo | Marketing
    </span>
    <h1 class="mt-6 text-3xl md:text-5xl font-extrabold leading-snug">
      Lựa chọn được đánh giá cao nhất cho <br />
      dịch vụ <span class="text-blue-500">tăng tương tác</span>, <br />
      xếp hạng số 1 tại Việt Nam 🔥
    </h1>
    <p class="mt-6 max-w-2xl text-gray-300">
      Cung cấp dịch vụ tăng Like, Share, Comment, Follow, Reactions… cho Facebook,
      Instagram, Tiktok, Youtube kèm giải pháp gia tăng doanh số kinh doanh.
    </p>
    <div class="label-main hero--label-main">
                        <div class="label-main--content">
                            <a href="/new" class="btn btn-primary"> Truy cập ngay <i class="fas fa-snowflake"></i> </a>
                        </div>
                    </div>
    <div class="flex items-center gap-3 mt-10">
      <img src="https://randomuser.me/api/portraits/men/1.jpg" class="w-10 h-10 rounded-full" />
      <img src="https://randomuser.me/api/portraits/women/2.jpg" class="w-10 h-10 rounded-full" />
      <img src="https://randomuser.me/api/portraits/men/3.jpg" class="w-10 h-10 rounded-full" />
      <p class="text-gray-300 text-sm">Được hơn <b>30000+</b> khách hàng tin dùng.</p>
    </div>
  </section>

  <!-- Social Slider -->
  <section>
    <div class="container mx-auto">
      <div class="social-slider">
        <div class="social-item"><span>Vimeo</span><i class="fab fa-vimeo-v"></i></div>
        <div class="social-item"><span>Twitch</span><i class="fab fa-twitch"></i></div>
        <div class="social-item"><span>Reddit</span><i class="fab fa-reddit"></i></div>
        <div class="social-item"><span>Telegram</span><i class="fab fa-telegram"></i></div>
        <div class="social-item"><span>TikTok</span><i class="fab fa-tiktok"></i></div>
        <div class="social-item"><span>Instagram</span><i class="fab fa-instagram"></i></div>
        <div class="social-item"><span>Twitter</span><i class="fab fa-twitter"></i></div>
        <div class="social-item"><span>Spotify</span><i class="fab fa-spotify"></i></div>
        <div class="social-item"><span>Discord</span><i class="fab fa-discord"></i></div>
        <div class="social-item"><span>Dailymotion</span><i class="fab fa-dailymotion"></i></div>
        <div class="social-item"><span>Tumblr</span><i class="fab fa-tumblr"></i></div>
        <div class="social-item"><span>YouTube</span><i class="fab fa-youtube"></i></div>
        <div class="social-item"><span>Pinterest</span><i class="fab fa-pinterest"></i></div>
      </div>
    </div>
  </section>

  <section>
    <div class="services">
      <!-- TikTok Card -->
      <div class="first-item first-item--4">
        <div class="first-card">
          <div class="first-circle"></div>
          <div class="first-shadow"></div>
          <div class="first-image">
            <img src="https://tuongtacsieure.com/storage/cloudfront/assets/images/main-img.svg9" 
                 alt="TikTok" width="350">
          </div>
          <div class="first-card--body">
            <div class="first-content">
              <div class="label">
                <span class="fab fa-tiktok"></span>
                Người theo dõi, lượt thích và nhiều hơn nữa
              </div>
              <h3 class="title">Nhận dạng trên TikTok</h3>
              <p class="text">
                Giúp khách hàng hoặc chính bạn tiếp cận lượng đối tượng rộng lớn và 
                hoàn toàn mới với bảng điều khiển SMM TikTok của chúng tôi.
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- YouTube Card -->
      <div class="first-item first-item--6">
        <div class="first-card">
          <div class="first-image">
            <img src="https://tuongtacsieure.com/storage/cloudfront/assets/images/main-img.svg7" 
                 alt="YouTube" width="375">
          </div>
          <div class="first-card--body">
            <div class="first-content">
              <div class="label">
                <i class="fab fa-youtube"></i>
                Người đăng ký, lượt thích và nhiều hơn nữa
              </div>
              <h3 class="title">Bảng điều khiển Youtube ổn định</h3>
              <p class="text">
                Tăng sự nổi tiếng cho các kênh của khách hàng. Tăng lượt xem, lượt thích, 
                người đăng ký, bình luận và chia sẻ video của họ. Tất cả đều có thể thực hiện 
                được với tài khoản YouTube của chúng tôi.
              </p>
            </div>
          </div>
        </div>
      </div>

    </div>
  </section>

  <!-- First Cards -->
  <section class="home-section section-first">
    <div class="container mx-auto px-6">
      <h2 class="text-center text-2xl md:text-4xl font-bold mb-10">
        Nhà cung cấp <span class="text-blue-500">tăng tương tác</span> tốt nhất thế giới
      </h2>

      <div class="first-cards">
        <!-- Card 1 -->
        <div class="first-card">
          <h3 class="title text-xl font-semibold mb-2">Hỗ Trợ Hơn 40 Nền Tảng</h3>
          <p class="text-gray-300">Chúng tôi có đội ngũ hỗ trợ tốt nhất sẽ giải quyết vấn đề của bạn trong vòng 24 giờ.</p>
        </div>

        <!-- Card 2 -->
        <div class="first-card">
          <h3 class="title text-xl font-semibold mb-2">Tiếp thị đầu tiên</h3>
          <p class="text-gray-300">Tăng tương tác trên phương tiện truyền thông xã hội với chúng tôi.</p>
        </div>

        <!-- Card 3 -->
        <div class="first-card">
          <h3 class="title text-3xl font-bold mb-2">3,507+</h3>
          <p class="text-gray-300">Khách hàng hài lòng</p>
        </div>

      </div>
    </div>
  </section>

  <!-- Features -->
  <section class="home-section section-features py-20">
    <div class="container mx-auto px-6">
      <h2 class="text-center text-2xl md:text-4xl font-bold mb-10">Tính năng của chúng tôi</h2>
      <div class="grid md:grid-cols-3 gap-6">
        <div class="feature-item">
          <h3 class="text-lg font-semibold mb-2">Đặt hàng nhanh chóng</h3>
          <p class="text-gray-300">Hỗ trợ doanh nghiệp phát triển mạnh mẽ trong lĩnh vực truyền thông xã hội.</p>
        </div>
        <div class="feature-item">
          <h3 class="text-lg font-semibold mb-2">Tài liệu API</h3>
          <p class="text-gray-300">Thư viện API tích hợp đầy đủ, dễ dàng triển khai dịch vụ.</p>
        </div>
        <div class="feature-item">
          <h3 class="text-lg font-semibold mb-2">Dịch vụ hàng tháng</h3>
          <p class="text-gray-300">Dịch vụ ổn định, mang lại trải nghiệm tốt nhất cho khách hàng.</p>
        </div>
        <div class="feature-item">
          <h3 class="text-lg font-semibold mb-2">Dịch vụ nhanh chóng</h3>
          <p class="text-gray-300">Tối ưu tốc độ triển khai và chất lượng dịch vụ.</p>
        </div>
        <div class="feature-item">
          <h3 class="text-lg font-semibold mb-2">Chương trình liên kết</h3>
          <p class="text-gray-300">Hỗ trợ đại lý và đối tác với chính sách hấp dẫn.</p>
        </div>
        <div class="feature-item">
          <h3 class="text-lg font-semibold mb-2">Trở thành đại lý</h3>
          <p class="text-gray-300">Gia nhập hệ thống và kinh doanh dịch vụ dễ dàng.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-gray-900 py-6 mt-10">
    <div class="container mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-4">
      <div class="text-gray-400 text-sm" id="copyright-text"></div>
      <div>
        <img src="https://tuongtacsieure.com/storage/cloudfront/assets/images/payment-logos.svg" alt="Payment Methods"
          class="h-8">
      </div>
    </div>
  </footer>

  <script>
    const domain = window.location.hostname.replace(/^www\./, '');
    document.getElementById('copyright-text').innerText =
      `Copyright © 2025. ${domain} - Social Media Marketing`;
  </script>
</body>
</html>
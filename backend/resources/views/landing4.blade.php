<!DOCTYPE html>
<html lang=vi>

<head>

    <title>{{ s('title') }}</title>
    @include('components.seo-header')

    <link rel="canonical" href="{{ url()->current() }}">
    <meta charset=UTF-8>
    <meta name=viewport content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
    <meta http-equiv=X-UA-Compatible content="ie=edge">
    <meta name=title content="SMMPANEL - Social Media Marketing">

    <style>
        .hero-content {
            margin-top: 70px !important;
        }

        .hero {
            min-height: 110vh !important;
            /* chiếm 90% chiều cao viewport */
            display: flex !important;
            align-items: center !important;
            /* canh dọc giữa */
            padding-top: 40px !important;
            /* vẫn giữ khoảng trên nếu cần */
            padding-bottom: 40px !important;
            /* khoảng dưới */
            box-sizing: border-box !important;
        }

        .hero .container {
            width: 100%;
        }

        /* Thu nhỏ tiêu đề chính, nhưng vẫn responsive */
        .hero .section-title h1 {
            font-size: 36px !important;
            /* chỉnh nhỏ hơn: 36px */
            line-height: 1.15 !important;
            margin: 0 0 12px 0 !important;
            max-width: 100% !important;
            word-break: break-word !important;
        }

        /* Phần span (SOCIAL MEDIA MARKETING) - giữ lớn hơn 1 chút nếu muốn */
        .hero .section-title h1 span {
            display: block;
            font-size: 44px !important;
            margin-top: 8px !important;
        }

        /* Chỉnh lại khoảng cách và cỡ chữ cho đoạn mô tả nhỏ bên dưới */
        .hero .section-title p {
            font-size: 15px !important;
            margin-top: 12px !important;
            max-width: 900px !important;
        }

        /* Nếu bạn muốn tiêu đề nhỏ hơn nữa trên thiết bị nhỏ (responsive) */
        @media (max-width: 768px) {
            .hero {
                min-height: 70vh !important;
                padding-top: 30px !important;
                padding-bottom: 30px !important;
            }

            .hero .section-title h1 {
                font-size: 26px !important;
            }

            .hero .section-title h1 span {
                font-size: 30px !important;
            }
        }

        @media (max-width: 768px) {

            /* Xóa toàn bộ spacing của Swiper */
            .hero-company-slider .swiper,
            .hero-company-slider .swiper-wrapper,
            .hero-company-slider .swiper-slide {
                margin: 0 !important;
                padding: 0 !important;
            }

            /* Xóa gap hoàn toàn */
            .hero-company-slider .swiper-wrapper {
                gap: 25px !important;
            }

            /* Ép slide co lại đúng bằng logo, không có khoảng trống */
            .hero-company-slider .swiper-slide {
                width: auto !important;
                flex: 0 0 auto !important;
                margin: 0 !important;
            }

            /* Logo nhỏ + sát nhau hoàn toàn */
            .hero-company-slider .company-logo img {
                width: 50px !important;
                height: 50px !important;
                object-fit: contain !important;
                margin: 0 !important;
                padding: 0 !important;
                display: block !important;
            }
        }

        /* Thu nhỏ logo trong header */
        .main-header .navbar-brand img {
            height: 80px !important;
            /* chỉnh kích thước logo */
            width: auto !important;
            /* giữ tỉ lệ đúng */
            object-fit: contain !important;
        }

        /* Logo nhỏ hơn trên mobile */
        @media (max-width: 768px) {
            .main-header .navbar-brand img {
                height: 60px !important;
            }
        }
    </style>

<link rel=preconnect href=https://fonts.googleapis.com/>
<link rel=preconnect href=https://fonts.gstatic.com/ crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&amp;display=swap" rel=stylesheet>
<link href=/landing/page1/css/bootstrap.min.css rel=stylesheet media=screen>
<link href=/landing/page1/css/slicknav.min.css rel=stylesheet>
<link rel=stylesheet href=/landing/page1/css/swiper-bundle.min.css>
<link href=/landing/page1/css/all.min.css rel=stylesheet media=screen>
<link href=/landing/page1/css/animate.css rel=stylesheet>
<link rel=stylesheet href=/landing/page1/css/magnific-popup.css>
<link rel=stylesheet href=/landing/page1/css/mousecursor.css>
<link href=/landing/page1/css/custom.css rel=stylesheet media=screen>

<body>
    <div class=preloader>
        <div class=loading-container>
            <div class=loading></div>
            <div id=loading-icon><img src=/landing/page1/images/loader.svg alt=""></div>
        </div>
    </div>
    <header class=main-header>
        <div class=header-sticky>
            <nav class="navbar navbar-expand-lg">
                <div class=container><a class=navbar-brand href=index.html><img src={{ asset(s('logo')) }}
                            alt=Logo></a>
                    <div class="collapse navbar-collapse main-menu">
                        <div class=nav-menu-wrapper>
                            <ul class="navbar-nav mr-auto" id=menu>
                                <li class=nav-item><a class=nav-link href="/new">Trang Chủ</a></li>
                                <li class=nav-item><a class=nav-link href="/services">Dịch Vụ</a></li>
                                <li class=nav-item><a class=nav-link href=/apidoc>API</a></li>
                              
                            </ul>
                        </div>
                        <div class=header-btn><a href=/auth/register class=btn-default>Đăng Ký</a></div>
                    </div>
                    <div class=navbar-toggle></div>
                </div>
            </nav>
            <div class=responsive-menu></div>
        </div>
    </header>
    <div class=hero>
        <div class=container>
            <div class="row align-items-center">
                <div class=col-lg-12>
                    <div class=hero-content>
                        <div class=section-title>
                            <h1 class="wow fadeInUp" data-cursor=-opaque>Hệ Thống Dịch Vụ Mạng Xã Hội Hàng Đầu Việt Nam
                                <span>SMMPANEL - Social Media Marketing</span></h1>
                            <p class="wow fadeInUp" data-wow-delay=0.2s>SMMPANEL - Social Media Marketing là nhà cung
                                cấp dịch vụ Facebook & TikTok & Instagram hàng đầu tại Việt Nam !! . Cùng rất nhiều dịch
                                vụ nền tảng khác giá tốt.</p>
                        </div>
                        <div class="hero-btn wow fadeInUp" data-wow-delay=0.4s><a href=/new
                                class="btn-default btn-highlighted">Truy Cập Ngay</a> <a href=/auth/login
                                class=btn-default>Đăng Nhập</a></div>
                        <div class=hero-company-slider>
                            <p>Cung Cấp Hầu Hết Các Nền Tảng Trên Thị Trường</p>
                            <div class=swiper>
                                <div class=swiper-wrapper>
                                    <div class=swiper-slide>
                                        <div class=company-logo><img
                                                src=https://cdn.mypanel.link/ob5eq3/8i5ip7j6s4zldfpc.gif alt=Facebook
                                                style="width:80px; height:80px; object-fit:contain;"></div>
                                    </div>
                                    <div class=swiper-slide>
                                        <div class=company-logo><img
                                                src=https://cdn.mypanel.link/ob5eq3/pzxc1dk0d4arm7d0.gif alt=TikTok
                                                style="width:80px; height:80px; object-fit:contain;"></div>
                                    </div>
                                    <div class=swiper-slide>
                                        <div class=company-logo><img
                                                src=https://cdn.mypanel.link/ob5eq3/fkeumdkfe6tm2nnw.gif alt=Instagram
                                                style="width:80px; height:80px; object-fit:contain;"></div>
                                    </div>
                                    <div class=swiper-slide>
                                        <div class=company-logo><img
                                                src=https://storage.perfectcdn.com/omxgjl/rc2yu8dow04524tt.gif
                                                alt=YouTube style="width:80px; height:80px; object-fit:contain;"></div>
                                    </div>
                                    <div class=swiper-slide>
                                        <div class=company-logo><img
                                                src=https://storage.perfectcdn.com/hghbyr/ej7swmpsdmz98m74.gif
                                                alt=Twitter style="width:80px; height:80px; object-fit:contain;"></div>
                                    </div>
                                    <div class=swiper-slide>
                                        <div class=company-logo><img
                                                src=https://cdn.mypanel.link/sw177w/7ea6iam2aygm0qws.gif alt=Telegram
                                                style="width:80px; height:80px; object-fit:contain;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class=about-us>
        <div class=container>
            <div class="row section-row align-items-center">
                <div class=col-lg-12>
                    <div class="section-title section-title-center">
                        <h3 class="wow fadeInUp">Về Chúng Tôi</h3>
                        <h2 class=text-effect data-cursor=-opaque style="font-size:26px; line-height:1.3;">Giải pháp
                            tăng tương tác mạng xã hội nhanh, an toàn và hiệu quả cho Facebook, TikTok, Instagram và
                            YouTube</h2>
                    </div>
                </div>
            </div>
            <div class=row>
                <div class="col-lg-3 col-md-6">
                    <div class=about-us-box>
                        <div class=about-us-item>
                            <div class=about-item-content>
                                <h3>Đơn Hoàn Thành</h3>
                                <h2><span class=counter>44</span>K+</h2>
                            </div>
                            <div class=icon-box><img src=/landing/page1/images/icon-about-item-1.svg alt=""></div>
                        </div>
                        <div class=about-item-image><img src=/landing/page1/images/about-item-image-1.png alt=""></div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class=about-us-box>
                        <div class=about-us-item>
                            <div class=about-item-content>
                                <h3>Khách Hàng Đã Sử Dụng</h3>
                                <h2><span class=counter>5</span>K+</h2>
                            </div>
                            <div class=icon-box><img src=/landing/page1/images/icon-about-item-2.svg alt=""></div>
                        </div>
                        <div class=about-item-image><img src=/landing/page1/images/about-item-image-2.png alt=""></div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class=about-us-box>
                        <div class=about-us-item>
                            <div class=about-item-content>
                                <h3>Dịch Vụ Mới</h3>
                                <h2><span class=counter>300</span>%</h2>
                            </div>
                            <div class=icon-box><img src=/landing/page1/images/icon-about-item-3.svg alt=""></div>
                        </div>
                        <div class=about-item-image><img src=/landing/page1/images/about-item-image-3.png alt=""></div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class=about-us-box>
                        <div class=about-us-item>
                            <div class=about-item-content>
                                <h3>Độ Tin Cậy</h3>
                                <h2><span class=counter>100</span>%</h2>
                            </div>
                            <div class=icon-box><img src=/landing/page1/images/icon-about-item-4.svg alt=""></div>
                        </div>
                        <div class=about-item-image><img src=/landing/page1/images/about-item-image-4.png alt=""></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class=real-impacts>
        <div class=container>
            <div class="row section-row">
                <div class=col-lg-12>
                    <div class="section-title section-title-center">
                        <h3 class="wow fadeInUp">Hiệu Quả Thực Tế</h3>
                        <h2 class="wow fadeInUp" data-wow-delay=0.2s data-cursor=-opaque>Mang lại kết quả rõ ràng giúp
                            <span>tăng trưởng mạnh mẽ</span></h2>
                    </div>
                </div>
            </div>
            <div class=row>
                <div class="col-lg-4 col-md-6">
                    <div class="real-impact-item wow fadeInUp">
                        <div class=real-impact-image>
                            <h3>Tăng tương tác người dùng trên Facebook – TikTok – Instagram.</h3>
                            <div class=impact-chatbot-list>
                                <div class=chatbot-item><img src=/landing/page1/images/impact-chatbot-1.svg alt="">
                                </div>
                                <div class=chatbot-item><img src=/landing/page1/images/impact-chatbot-2.svg alt="">
                                </div>
                                <div class=chatbot-item><img src=/landing/page1/images/impact-chatbot-3.svg alt="">
                                </div>
                                <div class=chatbot-item><img src=/landing/page1/images/impact-chatbot-4.svg alt="">
                                </div>
                                <div class=chatbot-item><img src=/landing/page1/images/impact-chatbot-5.svg alt="">
                                </div>
                            </div>
                        </div>
                        <div class=real-impact-content>
                            <p>Chúng tôi mang đến các giải pháp tăng tương tác an toàn – hiệu quả, giúp đẩy mạnh hiển
                                thị, tối ưu thuật toán và tăng độ phủ tự nhiên cho thương hiệu cũng như nhà sáng tạo nội
                                dung.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="real-impact-item wow fadeInUp" data-wow-delay=0.2s>
                        <div class=real-impact-image>
                            <figure><img src=/landing/page1/images/real-impact-image-1.png alt=""></figure>
                        </div>
                        <div class="real-impact-content highlighted-content">
                            <div class=customer-review-images>
                                <div class="customer-image reveal">
                                    <figure class=image-anime><img src=/landing/page1/images/author-1.jpg alt="">
                                    </figure>
                                </div>
                                <div class="customer-image reveal">
                                    <figure class=image-anime><img src=/landing/page1/images/author-2.jpg alt="">
                                    </figure>
                                </div>
                                <div class="customer-image reveal">
                                    <figure class=image-anime><img src=/landing/page1/images/author-3.jpg alt="">
                                    </figure>
                                </div>
                                <div class="customer-image reveal">
                                    <figure class=image-anime><img src=/landing/page1/images/author-4.jpg alt="">
                                    </figure>
                                </div>
                            </div>
                            <p>Hơn 5.000+ khách hàng đã tin dùng dịch vụ để tăng tương tác và cải thiện hiệu suất nội
                                dung mỗi ngày.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="real-impact-item wow fadeInUp" data-wow-delay=0.4s>
                        <div class=real-impact-image>
                            <figure><img src=/landing/page1/images/real-impact-image-2.png alt=""></figure>
                        </div>
                        <div class=real-impact-content>
                            <p>Giải pháp được tối ưu theo thuật toán thực tế, giúp video dễ lên xu hướng, fanpage tăng
                                reach tự nhiên và kênh social phát triển ổn định bền vững.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class=our-testimonials>
        <div class=container>
            <div class=row>
                <div class=col-lg-12>
                    <div class=testimonials-box>
                        <div class=testimonials-content>
                            <div class=section-title>
                                <h3 class="wow fadeInUp">Khách hàng nói gì?</h3>
                                <h2 class="wow fadeInUp" data-wow-delay=0.2s data-cursor=-opaque>Hơn 5.000+ creator &
                                    doanh nghiệp tin tưởng dịch vụ <span>Tăng Tương Tác</span></h2>
                                <p class="wow fadeInUp" data-wow-delay=0.4s>Chúng tôi giúp bạn tăng tương tác thật, tối
                                    ưu thuật toán và mở rộng độ phủ thương hiệu trên Facebook, TikTok, Instagram và
                                    YouTube.</p>
                            </div>
                            <div class=testimonials-body>
                                <div class=testimonials-counter-item>
                                    <h2><span class=counter>5</span>k+</h2>
                                    <p>Khách hàng hài lòng</p>
                                </div>
                                <div class=testimonials-counter-item>
                                    <h2><span class=counter>35</span>M+</h2>
                                    <p>Lượt tương tác đã tạo ra</p>
                                </div>
                            </div>
                        </div>
                        <div class=testimonial-slider>
                            <div class=swiper>
                                <div class=swiper-wrapper>
                                    <div class=swiper-slide>
                                        <div class=testimonial-item>
                                            <div class=testimonial-header>
                                                <div class=testimonial-author>
                                                    <div class=author-image>
                                                        <figure class=image-anime><img
                                                                src=/landing/page1/images/sontung.jpeg alt=""></figure>
                                                    </div>
                                                    <div class=author-content>
                                                        <h3>Sơn Tùng MTP</h3>
                                                        <p>Ca Sĩ</p>
                                                    </div>
                                                </div>
                                                <div class=testimonial-quotes-img><img
                                                        src=/landing/page1/images/testimonials-quotes-img.svg alt="">
                                                </div>
                                            </div>
                                            <div class=testimonial-content>
                                                <p>"Dịch vụ tăng tương tác thật sự tuyệt vời. Fanpage của tôi tăng hơn
                                                    300% lượt tương tác chỉ sau 2 tuần."</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class=swiper-slide>
                                        <div class=testimonial-item>
                                            <div class=testimonial-header>
                                                <div class=testimonial-author>
                                                    <div class=author-image>
                                                        <figure class=image-anime><img
                                                                src=/landing/page1/images/tunpham.jpeg alt=""></figure>
                                                    </div>
                                                    <div class=author-content>
                                                        <h3>Tun Phạm</h3>
                                                        <p>Content Creator</p>
                                                    </div>
                                                </div>
                                                <div class=testimonial-quotes-img><img
                                                        src=/landing/page1/images/testimonials-quotes-img.svg alt="">
                                                </div>
                                            </div>
                                            <div class=testimonial-content>
                                                <p>"Từ khi dùng dịch vụ, kênh TikTok của mình tăng follow tự nhiên và
                                                    lượng tương tác tốt hơn hẳn."</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class=swiper-slide>
                                        <div class=testimonial-item>
                                            <div class=testimonial-header>
                                                <div class=testimonial-author>
                                                    <div class=author-image>
                                                        <figure class=image-anime><img
                                                                src=/landing/page1/images/quynhaka.jpeg alt=""></figure>
                                                    </div>
                                                    <div class=author-content>
                                                        <h3>Quỳnh Aka</h3>
                                                        <p>Nữ TikToker</p>
                                                    </div>
                                                </div>
                                                <div class=testimonial-quotes-img><img
                                                        src=/landing/page1/images/testimonials-quotes-img.svg alt="">
                                                </div>
                                            </div>
                                            <div class=testimonial-content>
                                                <p>"Dịch vụ rất uy tín, giúp mình duy trì tương tác và tăng view đều mỗi
                                                    ngày."</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class=testimonial-pagination></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class=our-faqs>
        <div class=container>
            <div class=row>
                <div class=col-lg-6>
                    <div class=faqs-content>
                        <div class=section-title>
                            <h3 class="wow fadeInUp">Câu hỏi thường gặp</h3>
                            <h2 class="wow fadeInUp" data-wow-delay=0.2s data-cursor=-opaque>Giải đáp thắc mắc về dịch
                                vụ <span>tăng tương tác mạng xã hội</span></h2>
                        </div>
                        <div class="faqs-button wow fadeInUp" data-wow-delay=0.4s><a href=/new class=btn-default>Truy
                                Cập Ngay</a></div>
                    </div>
                </div>
                <div class=col-lg-6>
                    <div class=faq-accordion id=accordion>
                        <div class="accordion-item wow fadeInUp">
                            <h2 class=accordion-header id=heading1><button class="accordion-button collapsed"
                                    type=button data-bs-toggle=collapse data-bs-target=#collapse1 aria-expanded=true
                                    aria-controls=collapse1>1. Dịch vụ tăng tương tác gồm những gì?</button></h2>
                            <div id=collapse1 class="accordion-collapse collapse" aria-labelledby=heading1
                                data-bs-parent=#accordion>
                                <div class=accordion-body>
                                    <p>Chúng tôi cung cấp các dịch vụ tăng like, follow, comment, share, view cho
                                        Facebook, TikTok, Instagram và YouTube. Tất cả đều xử lý tự động, nhanh và đảm
                                        bảo an toàn cho tài khoản.</p>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item wow fadeInUp" data-wow-delay=0.2s>
                            <h2 class=accordion-header id=heading2><button class=accordion-button type=button
                                    data-bs-toggle=collapse data-bs-target=#collapse2 aria-expanded=false
                                    aria-controls=collapse2>2. Tăng tương tác có an toàn cho tài khoản không?</button>
                            </h2>
                            <div id=collapse2 class="accordion-collapse collapse show" aria-labelledby=heading2
                                data-bs-parent=#accordion>
                                <div class=accordion-body>
                                    <p>Dịch vụ chạy theo thuật toán tự nhiên, không yêu cầu mật khẩu, không ảnh hưởng độ
                                        an toàn của tài khoản. Hệ thống tối ưu để hạn chế tối đa rủi ro.</p>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item wow fadeInUp" data-wow-delay=0.4s>
                            <h2 class=accordion-header id=heading3><button class="accordion-button collapsed"
                                    type=button data-bs-toggle=collapse data-bs-target=#collapse3 aria-expanded=false
                                    aria-controls=collapse3>3. Bao lâu thì đơn tăng tương tác hoàn thành?</button></h2>
                            <div id=collapse3 class="accordion-collapse collapse" aria-labelledby=heading3
                                data-bs-parent=#accordion>
                                <div class=accordion-body>
                                    <p>Thời gian xử lý tùy dịch vụ: các đơn nhỏ chạy trong vài phút, đơn lớn có thể 5–30
                                        phút. Một số dịch vụ đặc biệt có thể lâu hơn.</p>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item wow fadeInUp" data-wow-delay=0.6s>
                            <h2 class=accordion-header id=heading4><button class="accordion-button collapsed"
                                    type=button data-bs-toggle=collapse data-bs-target=#collapse4 aria-expanded=false
                                    aria-controls=collapse4>4. Có cần cung cấp mật khẩu tài khoản không?</button></h2>
                            <div id=collapse4 class="accordion-collapse collapse" aria-labelledby=heading4
                                data-bs-parent=#accordion>
                                <div class=accordion-body>
                                    <p>Không. Bạn chỉ cần cung cấp link bài viết hoặc link tài khoản. Hệ thống hoàn toàn
                                        tự động nên không bao giờ yêu cầu mật khẩu.</p>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item wow fadeInUp" data-wow-delay=0.8s>
                            <h2 class=accordion-header id=heading5><button class="accordion-button collapsed"
                                    type=button data-bs-toggle=collapse data-bs-target=#collapse5 aria-expanded=false
                                    aria-controls=collapse5>5. Dịch vụ có bảo hành không?</button></h2>
                            <div id=collapse5 class="accordion-collapse collapse" aria-labelledby=heading5
                                data-bs-parent=#accordion>
                                <div class=accordion-body>
                                    <p>Có. Các dịch vụ đều có bảo hành theo từng loại, đảm bảo tương tác không bị tụt
                                        ngoài phạm vi cho phép. Nếu có lỗi, hệ thống tự động hỗ trợ hoàn trả hoặc bổ
                                        sung.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer class=main-footer>
        <div class=container>
            <div class="row section-row align-items-center">
                <div class=col-lg-7>
                    <div class=section-title>
                        <h2 class="wow fadeInUp" data-cursor=-opaque>Sẵn sàng tăng trưởng ngay hôm nay?</h2>
                        <p class="wow fadeInUp" data-wow-delay=0.2s>Tạo tài khoản miễn phí, thêm số dư sau – bắt đầu
                            trong 60 giây.</p>
                    </div>
                </div>
                <div class=col-lg-5>
                    <div class="section-btn wow fadeInUp" data-wow-delay=0.4s><a href=/auth/register class=btn-default>Tạo
                            Tài Khoản Ngay</a></div>
                </div>
            </div>
            <div class=row>
                <div class="col-lg-3 col-md-4">
                    <div class=about-footer>
                        <div class=footer-logo><img src={{ asset(s('logo')) }} alt=""></div>
                    </div>
                </div>
                
                <div class=col-lg-12>
                    <div class=footer-copyright-text>
                        <p>Copyright © 2025 All Rights Reserved.</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <script src=/landing/page1/js/jquery-3.7.1.min.js></script>
    <script src=/landing/page1/js/bootstrap.min.js></script>
    <script src=/landing/page1/js/validator.min.js></script>
    <script src=/landing/page1/js/jquery.slicknav.js></script>
    <script src=/landing/page1/js/swiper-bundle.min.js></script>
    <script src=/landing/page1/js/jquery.waypoints.min.js></script>
    <script src=/landing/page1/js/jquery.counterup.min.js></script>
    <script src=/landing/page1/js/jquery.magnific-popup.min.js></script>
    <script src=/landing/page1/js/parallaxie.js></script>
    <script src=/landing/page1/js/gsap.min.js></script>
    <script src=/landing/page1/js/magiccursor.js></script>
    <script src=/landing/page1/js/SplitText.js></script>
    <script src=/landing/page1/js/ScrollTrigger.min.js></script>
    <script src=/landing/page1/js/SmoothScroll.js></script>
    <script src=/landing/page1/js/jquery.mb.YTPlayer.min.js></script>
    <script src=/landing/page1/js/wow.min.js></script>
    <script src=/landing/page1/js/function.js></script>
    <script defer
        src="https://static.cloudflareinsights.com/beacon.min.js/vcd15cbe7772f49c399c6a5babf22c1241717689176015"
        integrity="sha512-ZpsOmlRQV6y907TI0dKBHq9Md29nnaEIPlkf84rnaERnq6zvWvPUqr2ft8M1aS28oN72PdrCzSjY4U6VaAw1EQ=="
        data-cf-beacon='{"version":"2024.11.0","token":"2f8dde1263ef4151b8a6e38a9b1cba56","r":1,"server_timing":{"name":{"cfCacheStatus":true,"cfEdge":true,"cfExtPri":true,"cfL4":true,"cfOrigin":true,"cfSpeedBrain":true},"location_startswith":null}}'
        crossorigin="anonymous"></script>
</body>

</html>
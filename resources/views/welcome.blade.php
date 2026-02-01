@extends('landing.layout')
@section('content')
<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
    <div class="container d-flex align-items-center justify-content-between">
        <!-- Brand (ATA + متن) -->
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="{{ asset('assets/landing/images/ATA_Right.png') }}" alt="لوگو ATA"
                 style="height: 40px; margin-left: 10px;"
                 onerror="this.style.display='none';">
            آرین تأمین آفرین SCF
        </a>

        <!-- راست: همبرگری + Golrang -->
        <div class="d-flex align-items-center order-lg-3">
            <!-- Toggler -->
            <button class="navbar-toggler me-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- Golrang -->
            <img src="{{ asset('assets/landing/images/Golrang_Left.png') }}" alt="Golrang Logo" style="height: 25px;">
        </div>

        <!-- وسط: منو -->
        <div class="collapse navbar-collapse order-lg-2 justify-content-end text-end" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="#home">خانه</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Slider Section -->
<section id="home" class="hero-slider">i
    <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="3000">
        <div class="carousel-inner">

            <!-- Slide 1 -->
            <div class="carousel-item active">
                <img src="{{ asset('assets/landing/images/2525-scaled.jpg') }}" class="d-block w-100" alt="تصویر کسب‌وکار و زنجیره تأمین"
                     onerror="this.src='/assets/landing/images/2525-scaled.jpg';">

                <!-- Captions -->
                <div class="carousel-caption">
                    <!-- موبایل -->
                    <div class="d-block d-md-none mobile-caption">
                        <h2 class="fw-bold text-white mb-2" style="font-size: 1.5rem;">توانمندسازی زنجیره تأمین</h2>
                        <p class="text-white mb-3" style="font-size: 0.9rem;">راهکارهای مالی برای رشد پایدار</p>
                        <a href="#contact" class="btn btn-success btn-sm px-3">مشاوره رایگان</a>
                    </div>

                    <!-- دسکتاپ -->
                    <div class="d-none d-md-block">
                        <h1 class="display-3 fw-bold text-white mb-4">توانمندسازی زنجیره تأمین<br> بهبود نقدینگی</h1>
                        <p class="lead mb-4 text-white">راهکارهای نوین مالی با پشتوانه‌ی گروه صنعتی گلرنگ و تیمی از خبرگان بانکی</p>
                        <p class="fs-5 mb-4 text-white">پلتفرم SCF آرین تأمین آفرین؛ شتاب در دریافت مطالبات، قدرت بیشتر برای خریداران و پرداخت‌های دیجیتال و شفاف</p>
                        <a href="#contact" class="btn btn-success btn-lg px-4">شروع مشاوره رایگان</a>
                    </div>
                </div>
            </div>

            <!-- Slide 2 -->
            <div class="carousel-item">
                <img src="{{ asset('landing/images/entrepreneurs.jpg') }}" class="d-block w-100" alt="تصویر کارآفرینان و شهر مدرن"
                     onerror="this.src='/assets/landing/images/entrepreneurs.jpg';">

                <div class="carousel-caption">
                    <!-- موبایل -->
                    <div class="d-block d-md-none mobile-caption">
                        <h2 class="fw-bold text-white mb-2" style="font-size: 1.5rem;">توانمندسازی زنجیره تأمین</h2>
                        <p class="text-white mb-3" style="font-size: 0.9rem;">جریان نقدینگی سریع‌تر، رشد پایدار</p>
                        <a href="#contact" class="btn btn-success btn-sm px-3">مشاوره رایگان</a>
                    </div>

                    <!-- دسکتاپ -->
                    <div class="d-none d-md-block">
                        <h1 class="display-3 fw-bold text-white mb-4">توانمندسازی زنجیره تأمین<br> بهبود نقدینگی</h1>
                        <p class="lead mb-4 text-white">راهکارهای نوین مالی با پشتوانه‌ی گروه صنعتی گلرنگ و تیمی از خبرگان بانکی</p>
                        <p class="fs-5 mb-4 text-white">پلتفرم SCF آرین تأمین آفرین؛ شتاب در دریافت مطالبات، قدرت بیشتر برای خریداران و پرداخت‌های دیجیتال و شفاف</p>
                        <a href="#contact" class="btn btn-success btn-lg px-4">شروع مشاوره رایگان</a>
                    </div>
                </div>
            </div>

        </div>

        <!-- Controls -->
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">قبلی</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">بعدی</span>
        </button>
    </div>
</section>


<!-- CTA Footer with Parallax -->
<style>
    footer#contact {
        color: white;
    }

    footer#contact a {
        color: white;
        text-decoration: none;
    }

    footer#contact a:hover {
        text-decoration: underline;
    }

    footer#contact .form-label {
        color: white;
    }

    footer#contact ::placeholder {
        color: #ddd;
    }
</style>

@endsection

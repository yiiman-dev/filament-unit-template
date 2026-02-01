<!DOCTYPE html>
<html>
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>آرین تأمین آفرین SCF | آزادسازی نقدینگی و توانمندسازی زنجیره تأمین</title>
    <meta name="description"
          content="پلتفرم تخصصی تأمین مالی زنجیره تأمین SCF برای بهبود cash-flow، کاهش ریسک و رشد پایدار کسب‌وکارها.">
    <meta name="keywords" content="SCF, تأمین مالی زنجیره تأمین, آرین تأمین آفرین, فین‌تک, نقدینگی">

    <!-- Bootstrap RTL CSS -->
    <link href="{{asset('/assets/landing/css/bootstrap.min.css')}}" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="{{asset('/assets/landing/css/bootstrap-icons.css')}}" rel="stylesheet">
    <!-- Google Fonts: Vazir -->
    <link href="{{asset('/assets/landing/css/google-fonts.css')}}" rel="stylesheet">
    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Custom Styles -->
    <link href="{{asset('/assets/landing/css/styles.css')}}" rel="stylesheet">
</head>

<body>

@yield('content')

@stack('scripts')

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- AOS JS -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<!-- Custom Script -->
<script src="{{asset('/assets/landing/js/script.js')}}"></script>
</body>
</html>

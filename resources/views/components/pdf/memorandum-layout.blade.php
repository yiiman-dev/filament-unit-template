@php
    $fontLight = base64_encode(file_get_contents(public_path("fonts/shabnam/Shabnam-Light-FD.ttf")));
    $fontRegular = base64_encode(file_get_contents(public_path("fonts/shabnam/Shabnam-FD.ttf")));
    $fontBold = base64_encode(file_get_contents(public_path('fonts/shabnam/Shabnam-Bold-FD.ttf')));
@endphp
<html>
<head>
    <title>{{ $title ?? 'Todo Manager' }}</title>


    <style>
        @font-face {
            font-family: 'Shabnam';
            src: url('data:font/ttf;charset=utf-8;base64,{{ $fontRegular }}') format('truetype');
        }

        body:not(.fonts-loaded) {
            visibility: hidden;
        }

        body, * {
            font-family: 'Shabnam' !important;
            font-size: clamp(8px, 1vw, 14px);
        }

    </style>

</head>
<body>
<div dir="rtl">
    {{ $slot }}
</div>

<script>
    document.fonts.ready.then(() => {
        document.body.classList.add('fonts-loaded');
    });
</script>
</body>
</html>

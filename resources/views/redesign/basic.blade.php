<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @if(!empty($pageTitle))
        <title>{{ $pageTitle }}</title>
    @else
        <title>{{ config('app.name') }}</title>
    @endif

    @if(!empty($pageDescription))
        <meta name="description" content="{{$pageDescription}}">
    @endif

    @if(!empty($pageKeywords))
        <meta name="keywords" content="{{$pageKeywords}}">
    @endif

    <!-- Fonts -->
{{--    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600&font-display=swap" rel="stylesheet" type="text/css">--}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="apple-touch-icon" sizes="57x57" href="/favicons/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/favicons/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/favicons/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/favicons/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/favicons/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/favicons/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/favicons/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/favicons/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/favicons/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="/favicons/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/favicons/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicons/favicon-16x16.png">
    <link rel="icon"
          type="image/png"
          href="/favicons/favicon-96x96.png">

    <link rel="manifest" href="/favicons/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/favicons/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <link rel="stylesheet" href="{{mix('css/app.css')}}">

    <!-- Facebook Pixel Code -->
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window,document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '540738236784232');
        fbq('track', 'PageView');
    </script>
    <noscript>
        <img height="1" width="1"
             src="https://www.facebook.com/tr?id=540738236784232&ev=PageView&noscript=1"/>
    </noscript>
    <!-- End Facebook Pixel Code -->


</head>
<body class="{{$mainPageClass ?? ''}}" >
    @if(empty($noHeader))
        @include('redesign.parts.header_new')
    @endif

    <div id="app">
        @yield('content')
    </div>

    {{-- @include('redesign.parts.mobile_menu')

    <a class="mobile-cart-icon" href="/order">
        <img src="/images/icons/mobile-menu/cart-widget.png"
             alt="Информация"
        />

        <span class="badge badge-danger cart-count">{{Cart::count()}}</span>
    </a> --}}

    @include('redesign.parts.footer_new')


    @if(!isBot())
       @include('redesign.parts.scripts')
       @stack('js')
    @endif
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
    @stack('css')
</body>
</html>

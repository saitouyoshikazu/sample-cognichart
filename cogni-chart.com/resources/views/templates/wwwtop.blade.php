<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-143826055-1"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
    
      gtag('config', 'UA-143826055-1');
    </script>

@if (!empty($link_canonical))
    <link rel="canonical" href="{{$link_canonical}}">
@endif
    <title>Cogni Chart</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('ico/favicon.ico') }}">
    <link rel="apple-touch-icon" size="180x180" href="{{ asset('png/apple-touch-icon-180x180.png') }}">

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="{{ __("YouTube songs of singles chart. You can listen to musics without an account.") }}">

    <meta property="og:title" content="Cogni Chart" />
    <meta property="og:type" content="website" />
    <meta property="og:description" content="{{ __("YouTube songs of singles chart. You can listen to musics without an account.") }}" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:site_name" content="Cogni Chart" />
    <meta property="og:image" content="{{ asset('png/opg-cognichart.png') }}" />

    <meta property="fb:app_id" content="{{ config('app.facebook_app_id') }}" />

    <meta property="twitter:card" content="summary_large_image" />
    <meta property="twitter:site" content="@CogniChart" />
    <meta property="twitter:creator" content="@CogniChart" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Styles -->
    <link href="{{ asset('css/www/cognichart.com.css') }}" rel="stylesheet">
    <script src="{{ asset('js/app.js') }}" async></script>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
    <script>
        (adsbygoogle = window.adsbygoogle || []).push({
            google_ad_client: "ca-pub-7212715746724887",
            enable_page_level_ads: true
        });
    </script>
</head>
<body class="bg-box" style="background-image: url({{ asset('jpg/top-1920-1080.jpg') }});">
@yield('content')
@include('www.top.topfooter')
</body>
</html>

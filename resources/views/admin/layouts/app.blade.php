<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible"
        content="IE=edge">
  <meta name="viewport"
        content="width=device-width, initial-scale=1">
  <title>{{env('APP_NAME')}} admin</title>
  <link rel="stylesheet"
        href="/css/app.css">
  <!-- Fonts -->
  {{--    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">--}}
  <meta name="csrf-token"
        content="{{ csrf_token() }}">
    <meta http-equiv="cache-control" content="no-cache, must-revalidate, post-check=0, pre-check=0" />
    <meta http-equiv="cache-control" content="max-age=0" />
    <meta http-equiv="expires" content="0" />
    <meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
    <meta http-equiv="pragma"  content="no-cache" />
</head>
<body>
<div class="row">
  <div class="col-md-2">
    @component('admin.layouts.sidebar')
    @endcomponent

  </div>
  <div class="col-md-10">
    <div class="content m-6">
      @yield('content')
    </div>
  </div>
</div>
<div class="footer footer-border bg-dark pr-6 pl-6 pt-3 pb-3 mt-6 d-flex justify-content-between">
  <div>
    <img src="/images/icons/social/vk.png"
         alt=""
         class="Icon-social mr-2">
    <img src="/images/icons/social/facebook.png"
         alt=""
         class="Icon-social mr-2">
    <img src="/images/icons/social/instagram.png"
         alt=""
         class="Icon-social mr-2">
    <img src="/images/icons/social/telegram.png"
         alt=""
         class="Icon-social mr-2">
  </div>
  <div class="text-info">
    © {{date('Y')}} {{env('APP_URL')}}
  </div>
</div>
<script src="/js/app.js"></script>
@yield('js')
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Shared on THEMELOCK.COM - Sign In | Urbix Admin & Dashboards Template </title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <meta content="Admin & Dashboards Template" name="description" />
  <meta content="Pixeleyez" name="author" />
  
  <!-- layout setup -->
  <script type="module" src="{{ asset('assets/js/layout-setup.js') }}"></script>
  
  <!-- App favicon -->
  <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}">  <!-- Simplebar Css -->
  <link rel="stylesheet" href="{{ asset('assets/libs/simplebar/simplebar.min.css') }}">
  <!-- Swiper Css -->
  <link href="{{ asset('assets/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
  <!-- Nouislider Css -->
  <link href="{{ asset('assets/libs/nouislider/nouislider.min.css') }}" rel="stylesheet">
  <!-- Bootstrap Css -->
  <link href="{{ asset('assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css">
  <!--icons css-->
  <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css">
  <!-- App Css-->
  <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START -->
<div class="position-fixed top-0 bottom-0 end-0 start-0 z-0 bg-pattern"></div>
<div class="auth-pattern-shapes d-none d-lg-block"></div>
<div class="auth-pattern-outline d-none d-lg-block"></div>
<div class="auth-pattern-shape extra d-none d-lg-block"></div>
<div class="auth-pattern-extra d-none d-lg-block"></div>

@yield('content')

<!-- JAVASCRIPT -->
<script src="{{ asset('assets/libs/swiper/swiper-bundle.min.js') }}"></script>
<script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/js/scroll-top.init.js') }}"></script>
<script src="{{ asset('assets/js/auth/auth.init.js') }}"></script>
</body>

</html>
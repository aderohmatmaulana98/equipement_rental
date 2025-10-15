
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Not Authorize</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta content="Admin & Dashboards Template" name="description" />
    <meta content="Pixeleyez" name="author" />
    
    <!-- layout setup -->
    <script type="module" src="{{ asset('assets/js/layout-setup.js') }}"></script>
    
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}">    <!-- Simplebar Css -->
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
    <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css"></head>

<body>
<!-- START -->
<div>
    <div class="floating-shape animate-circle shape-square square-1 d-none d-lg-block"></div>
    <div class="floating-shape animate-circle shape-square square-2 d-none d-lg-block"></div>
    <div class="floating-shape animate-circle shape-triangle triangle-1 d-none d-lg-block"></div>
    <div class="floating-shape animate-circle shape-triangle triangle-2 d-none d-lg-block"></div>
    <div class="container">
        <div class="row position-relative justify-content-center align-items-center min-vh-100 position-relative">
            <div class="col-xl-8">
                <div class="text-center py-20 py-md-16">
                    <img src="{{ asset('assets/images/vector/authorized.png') }}" alt="Not Authorized" class="img-fluid mb-12">
                    <h1 class="mb-4 fs-55 text-primary">Oops! You Don’t Have Access</h1>
                    <p class="max-w-600px mx-auto fs-15 text-muted"> Oops! You don't have permission to view this page. Please make sure you're logged in with the right account or contact the admin for access.</p>
                    <div class="mt-12">
                        @php
                            if (auth()->user()->role_id == 1) {
                                $url = "/pemilik/dashboard";
                            } elseif (auth()->user()->role_id == 2) {
                                $url = "/admin/dashboard";
                            } elseif (auth()->user()->role_id == 4) {
                                $url = "/warehouse/dashboard";
                            }
                            else {
                                $url = "/user/dashboard";
                            }
                        @endphp
                        <a href="{{ $url }}" class="btn btn-primary">Back to Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JAVASCRIPT -->
<script src="{{ asset('assets/libs/swiper/swiper-bundle.min.js') }}"></script>
<script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/js/scroll-top.init.js') }}"></script>
</body>

</html>
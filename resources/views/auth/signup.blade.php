@extends('auth.templates.base')

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100 pt-20 pb-10">
        <div class="col-12 col-md-8 col-lg-6 col-xl-5">
            <div class="card mx-xxl-8 shadow-none">
                @if(session('success'))
                    <div class="alert alert-subtle-success d-flex align-items-center mb-2" id="success-alert" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        Logout Berhasil
                    </div>
                    <script>
                    // Auto hide setelah 3 detik
                    setTimeout(function() {
                        var alert = document.getElementById('success-alert');
                        if (alert) {
                            var bsAlert = new bootstrap.Alert(alert);
                            bsAlert.close();
                        }
                    }, 3000);
                </script>
                @endif

                @if(session('error'))
                    <div class="alert alert-subtle-danger d-flex align-items-center mb-2" id="error-alert" role="alert">
                        <i class="bi bi-x-circle-fill me-2"></i>
                        Email atau Password salah
                    </div>
                    <script>
                        // Auto hide setelah 3 detik
                        setTimeout(function() {
                            var alert = document.getElementById('error-alert');
                            if (alert) {
                                var bsAlert = new bootstrap.Alert(alert);
                                bsAlert.close();
                            }
                        }, 3000);
                    </script>
                @endif
                <div class="card-body p-8">
                    <h3 class="fw-medium text-center">Let’s get started!</h3>
                    <p class="mb-8 text-muted text-center">Create Your Account in Minutes</p>
                    <form method="post" action="{{ route('signup_action') }}">
                        @csrf
                        <div class="mb-4">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" placeholder="name" name="name" required>
                        </div>
                        <div class="mb-4">
                            <label for="no_hp" class="form-label">No HP<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="no_hp" placeholder="No HP" name="no_hp" required>
                        </div>
                        <div class="mb-4">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="email" placeholder="Email" name="email" required>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input type="password" class="form-control" id="password" placeholder="Password" name="password" required>
                                <button type="button" class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted toggle-password" id="toggle-password" data-target="password"><i class="ri-eye-off-line align-middle"></i></button>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="confirmationPassword" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input type="password" class="form-control" id="confirmationPassword" name="confirmationPassword" placeholder="Confirm Password" required>
                                <button type="button" class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted toggle-password" id="toggle-password" data-target="ConfirmPassword"><i class="ri-eye-off-line align-middle"></i></button>
                            </div>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary w-100 mb-4">Sign Up</button>
                        </div>
                    </form>
                        <p class="text-center mt-6 mb-0 text-muted fs-13">Already have an account? <a href="{{ route("login") }}" class="link fw-semibold">Sign In here</a></p>
                </div>
            </div>
            <p class="position-relative text-center fs-13 mb-0">©
                <script>document.write(new Date().getFullYear())</script> Urbix. Crafted with by Pixeleyez
            </p>
        </div>
    </div>
</div>
@endsection
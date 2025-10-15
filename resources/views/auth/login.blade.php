@extends('auth.templates.base')

@section('content')
  <div class="container">
    <div class="row justify-content-center align-items-center min-vh-100 pt-20 pb-10">
      <div class="col-12 col-md-8 col-lg-6 col-xl-5">
        <div class="card mx-xxl-8 shadow-none">
          <!-- Tampilkan flash message -->
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
            <h3 class="fw-medium text-center">Welcome back!</h3>
            <p class="mb-8 text-muted text-center">Create Your Account in Minutes</p>
            <form action="{{ route('login_action') }}" method="POST">
              @csrf
              <div class="mb-4">
                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
              </div>
              <div class="mb-4">
                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                <div class="position-relative">
                  <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                  <button type="button" class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted toggle-password" id="toggle-password" data-target="password"><i class="ri-eye-off-line align-middle"></i></button>
                </div>
              </div>
              <div class="my-6">
                <div class="d-flex justify-content-between align-items-center">
                  {{-- <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="rememberMe">
                    <label class="form-check-label" for="rememberMe">Remember me</label>
                  </div> --}}
              
                </div>
              </div>
              <div>
                <button type="submit" class="btn btn-primary w-100 mb-4">Sign In</button>
                
              </div>
            </form>
            <p class="text-center mt-6 mb-0 text-muted fs-13">Don't have an account? <a href="{{ route('signup') }}" class="link fw-semibold">Sign up here</a></p>
          </div>
        </div>
        <p class="position-relative text-center fs-13 mb-0">©
          <script>document.write(new Date().getFullYear())</script> Urbix. Crafted with by Pixeleyez
        </p>
      </div>
    </div>
  </div>
@endsection
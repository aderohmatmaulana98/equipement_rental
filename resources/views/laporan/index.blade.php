@extends('templates.base')

@section('content')

<div class="sidebar-backdrop" id="sidebar-backdrop"></div>
    <main class="app-wrapper">
        <div class="container-fluid">

            <div class="main-breadcrumb d-flex align-items-center my-3 position-relative">
                <h2 class="breadcrumb-title mb-0 flex-grow-1 fs-14">{{ $title }}</h2>
                <div class="flex-shrink-0">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-end mb-0">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">Pages</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
            @if(session('success'))
              <div class="alert alert-subtle-success d-flex align-items-center mb-2" id="success-alert" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
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
                {{ session('error') }}
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

            <div class="col-12">
                
                <form action="{{ route('laporan.index') }}" method="GET" class="mb-5">
                    <div class="mb-3">
                        <input class="form-control" type="date" name="tanggal_awal" required>
                    </div>
                    <div class="mb-3">
                        <input class="form-control" type="date" name="tanggal_akhir" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Filter</button>
                </form>

                @if(count($sewas))
                    <a href="{{ route('laporan.export.pdf', request()->query()) }}" class="btn btn-success">Export PDF</a>
                @endif
                
            </div>
        </div><!--End container-fluid-->
    </main><!--End app-wrapper-->

@endsection

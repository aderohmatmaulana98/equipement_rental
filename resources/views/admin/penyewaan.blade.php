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
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
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
                <div class="card">
                    <!--start::card-->
                   <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"> Table {{ $title }} </h5>
                        <a href="{{ route('admin.create_sewa') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Sewa Offline
                        </a>
                    </div>
                        
                    <div class="card-body table-responsive">
                        <!-- start:: Default Navbar -->
                        <table id="example" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No</th >
                                    <th>Kode Sewa</th >
                                    <th>Nama Penyewa</th>
                                    <th>Tanggal Sewa</th >
                                    <th>Tanggal Acara</th >
                                    <th>Alamat Acara</th >
                                    <th>Total Biaya</th >
                                    <th>Status</th >
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>                   
                                @foreach($sewas as $row)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $row->kode_sewa }}</td>
                                        <td>{{ $row->user->name ?? '-' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($row->tgl_sewa)->format('d/m/Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($row->tgl_acara)->format('d/m/Y') }}</td>
                                        <td>{{ Str::limit($row->alamat_acara, 30) }}</td>
                                        <td>Rp {{ number_format($row->total_biaya, 0, ',', '.') }}</td>
                                        <td>
                                            @if($row->status === 'belum bayar')
                                                <span class="badge bg-danger">Belum Bayar</span>
                                            @elseif($row->status === 'pending')
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @elseif($row->status === 'dp_lunas')
                                                <span class="badge bg-info">DP Lunas</span>
                                            @elseif($row->status === 'disetujui')
                                                <span class="badge bg-success">Lunas</span>
                                            @elseif($row->status === 'selesai')
                                                <span class="badge bg-primary">Selesai</span>
                                            @elseif($row->status === 'batal' || $row->status === 'dibatalkan')
                                                <span class="badge bg-secondary">Batal</span>
                                            @elseif($row->status === 'expired')
                                                <span class="badge bg-dark">Expired</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $row->status }}</span>
                                            @endif
                                        </td>   
                                        <td>
                                            <a href="{{ route('admin.sewa.detail', $row->id) }}" class="btn btn-primary btn-sm">Detail</a>
                                        </td>                             
                                    </tr>
                                @endforeach
                            </tbody>                           
                        </table>

                        <!-- end:: Default Navbar -->

                    </div>
                </div>
                <!--end::card-->
            </div>
        </div><!--End container-fluid-->
    </main><!--End app-wrapper-->

@endsection
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
                   <div class="card-header">
                        <h5 class="card-title mb-0"> Table {{ $title }} </h5>
                        <!-- Static Modal Button -->
                        <a href="{{ route('sewa.create') }}" type="button" class="btn btn-primary" >
                            + Tambah Sewa
                        </a>

                    </div>
                        
                    <div class="card-body table-responsive">
                        <!-- start:: Default Navbar -->
                        <table id="example" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No</th >
                                    <th>Kode Sewa</th >
                                    <th>Tanggal Sewa</th >
                                    <th>Tanggal Acara</th >
                                    <th>Alamat Acara</th >
                                    <th>Jam Acara</th >
                                    <th>Tanggal Loading</th >
                                    <th>Jam Loading</th >
                                    <th>Tanggal Loading Out</th >
                                    <th>Total Biaya</th >
                                    <th>Uang Muka</th >
                                    <th>Batas Konfirmasi</th >
                                    <th>Status</th >
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>                   
                                @foreach($sewas as $row)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $row->kode_sewa }}</td>
                                        <td>{{ $row->tgl_sewa }}</td>
                                        <td>{{ $row->tgl_acara }}</td>
                                        <td>{{ $row->alamat_acara }}</td>
                                        <td>{{ $row->jam_acara }}</td>
                                        <td>{{ $row->tgl_loading }}</td>
                                        <td>{{ $row->jam_loading }}</td>
                                        <td>{{ $row->tgl_loading_out }}</td>
                                        <td>{{ $row->total_biaya }}</td>
                                        <td>{{ $row->uang_muka }}</td>
                                        <td>{{ $row->batas_waktu_pembayaran }}</td>
                                        <td>
                                            @if(in_array($row->status, ['pending', 'disetujui', 'berjalan', 'selesai']))
                                                @if ($row->status === 'pending')
                                                    <span class="badge bg-danger">{{ $row->status }}</span>

                                                @elseif ($row->status === 'disetujui')
                                                    <span class="badge bg-success">{{ $row->status }}</span>

                                                @elseif ($row->status === 'berjalan')
                                                    <span class="badge bg-info">{{ $row->status }}</span>

                                                @elseif ($row->status === 'selesai')
                                                    <span class="badge bg-primary">{{ $row->status }}</span>
                                                    
                                                @endif
                                            @elseif($row->status === 'belum bayar' && $row->batas_waktu_pembayaran)
                                                <span class="badge bg-danger">Belum Bayar</span>                                             
                                            @elseif($row->status === 'batal' || $row->status === 'dibatalkan')
                                                <span class="badge bg-secondary">Batal</span>
                                            @else
                                                <span class="badge bg-secondary">Tidak diketahui</span>
                                            @endif
                                        </td>   
                                        <td>
                                            <a href="{{ route('warehouse.detail', $row->id) }}" class="btn btn-primary btn-sm">Detail</a>
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
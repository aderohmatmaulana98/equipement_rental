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

                   
                    </div>
                    <div class="card-body">
                        <!-- start:: Default Navbar -->
                        <table id="example" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No</th >
                                    <th>Jenis Barang</th >
                                    <th>Nama Barang</th >
                                    <th>Satuan</th >
                                    <th>Harga</th >
                                    <th>Keterangan</th >
                                    <th>Stok</th >
                                    <th>Gambar</th >
                                    <th>Status Ketersediaan</th>
                                </tr>
                            </thead>
                            <tbody>                   
                                @foreach($barangs as $row)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $row->jenis_barang }}</td>
                                        <td>{{ $row->nama_barang }}</td>
                                        <td>{{ $row->satuan }}</td>
                                        <td>{{ $row->harga }}</td>
                                        <td>{{ $row->keterangan }}</td>
                                        <td>{{ $row->stok }}</td>
                                        <td>
                                            <img src="{{ asset('storage/' . $row->gambar) }}" alt="gambar" height="100">
                                        </td>
                                        <td>
                                            @if ($row->stok > 0)
                                                <span class="badge bg-success">Tersedia</span>
                                            @else 
                                                <span class="badge bg-danger">Habis</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>No</th >
                                    <th>Jenis Barang</th >
                                    <th>Nama Barang</th >
                                    <th>Satuan</th >
                                    <th>Harga</th >
                                    <th>Keterangan</th >
                                    <th>Stok</th >
                                    <th>Gambar</th >
                                    <th>Aksi</th>
                                </tr>
                            </tfoot>
                        </table>

                        <!-- end:: Default Navbar -->

                    </div>
                </div>
                <!--end::card-->
            </div>
        </div><!--End container-fluid-->
    </main><!--End app-wrapper-->

@endsection
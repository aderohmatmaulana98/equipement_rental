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
               
                <div class="card card-h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Formulir Penyewaan</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-4">Silahkan isi data dibawah ini dengan benar dan teliti.</p>
                            <form action="{{ route('sewa.store') }}" method="POST">
                                @csrf
                                <div class="row g-4">
                                    <div class="col-xxl-6">
                                        <label for="tgl_sewa" class="form-label">Tanggal Sewa</label>
                                        <input type="date" class="form-control" id="tgl_sewa" name="tgl_sewa" placeholder="Tanggal Sewa" required>
                                    </div>
                                    <div class="col-xxl-6">
                                        <label for="tgl_acara" class="form-label">Tanggal Acara</label>
                                        <input type="date" class="form-control" id="tgl_acara" name="tgl_acara" placeholder="Tanggal Acara" required>
                                    </div>
                                    <div class="col-xxl-6">
                                        <label for="jam_acara" class="form-label">Jam Acara</label>
                                        <input type="time" class="form-control" id="jam_acara" name="jam_acara" placeholder="Jam Acara" required>
                                    </div>
                                    <div class="col-xxl-6">
                                        <label for="tgl_loading" class="form-label">Tanggal Loading</label>
                                        <input type="date" class="form-control" id="tgl_loading" name="tgl_loading" placeholder="Tanggal Loading" required>
                                    </div>
                                    <div class="col-xxl-6">
                                        <label for="jam_loading" class="form-label">Jam Loading</label>
                                        <input type="time" class="form-control" id="jam_loading" name="jam_loading" placeholder="Jam Loading" required>
                                    </div>
                                    <div class="col-xxl-6">
                                        <label for="tgl_loading_out" class="form-label">Tanggal Loading Out</label>
                                        <input type="date" class="form-control" id="tgl_loading_out" name="tgl_loading_out" placeholder="Tanggal Loading Out" required>
                                    </div>
                                    <div class="col-12">
                                        <label for="alamat_acara" class="form-label">Alamat Acara</label>
                                        <textarea class="form-control" id="alamat_acara" name="alamat_acara" rows="2" placeholder="Alamat Acara" required></textarea>
                                    </div>
                                    <div class="col-12">
                                        <table id="scroll-vertical" class="table table-nowrap table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama Barang</th>
                                                    <th>Stok</th>
                                                    <th>Harga</th>
                                                    <th>Satuan</th>
                                                    <th>Keterangan</th>
                                                    <th>Pilih</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Sample Data -->
                                                @foreach ($barangs as $barang )
                                                    @php
                                                        $i = 1;
                                                    @endphp
                                                    <tr>
                                                        <td>{{$i}}</td>
                                                        <td>{{ $barang->nama_barang }}</td>
                                                        <td>{{ $barang->stok }}</td>
                                                        <td>{{ $barang->harga }}</td>
                                                        <td>{{ $barang->satuan }}</td>
                                                        <td>{{ $barang->keterangan }}</td>
                                                        <td>
                                                             <input type="number" name="qty[{{ $barang->id }}]" class="form-control" min="1" max="{{ $barang->stok }}" value="1">
                                                            <input type="checkbox" name="barang_id[]" value="{{ $barang->id }}">
                                                        </td>
                                                    </tr> 
                                                    @php
                                                        $i=+1;
                                                    @endphp                                              
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <th>No</th>
                                                <th>Nama Barang</th>
                                                <th>Stok</th>
                                                <th>Harga</th>
                                                <th>Satuan</th>
                                                <th>Keterangan</th>
                                                <th>Pilih</th>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check mb-4">
                                            <input type="checkbox" class="form-check-input" id="termsLayout3" required>
                                            <label class="form-check-label" for="termsLayout3">I agree to the terms and conditions</label>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit Form</button>
                            </form>
                        </div>
                    </div>
                
            </div>
        </div><!--End container-fluid-->
    </main><!--End app-wrapper-->

@endsection

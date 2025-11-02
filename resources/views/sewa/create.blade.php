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
            @if (session('success'))
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

            @if (session('error'))
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

                        <div class="col-12">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nama Barang</th>
                                        <th>Stok</th>
                                        <th>Harga</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($barangs as $barang)
                                        <tr>
                                            <td>{{ $barang->nama_barang }}</td>
                                            <td>{{ $barang->stok }}</td>
                                            <td>Rp{{ number_format($barang->harga, 0, ',', '.') }}</td>
                                            <td>
                                                <form action="{{ route('cart.add') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="barang_id" value="{{ $barang->id }}">
                                                    <input type="number" name="qty" value="1" min="1"
                                                        max="{{ $barang->stok }}" style="width:60px;">
                                                    <button type="submit" class="btn btn-sm btn-primary">+
                                                        Keranjang</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <a href="{{ route('cart.show') }}" class="btn btn-success mt-3">Lihat Keranjang</a>
                        </div>
                    </div>
                </div>

            </div>
        </div><!--End container-fluid-->
    </main><!--End app-wrapper-->

@endsection

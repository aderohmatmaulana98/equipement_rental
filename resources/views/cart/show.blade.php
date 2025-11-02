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
                        <h5 class="card-title mb-0">{{ $title }}</h5>
                    </div>
                    <div class="card-body">
                        @if (empty($cart))
                            <p>Keranjang masih kosong.</p>
                        @else
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nama Barang</th>
                                        <th>Qty</th>
                                        <th>Harga</th>
                                        <th>Subtotal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $total = 0; @endphp
                                    @foreach ($cart as $item)
                                        @php
                                            $subtotal = $item['harga'] * $item['qty'];
                                            $total += $subtotal;
                                        @endphp
                                        <tr>
                                            <td>{{ $item['nama_barang'] }}</td>
                                            <td>{{ $item['qty'] }}</td>
                                            <td>Rp{{ number_format($item['harga'], 0, ',', '.') }}</td>
                                            <td>Rp{{ number_format($subtotal, 0, ',', '.') }}</td>
                                            <td>
                                                <form action="{{ route('cart.remove') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="barang_id" value="{{ $item['id'] }}">
                                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <h5 class="mt-3">Harga Sub Total / Hari: Rp{{ number_format($total, 0, ',', '.') }}</h5>

                            <form action="{{ route('checkout') }}" method="POST">
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
                                </div>
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary" id="checkout-button">Checkout</button>
                                    <a href="{{ route('sewa.create') }}" class="btn btn-secondary">Kembali</a>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>

            </div>
        </div><!--End container-fluid-->
    </main>

@endsection

@extends('templates.base')
@section('content')
    <div class="sidebar-backdrop" id="sidebar-backdrop"></div>
    <main class="app-wrapper">
        <div class="container-fluid">

            {{-- Breadcrumb --}}
            <div class="main-breadcrumb d-flex align-items-center my-3 position-relative">
                <div class="flex-shrink-0">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-end mb-0">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">Warehouse</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
                        </ol>
                    </nav>
                </div>
            </div>

            {{-- Header & Tanggal --}}
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card bg-primary text-white shadow-sm border-0">
                        <div class="card-body p-4 d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-1 fw-bold">Halo, {{ $user->name }}! 📦</h4>
                                <p class="mb-0 opacity-75">Berikut adalah ringkasan operasional gudang hari ini.</p>
                            </div>
                            <div class="text-end">
                                <h2 class="mb-0 fw-bold">{{ now()->locale('id')->isoFormat('D') }}</h2>
                                <span class="text-uppercase small">{{ now()->locale('id')->isoFormat('MMMM Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Statistik Operasional --}}
            <div class="row g-3 mb-4">
                {{-- 1. Perlu Persiapan --}}
                <div class="col-md-3 col-sm-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="flex-shrink-0 bg-warning bg-opacity-10 p-3 rounded-3 text-warning">
                                <i class="bi bi-box-seam fs-3"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted small mb-1">Perlu Persiapan</h6>
                                <h4 class="fw-bold mb-0">{{ $perluPersiapan }} <small class="fs-6 text-muted fw-normal">Sewa</small></h4>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. Sedang Disewa --}}
                <div class="col-md-3 col-sm-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="flex-shrink-0 bg-info bg-opacity-10 p-3 rounded-3 text-info">
                                <i class="bi bi-truck fs-3"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted small mb-1">Sedang Disewa</h6>
                                <h4 class="fw-bold mb-0">{{ $sedangDisewa }} <small class="fs-6 text-muted fw-normal">Sewa</small></h4>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. Loading Hari Ini --}}
                <div class="col-md-3 col-sm-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded-3 text-primary">
                                <i class="bi bi-arrow-up-circle fs-3"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted small mb-1">Barang Keluar Hari Ini</h6>
                                <h4 class="fw-bold mb-0">{{ $loadingHariIni->count() }} <small class="fs-6 text-muted fw-normal">Sewa</small></h4>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 4. Kembali Hari Ini --}}
                <div class="col-md-3 col-sm-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="flex-shrink-0 bg-success bg-opacity-10 p-3 rounded-3 text-success">
                                <i class="bi bi-arrow-down-circle fs-3"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted small mb-1">Barang Masuk Hari Ini</h6>
                                <h4 class="fw-bold mb-0">{{ $loadingOutHariIni->count() }} <small class="fs-6 text-muted fw-normal">Sewa</small></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                {{-- Kolom Kiri: Jadwal Aktivitas --}}
                <div class="col-xl-8">
                    {{-- Barang Keluar --}}
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                            <h5 class="card-title fw-bold mb-0 text-primary"><i class="bi bi-upload me-2"></i>Jadwal Loading Hari Ini</h5>
                            <small class="text-muted">{{ now()->locale('id')->isoFormat('dddd, D MMMM') }}</small>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4">No</th>
                                            <th>Kode Sewa</th>
                                            <th>Jam Loading</th>
                                            <th>Tujuan</th>
                                            <th class="text-end pe-4">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($loadingHariIni as $sewa)
                                            <tr>
                                                <td class="ps-4">{{ $loop->iteration }}</td>
                                                <td>
                                                    <span class="fw-bold">{{ $sewa->kode_sewa }}</span>
                                                    <br><small class="text-muted">{{ $sewa->user->name ?? '-' }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                                        <i class="bi bi-clock me-1"></i> {{ \Carbon\Carbon::parse($sewa->jam_loading)->format('H:i') }}
                                                    </span>
                                                </td>
                                                <td class="text-truncate" style="max-width: 200px;">{{ $sewa->alamat_acara }}</td>
                                                <td class="text-end pe-4">
                                                    <a href="{{ route('warehouse.detail', $sewa->id) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">
                                                    <i class="bi bi-check-circle fs-3 d-block mb-2"></i>
                                                    Tidak ada jadwal loading keluar hari ini.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Barang Masuk --}}
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                            <h5 class="card-title fw-bold mb-0 text-success"><i class="bi bi-download me-2"></i>Jadwal Pengembalian Hari Ini</h5>
                            <small class="text-muted">{{ now()->locale('id')->isoFormat('dddd, D MMMM') }}</small>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4">No</th>
                                            <th>Kode Sewa</th>
                                            <th>Tgl Loading Out</th>
                                            <th>Status Saat Ini</th>
                                            <th class="text-end pe-4">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($loadingOutHariIni as $sewa)
                                            <tr>
                                                <td class="ps-4">{{ $loop->iteration }}</td>
                                                <td>
                                                    <span class="fw-bold">{{ $sewa->kode_sewa }}</span>
                                                    <br><small class="text-muted">{{ $sewa->user->name ?? '-' }}</small>
                                                </td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($sewa->tgl_loading_out)->format('d M Y') }}
                                                </td>
                                                <td>
                                                    @if($sewa->status == 'selesai')
                                                        <span class="badge bg-success">Selesai</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark">Berjalan</span>
                                                    @endif
                                                </td>
                                                <td class="text-end pe-4">
                                                    <a href="{{ route('warehouse.detail', $sewa->id) }}" class="btn btn-sm btn-outline-success">Proses</a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">
                                                    <i class="bi bi-emoji-smile fs-3 d-block mb-2"></i>
                                                    Tidak ada jadwal pengembalian hari ini.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan: Stok & Info --}}
                <div class="col-xl-4">
                    {{-- Stok Menipis --}}
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-danger text-white border-0 py-3">
                            <h5 class="card-title fw-bold mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Stok Menipis</h5>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                @forelse($stokMenipis as $barang)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-bold">{{ $barang->nama_barang }}</div>
                                            <small class="text-muted">Kode: {{ $barang->kode_barang }}</small>
                                        </div>
                                        <span class="badge bg-danger rounded-pill">{{ $barang->stok }} Unit</span>
                                    </li>
                                @empty
                                    <li class="list-group-item text-center py-4 text-muted">
                                        <i class="bi bi-check-circle-fill text-success fs-3 d-block mb-2"></i>
                                        Stok barang aman.
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                        <div class="card-footer bg-white border-0 text-center">
                            <a href="{{ route('warehouse.list_barang') }}" class="btn btn-sm btn-outline-danger">Lihat Semua Barang</a>
                        </div>
                    </div>

                    {{-- Quick Links --}}
                     <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="card-title fw-bold mb-0"><i class="bi bi-link-45deg me-2"></i>Ases Cepat</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('warehouse.penyewaan') }}" class="btn btn-outline-primary text-start">
                                    <i class="bi bi-list-check me-2"></i> Daftar Semua Sewa
                                </a>
                                <a href="{{ route('warehouse.list_barang') }}" class="btn btn-outline-secondary text-start">
                                    <i class="bi bi-box-seam me-2"></i> Kelola Stok Barang
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div><!--End container-fluid-->
    </main>
@endsection

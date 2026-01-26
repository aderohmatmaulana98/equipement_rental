@extends('templates.base')

@section('content')

<div class="sidebar-backdrop" id="sidebar-backdrop"></div>
<main class="app-wrapper">
    <div class="container-fluid">

        {{-- Header --}}
        <div class="d-flex align-items-center justify-content-between my-4">
            <div>
                <h3 class="fw-bold mb-1">Laporan Bisnis</h3>
                <p class="text-muted mb-0">Filter dan export laporan transaksi penyewaan.</p>
            </div>
        </div>

        {{-- Filter Card --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form action="{{ route('laporan.index') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-uppercase">Tanggal Awal</label>
                        <input class="form-control" type="date" name="tanggal_awal" value="{{ request('tanggal_awal') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-uppercase">Tanggal Akhir</label>
                        <input class="form-control" type="date" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}" required>
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-filter me-1"></i> Tampilkan Laporan
                        </button>
                        @if(request('tanggal_awal') && count($sewas))
                            <a href="{{ route('laporan.export.pdf', request()->query()) }}" class="btn btn-danger">
                                <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        @if(request('tanggal_awal') && request('tanggal_akhir'))
            
            {{-- Summary Cards --}}
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card bg-primary text-white border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="text-uppercase small md-1 opacity-75">Total Transaksi</h6>
                            <h3 class="fw-bold mb-0">{{ $summary['total_transaksi'] }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="text-uppercase small md-1 opacity-75">Total Pendapatan</h6>
                            <h3 class="fw-bold mb-0">Rp {{ number_format($summary['total_pendapatan'], 0, ',', '.') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                     <div class="card bg-white border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="text-uppercase small md-1 text-muted">Aktivitas Periode</h6>
                            <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse(request('tanggal_awal'))->format('d M') }} - {{ \Carbon\Carbon::parse(request('tanggal_akhir'))->format('d M Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Result Table --}}
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title fw-bold mb-0">Rincian Transaksi</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">No</th>
                                    <th>Kode Sewa</th>
                                    <th>Tanggal Sewa</th>
                                    <th>Pelanggan</th>
                                    <th>Barang</th>
                                    <th>Status</th>
                                    <th class="text-end pe-4">Total Biaya</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sewas as $sewa)
                                <tr>
                                    <td class="ps-4">{{ $loop->iteration }}</td>
                                    <td>
                                        <span class="fw-bold">{{ $sewa->kode_sewa }}</span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($sewa->tgl_sewa)->format('d/m/Y') }}</td>
                                    <td>
                                        {{ $sewa->user->name ?? 'Deleted User' }}
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                        @foreach($sewa->detailSewas as $d)
                                            • {{ $d->barang->nama_barang ?? '-' }} ({{ $d->qty }})<br>
                                        @endforeach
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">
                                            {{ ucfirst($sewa->status) }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4 fw-bold">Rp {{ number_format($sewa->total_biaya, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="bi bi-search fs-1 d-block mb-3"></i>
                                        Tidak ada data yang ditemukan pada periode ini.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if(count($sewas))
                            <tfoot>
                                <tr class="bg-light fw-bold">
                                    <td colspan="6" class="text-end pe-3 py-3">TOTAL PENDAPATAN PERIODE INI</td>
                                    <td class="text-end pe-4 py-3 text-success fs-6">Rp {{ number_format($sewas->sum('total_biaya'), 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
            
        @else
            <div class="text-center py-5 text-muted">
                <i class="bi bi-calendar-range fs-1 d-block mb-3"></i>
                <p>Silakan pilih rentang tanggal untuk melihat laporan.</p>
            </div>
        @endif

    </div>
</main>
@endsection

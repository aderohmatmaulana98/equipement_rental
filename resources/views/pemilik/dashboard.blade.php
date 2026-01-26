@extends('templates.base')
@section('content')
<div class="sidebar-backdrop" id="sidebar-backdrop"></div>
<main class="app-wrapper">
    <div class="container-fluid">

        {{-- Welcome Header --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-white border-0 shadow-sm">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="fw-bold mb-1 text-dark">Executive Dashboard</h3>
                            <p class="text-muted mb-0">Overview kinerja bisnis & keuangan perusahaan.</p>
                        </div>
                        <div class="text-end d-none d-md-block">
                            <a href="{{ route('laporan.index') }}" class="btn btn-primary px-4">
                                <i class="bi bi-file-earmark-text me-2"></i>Lihat Laporan Detail
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Key Metrics - Financial Focus --}}
        <div class="row g-3 mb-4">
            <!-- Total Pendapatan -->
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100 border-start border-4 border-success">
                    <div class="card-body">
                        <h6 class="text-muted small text-uppercase fw-bold mb-2">Total Pendapatan (All Time)</h6>
                        <h3 class="fw-bold text-success mb-0">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
                        <div class="mt-2 text-muted small">
                            <i class="bi bi-arrow-up-right text-success"></i> Akumulasi pemasukan bersih
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pendapatan Bulan Ini -->
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100 border-start border-4 border-primary">
                    <div class="card-body">
                        <h6 class="text-muted small text-uppercase fw-bold mb-2">Pendapatan Bulan Ini</h6>
                        <h3 class="fw-bold text-primary mb-0">Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</h3>
                        <div class="mt-2 text-muted small">
                            Dari {{ $transaksiBulanIni }} transaksi sukses
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Transaksi -->
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100 border-start border-4 border-info">
                    <div class="card-body">
                        <h6 class="text-muted small text-uppercase fw-bold mb-2">Total Transaksi</h6>
                        <h3 class="fw-bold text-dark mb-0">{{ number_format($totalTransaksi) }}</h3>
                        <div class="mt-2 text-muted small">
                            Sejak awal beroperasi
                        </div>
                    </div>
                </div>
            </div>

            <!-- Aset Barang -->
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100 border-start border-4 border-warning">
                    <div class="card-body">
                        <h6 class="text-muted small text-uppercase fw-bold mb-2">Total Aset (Unit Barang)</h6>
                        <h3 class="fw-bold text-dark mb-0">{{ number_format($totalAsetBarang) }}</h3>
                        <div class="mt-2 text-muted small">
                            Unit barang siap sewa
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            {{-- Grafik Pendapatan --}}
            <div class="col-xl-8">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title fw-bold mb-0">Tren Pendapatan Bulanan</h5>
                        <small class="text-muted">Performansi keuangan 12 bulan terakhir</small>
                    </div>
                    <div class="card-body">
                         <div style="position: relative; height: 350px; width: 100%;">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Top Barang --}}
            <div class="col-xl-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title fw-bold mb-0">Top 5 Barang Terlaris</h5>
                        <small class="text-muted">Berdasarkan volume penyewaan</small>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @forelse($topBarang as $index => $item)
                            <div class="list-group-item d-flex justify-content-between align-items-center py-3 px-4">
                                <div class="d-flex align-items-center">
                                    <div class="badge bg-primary rounded-circle me-3" style="width:28px; height:28px; line-height:22px;">
                                        {{ $index + 1 }}
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold">{{ $item->barang->nama_barang }}</h6>
                                        <small class="text-muted">Disewa {{ $item->total_sewa }} kali</small>
                                    </div>
                                </div>
                                <span class="fw-bold text-success">
                                    <i class="bi bi-graph-up-arrow"></i>
                                </span>
                            </div>
                            @empty
                            <div class="text-center py-5 text-muted">
                                Belum ada data transaksi.
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

{{-- ChartJS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const labels = @json($bulanLabels);
        const data = @json($bulanData);

        const ctx = document.getElementById('revenueChart').getContext('2d');
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: data,
                    backgroundColor: '#198754',
                    borderRadius: 4,
                    hoverBackgroundColor: '#146c43'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) { label += ': '; }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [2, 4] },
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('id-ID', { notation: "compact", compactDisplay: "short" }).format(value);
                            }
                        }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    });
</script>
@endsection

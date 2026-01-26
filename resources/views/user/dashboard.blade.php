@extends('templates.base')

@section('content')
<div class="sidebar-backdrop" id="sidebar-backdrop"></div>
<main class="app-wrapper">
    <div class="container-fluid">

        {{-- Welcome Header --}}
        <div class="row mb-4 animate__animated animate__fadeIn">
            <div class="col-12">
                <div class="card bg-primary text-white shadow-sm border-0 overflow-hidden position-relative">
                    <div class="card-body p-4 position-relative z-1">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h3 class="fw-bold mb-1">Halo, {{ Auth::user()->name }}! 👋</h3>
                                <p class="mb-0 opacity-75">Selamat datang kembali di Dashboard Area Pelanggan.</p>
                            </div>
                            <div class="d-none d-md-block">
                                <span class="bg-white text-primary px-3 py-2 rounded-pill fw-bold small shadow-sm">
                                    <i class="bi bi-calendar-check me-1"></i> {{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistik Widgets --}}
        <div class="row g-3 mb-4">
            <!-- Total Sewa -->
            <div class="col-md-3 col-sm-6">
                <div class="card shadow-sm border-0 rounded-3 h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded-3 text-primary">
                            <i class="bi bi-receipt fs-3"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted small mb-1">Total Sewa</h6>
                            <h4 class="fw-bold mb-0">{{ $totalSewa }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Menunggu Pembayaran -->
            <div class="col-md-3 col-sm-6">
                <div class="card shadow-sm border-0 rounded-3 h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-shrink-0 bg-danger bg-opacity-10 p-3 rounded-3 text-danger">
                            <i class="bi bi-wallet2 fs-3"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted small mb-1">Menunggu Bayar</h6>
                            <h4 class="fw-bold mb-0">{{ $menungguPembayaran }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sewa Aktif -->
            <div class="col-md-3 col-sm-6">
                <div class="card shadow-sm border-0 rounded-3 h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-shrink-0 bg-warning bg-opacity-10 p-3 rounded-3 text-warning">
                            <i class="bi bi-hourglass-split fs-3"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted small mb-1">Sewa Aktif</h6>
                            <h4 class="fw-bold mb-0">{{ $sewaAktif }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Pengeluaran -->
            <div class="col-md-3 col-sm-6">
                <div class="card shadow-sm border-0 rounded-3 h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-shrink-0 bg-success bg-opacity-10 p-3 rounded-3 text-success">
                            <i class="bi bi-cash-stack fs-3"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted small mb-1">Total Pengeluaran</h6>
                            <h4 class="fw-bold mb-0 fs-5">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            {{-- Grafik Pengeluaran --}}
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title fw-bold mb-0"><i class="bi bi-bar-chart-line me-2"></i>Pengeluaran 6 Bulan Terakhir</h5>
                    </div>
                    <div class="card-body">
                        <div style="position: relative; height: 300px; width: 100%;">
                            <canvas id="pengeluaranChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Action --}}
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title fw-bold mb-0"><i class="bi bi-lightning-charge me-2"></i>Aksi Cepat</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-3">
                            <a href="{{ route('user.list_barang') }}" class="btn btn-primary btn-lg p-3 text-start">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-plus-circle fs-3 me-3"></i>
                                    <div>
                                        <div class="fw-bold">Sewa Barang Baru</div>
                                        <small class="opacity-75">Cari dan sewa peralatan sekarang</small>
                                    </div>
                                </div>
                            </a>
                            <a href="{{ route('sewa.index') }}" class="btn btn-outline-secondary btn-lg p-3 text-start">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-list-check fs-3 me-3"></i>
                                    <div>
                                        <div class="fw-bold">Lihat Semua Sewa</div>
                                        <small class="opacity-75">Cek riwayat dan status pesanan</small>
                                    </div>
                                </div>
                            </a>
                             <a href="{{ route('cart.show') }}" class="btn btn-outline-info btn-lg p-3 text-start">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-cart3 fs-3 me-3"></i>
                                    <div>
                                        <div class="fw-bold">Keranjang Belanja</div>
                                        <small class="opacity-75">Lihat barang yang akan disewa</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Riwayat Terbaru --}}
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold mb-0"><i class="bi bi-clock-history me-2"></i>Transaksi Terbaru</h5>
                <a href="{{ route('sewa.index') }}" class="btn btn-sm btn-light text-primary fw-semibold">Lihat Semua <i class="bi bi-arrow-right ms-1"></i></a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Kode Sewa</th>
                                <th>Tanggal Acara</th>
                                <th>Total Biaya</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sewas as $sewa)
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-bold text-primary">{{ $sewa->kode_sewa }}</span>
                                    <br> <small class="text-muted">{{ $sewa->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($sewa->tgl_acara)->format('d M Y') }}
                                </td>
                                <td>Rp {{ number_format($sewa->total_biaya, 0, ',', '.') }}</td>
                                <td>
                                    @if($sewa->status == 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($sewa->status == 'disetujui')
                                        <span class="badge bg-success">Disetujui</span>
                                    @elseif($sewa->status == 'berjalan')
                                        <span class="badge bg-info">Berjalan</span>
                                    @elseif($sewa->status == 'selesai')
                                        <span class="badge bg-primary">Selesai</span>
                                    @elseif($sewa->status == 'belum bayar')
                                        <span class="badge bg-danger">Belum Bayar</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $sewa->status }}</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('sewa.show', $sewa->id) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                    Belum ada riwayat transaksi.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</main>

{{-- ChartJS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data dari controller
        const labels = @json($bulanLabels);
        const data = @json($bulanData);

        const ctx = document.getElementById('pengeluaranChart').getContext('2d');
        
        // Gradient
        let gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(37, 99, 235, 0.2)');
        gradient.addColorStop(1, 'rgba(37, 99, 235, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pengeluaran (Rp)',
                    data: data,
                    backgroundColor: gradient,
                    borderColor: '#2563eb',
                    borderWidth: 2,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#2563eb',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
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
                        grid: {
                            borderDash: [5, 5],
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            callback: function(value, index, values) {
                                return new Intl.NumberFormat('id-ID', { notation: "compact" }).format(value);
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>
@endsection

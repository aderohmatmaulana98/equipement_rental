@extends('templates.base')

@section('content')
<div class="sidebar-backdrop" id="sidebar-backdrop"></div>
<main class="app-wrapper">
    <div class="container-fluid">

        {{-- Welcome Header --}}
        <div class="d-flex align-items-center justify-content-between my-4">
            <div>
                <h3 class="fw-bold mb-1">Dashboard Admin</h3>
                <p class="text-muted mb-0">Ringkasan statistik dan aktivitas terbaru.</p>
            </div>
            <div>
                <a href="{{ route('admin.create_sewa') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Buat Sewa Baru
                </a>
            </div>
        </div>

        {{-- Row 1: Statistik Utama --}}
        <div class="row g-3 mb-4">
            {{-- Total Pendapatan --}}
            <div class="col-md-3 col-sm-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0 bg-success bg-opacity-10 p-3 rounded-3 text-success">
                                <i class="bi bi-cash-coin fs-3"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted small mb-1">Total Pendapatan</h6>
                                <h4 class="fw-bold mb-0">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h4>
                            </div>
                        </div>
                        <small class="text-muted">
                            <i class="bi bi-calendar-event me-1"></i> Bulan ini: 
                            <span class="text-success fw-bold">Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</span>
                        </small>
                    </div>
                </div>
            </div>

            {{-- Sewa Aktif --}}
            <div class="col-md-3 col-sm-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-0">
                            <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded-3 text-primary">
                                <i class="bi bi-clipboard-data fs-3"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted small mb-1">Sewa Aktif</h6>
                                <h4 class="fw-bold mb-0">{{ $sewaAktif }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

             {{-- Menunggu Pembayaran --}}
             <div class="col-md-3 col-sm-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-0">
                            <div class="flex-shrink-0 bg-warning bg-opacity-10 p-3 rounded-3 text-warning">
                                <i class="bi bi-wallet2 fs-3"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted small mb-1">Menunggu Bayar</h6>
                                <h4 class="fw-bold mb-0">{{ $menungguPembayaran }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Total Pelanggan --}}
            <div class="col-md-3 col-sm-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-0">
                            <div class="flex-shrink-0 bg-info bg-opacity-10 p-3 rounded-3 text-info">
                                <i class="bi bi-people fs-3"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted small mb-1">Total Pelanggan</h6>
                                <h4 class="fw-bold mb-0">{{ $totalPenyewa }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            {{-- Grafik Pendapatan --}}
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title fw-bold mb-0">Grafik Pendapatan</h5>
                        <small class="text-muted">6 Bulan Terakhir</small>
                    </div>
                    <div class="card-body">
                         <div style="position: relative; height: 300px; width: 100%;">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Links / Status --}}
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title fw-bold mb-0">Status Sistem</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span>Total Barang</span>
                                <span class="badge bg-secondary rounded-pill">{{ $totalBarang }} Unit</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span>Total Transaksi (All Time)</span>
                                <span class="badge bg-secondary rounded-pill">{{ $totalSewa }}</span>
                            </li>
                        </ul>
                        
                        <hr>
                        <h6 class="fw-bold mb-3">Menu Cepat</h6>
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.penyewaan') }}" class="btn btn-outline-primary text-start">
                                <i class="bi bi-list-check me-2"></i> Kelola Penyewaan
                            </a>
                            <a href="{{ route('customer.customer') }}" class="btn btn-outline-secondary text-start">
                                <i class="bi bi-people me-2"></i> Data Pelanggan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Transaksi Terbaru --}}
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold mb-0">Transaksi Terbaru</h5>
                <a href="{{ route('admin.penyewaan') }}" class="btn btn-sm btn-light text-primary fw-semibold">Lihat Semua <i class="bi bi-arrow-right ms-1"></i></a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Invoice</th>
                                <th>Pelanggan</th>
                                <th>Tanggal</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sewaTerbaru as $sewa)
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-bold text-primary">{{ $sewa->kode_sewa }}</span>
                                </td>
                                <td>
                                    {{ $sewa->user->name ?? 'User Terhapus' }}
                                    <br><small class="text-muted">{{ $sewa->user->no_hp ?? '-' }}</small>
                                </td>
                                <td>
                                    {{ $sewa->created_at->format('d M Y') }}
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
                                    <a href="{{ route('admin.sewa.detail', $sewa->id) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    Belum ada transaksi.
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
        const labels = @json($bulanLabels);
        const data = @json($bulanData);

        const ctx = document.getElementById('revenueChart').getContext('2d');
        
        // Gradient Fill
        let gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(25, 135, 84, 0.2)');
        gradient.addColorStop(1, 'rgba(25, 135, 84, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: data,
                    backgroundColor: gradient,
                    borderColor: '#198754',
                    borderWidth: 2,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#198754',
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

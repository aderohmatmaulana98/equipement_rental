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
                            <li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
                        </ol>
                    </nav>
                </div>
            </div>

            {{-- Statistik Utama --}}
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card text-center shadow-sm border-0 rounded-3 p-3">
                        <h6 class="text-muted mb-2">Total Biaya</h6>
                        <h3 class="fw-bold text-primary mb-0">
                            Rp {{ number_format($totalBiaya, 0, ',', '.') }}
                        </h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center shadow-sm border-0 rounded-3 p-3">
                        <h6 class="text-muted mb-2">Sudah Dibayar (DP)</h6>
                        <h3 class="fw-bold text-success mb-0">
                            Rp {{ number_format($totalUangMuka, 0, ',', '.') }}
                        </h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center shadow-sm border-0 rounded-3 p-3">
                        <h6 class="text-muted mb-2">Sisa Pembayaran</h6>
                        <h3 class="fw-bold text-danger mb-0">
                            Rp {{ number_format($totalSisa, 0, ',', '.') }}
                        </h3>
                    </div>
                </div>
            </div>

            {{-- Grafik --}}
            <div class="row g-3">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Grafik Stok Barang</h4>
                        </div>
                        <div class="card-body">
                            <div id="stokBarangChart"></div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-0 pb-0">
                            <h5 class="card-title fw-semibold mb-0">Grafik Status Pembayaran</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="chartPembayaran" height="130"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Daftar Barang --}}
            <div class="card shadow-sm border-0 mt-4">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-semibold mb-0">📦 Daftar Barang yang Disewa</h5>
                    <small class="text-muted">Total: {{ $sewas->count() }} transaksi</small>
                </div>

                <div class="card-body">
                    @forelse($sewas as $index => $sewa)
                        <div class="border-start border-3 border-primary ps-3 mb-4 position-relative">
                            <div class="position-absolute top-0 start-0 translate-middle bg-primary rounded-circle"
                                style="width: 12px; height: 12px;"></div>

                            <div class="d-flex justify-content-between align-items-start flex-wrap">
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold text-primary mb-1">
                                        <i class="bi bi-receipt"></i> {{ $sewa->kode_sewa }}
                                    </h6>
                                    <p class="small text-muted mb-1">
                                        <strong><i class="bi bi-calendar-event"></i> Acara:</strong>
                                        {{ \Carbon\Carbon::parse($sewa->tgl_acara)->format('d M Y') }}
                                        |
                                        <strong><i class="bi bi-geo-alt"></i> Lokasi:</strong>
                                        {{ $sewa->alamat_acara }}
                                    </p>
                                    <p class="small text-muted mb-1">
                                        <strong><i class="bi bi-truck"></i> Loading:</strong>
                                        {{ \Carbon\Carbon::parse($sewa->tgl_loading)->format('d M Y') }} →
                                        {{ \Carbon\Carbon::parse($sewa->tgl_loading_out)->format('d M Y') }}
                                    </p>
                                    <p class="small mb-0">
                                        <strong><i class="bi bi-cash-coin"></i> Total:</strong>
                                        <span class="text-dark fw-semibold">Rp
                                            {{ number_format($sewa->total_biaya, 0, ',', '.') }}</span><br>
                                        <strong><i class="bi bi-wallet2"></i> DP:</strong>
                                        <span class="text-success fw-semibold">Rp
                                            {{ number_format($sewa->uang_muka, 0, ',', '.') }}</span>
                                    </p>
                                </div>

                                <div class="text-end mt-2 mt-sm-0">
                                    @switch($sewa->status)
                                        @case('pending')
                                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">
                                                <i class="bi bi-hourglass-split me-1"></i> Pending
                                            </span>
                                        @break

                                        @case('disetujui')
                                            <span class="badge bg-success px-3 py-2 rounded-pill">
                                                <i class="bi bi-check-circle me-1"></i> Disetujui
                                            </span>
                                        @break

                                        @case('berjalan')
                                            <span class="badge bg-info text-dark px-3 py-2 rounded-pill">
                                                <i class="bi bi-truck me-1"></i> Berjalan
                                            </span>
                                        @break

                                        @case('selesai')
                                            <span class="badge bg-primary px-3 py-2 rounded-pill">
                                                <i class="bi bi-flag me-1"></i> Selesai
                                            </span>
                                        @break

                                        @case('batal')
                                        @case('dibatalkan')
                                            <span class="badge bg-secondary px-3 py-2 rounded-pill">
                                                <i class="bi bi-x-circle me-1"></i> Batal
                                            </span>
                                        @break

                                        @default
                                            <span class="badge bg-light text-dark px-3 py-2 rounded-pill">
                                                <i class="bi bi-question-circle me-1"></i> Tidak Diketahui
                                            </span>
                                    @endswitch
                                </div>
                            </div>

                            {{-- Barang --}}
                            @if ($sewa->barang && $sewa->barang->count())
                                <button class="btn btn-sm btn-outline-primary mt-3" data-bs-toggle="collapse"
                                    data-bs-target="#barangList{{ $index }}" aria-expanded="false"
                                    aria-controls="barangList{{ $index }}">
                                    <i class="bi bi-box-seam"></i> Lihat Barang
                                </button>

                                <div class="collapse mt-2" id="barangList{{ $index }}">
                                    <ul class="list-group list-group-flush border rounded-3 small mt-2">
                                        @foreach ($sewa->barang as $barang)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span><i class="bi bi-caret-right-fill text-primary me-1"></i>
                                                    {{ $barang->nama_barang }}</span>
                                                <span class="badge bg-light text-dark border">
                                                    Stok: {{ $barang->stok ?? '-' }}
                                                </span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                        @empty
                            <p class="text-muted text-center mb-0">Belum ada penyewaan barang.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Tambahkan ini di layout utama bila belum ada --}}

        </main>

        {{-- ChartJS --}}
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Grafik Pembayaran
            new Chart(document.getElementById('chartPembayaran'), {
                type: 'doughnut',
                data: {
                    labels: ['Total Biaya', 'DP (Sudah Dibayar)', 'Sisa Pembayaran'],
                    datasets: [{
                        data: [{{ $totalBiaya }}, {{ $totalUangMuka }}, {{ $totalSisa }}],
                        backgroundColor: ['#007bff', '#28a745', '#dc3545'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const isDarkMode = document.documentElement.classList.contains('dark');

                // Ambil data dari controller
                const barangLabels = @json($barangs->pluck('nama_barang'));
                const barangStok = @json($barangs->pluck('stok'));

                const options = {
                    chart: {
                        type: 'bar',
                        height: 400,
                        toolbar: {
                            show: false
                        },
                        foreColor: isDarkMode ? '#e5e7eb' : '#333', // warna teks global
                        fontFamily: 'inherit',
                        background: 'transparent'
                    },
                    series: [{
                        name: 'Stok Barang',
                        data: barangStok
                    }],
                    xaxis: {
                        categories: barangLabels,
                        title: {
                            text: 'Nama Barang',
                            style: {
                                fontWeight: 600,
                                fontSize: '14px',
                                color: isDarkMode ? '#e5e7eb' : '#333'
                            }
                        },
                        labels: {
                            rotate: -45,
                            style: {
                                fontSize: '12px',
                                colors: isDarkMode ? '#cbd5e1' : '#333'
                            }
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'Jumlah Stok',
                            style: {
                                fontWeight: 600,
                                fontSize: '14px',
                                color: isDarkMode ? '#e5e7eb' : '#333'
                            }
                        },
                        labels: {
                            style: {
                                colors: isDarkMode ? '#cbd5e1' : '#333'
                            }
                        }
                    },
                    colors: ['#01a54d'],
                    dataLabels: {
                        enabled: true,
                        style: {
                            colors: [isDarkMode ? '#000' : '#fff'] // teks data label adaptif
                        },
                        background: {
                            enabled: true,
                            foreColor: isDarkMode ? '#000' : '#fff',
                            borderRadius: 4,
                            opacity: 0.9,
                        }
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 6,
                            columnWidth: '45%',
                            distributed: true
                        }
                    },
                    grid: {
                        borderColor: isDarkMode ? '#374151' : '#f1f1f1',
                        strokeDashArray: 4
                    },
                    tooltip: {
                        theme: isDarkMode ? 'dark' : 'light',
                        y: {
                            formatter: function(val) {
                                return val + " unit";
                            }
                        }
                    },
                    title: {
                        text: 'Distribusi Stok Barang',
                        align: 'center',
                        margin: 10,
                        style: {
                            fontSize: '16px',
                            fontWeight: 'bold',
                            color: isDarkMode ? '#f3f4f6' : '#111'
                        }
                    }
                };

                const chart = new ApexCharts(document.querySelector("#stokBarangChart"), options);
                chart.render();
            });
        </script>
    @endsection

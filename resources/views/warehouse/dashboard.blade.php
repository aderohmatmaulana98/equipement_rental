@extends('templates.base')
@section('content')
    <div class="sidebar-backdrop" id="sidebar-backdrop"></div>
    <main class="app-wrapper">
        <div class="container-fluid">

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
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card shadow-sm p-3">
                                        <h6 class="text-muted">Total Biaya</h6>
                                        <h3 class="fw-bold text-primary">Rp {{ number_format($totalBiaya, 0, ',', '.') }}
                                        </h3>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card shadow-sm p-3">
                                        <h6 class="text-muted">Sudah Dibayar</h6>
                                        <h3 class="fw-bold text-success">Rp {{ number_format($totalUangMuka, 0, ',', '.') }}
                                        </h3>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card shadow-sm p-3">
                                        <h6 class="text-muted">Sisa Pembayaran</h6>
                                        <h3 class="fw-bold text-danger">Rp {{ number_format($totalSisa, 0, ',', '.') }}</h3>
                                    </div>
                                </div>
                            </div>

                            <div class="row gy-4 gx-4">

                                @if ($user->role_id == 4)
                                    <!-- Total Warehouse -->
                                    <div class="col-md-6 col-xl-4">
                                        <div class="card border-0 shadow-sm rounded-3">
                                            <div class="card-body text-center py-4">
                                                <div
                                                    class="h-50px w-50px mx-auto mb-3 d-flex justify-content-center align-items-center bg-danger-subtle text-danger rounded-2 fs-3">
                                                    <i class="bi bi-box-seam"></i>
                                                </div>
                                                <h2 class="mb-1 fs-24 fw-semibold">{{ $totalWarehouse }}</h2>
                                                <p class="text-muted fs-16 mb-0">Total Warehouse</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Total Barang -->
                                    <div class="col-md-6 col-xl-4">
                                        <div class="card border-0 shadow-sm rounded-3">
                                            <div class="card-body text-center py-4">
                                                <div
                                                    class="h-50px w-50px mx-auto mb-3 d-flex justify-content-center align-items-center bg-info-subtle text-info rounded-2 fs-3">
                                                    <i class="bi bi-box-fill"></i>
                                                </div>
                                                <h2 class="mb-1 fs-24 fw-semibold">{{ $totalBarang }}</h2>
                                                <p class="text-muted fs-16 mb-0">Total Barang</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Total Jenis Barang -->
                                    <div class="col-md-6 col-xl-4">
                                        <div class="card border-0 shadow-sm rounded-3">
                                            <div class="card-body text-center py-4">
                                                <div
                                                    class="h-50px w-50px mx-auto mb-3 d-flex justify-content-center align-items-center bg-secondary-subtle text-secondary rounded-2 fs-3">
                                                    <i class="bi bi-tags-fill"></i>
                                                </div>
                                                <h2 class="mb-1 fs-24 fw-semibold">{{ $totalJenisBarang }}</h2>
                                                <p class="text-muted fs-16 mb-0">Total Jenis Barang</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
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

                <div class="col-xl-4 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h4>Grafik Status Pembayaran</h4>
                        </div>
                        <div class="card-body">
                            <div id="statusSewaChart"></div>
                        </div>
                    </div>
                </div>




                <!-- Load Chart.js -->
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const ctx = document.getElementById('pembayaranChart');

                        new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: ['Total Biaya', 'Sudah Dibayar', 'Sisa Pembayaran'],
                                datasets: [{
                                    label: 'Jumlah (Rp)',
                                    data: [{{ $totalBiaya }}, {{ $totalUangMuka }}, {{ $totalSisa }}],
                                    backgroundColor: [
                                        'rgba(54, 162, 235, 0.7)',
                                        'rgba(75, 192, 192, 0.7)',
                                        'rgba(255, 99, 132, 0.7)'
                                    ],
                                    borderColor: [
                                        'rgba(54, 162, 235, 1)',
                                        'rgba(75, 192, 192, 1)',
                                        'rgba(255, 99, 132, 1)'
                                    ],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        display: false
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            callback: function(value) {
                                                return 'Rp ' + value.toLocaleString('id-ID');
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    });
                </script>


            </div>
        </div><!--End container-fluid-->
    </main>

    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>


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


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusCounts = @json($statusCounts);

            const labels = Object.keys(statusCounts);
            const data = Object.values(statusCounts);

            const options = {
                chart: {
                    type: 'donut',
                    height: 400,
                    toolbar: {
                        show: false
                    },
                    foreColor: '#333',
                },
                series: data,
                labels: labels,
                colors: ['#dc3545', '#198754', '#0dcaf0', '#0d6efd', '#6c757d', '#ffc107'],
                legend: {
                    position: 'bottom',
                    labels: {
                        colors: 'inherit'
                    }
                },
                dataLabels: {
                    enabled: true,
                    style: {
                        fontSize: '14px',
                        colors: ['#fff']
                    },
                    dropShadow: {
                        enabled: false
                    }
                },
                title: {
                    text: 'Distribusi Status Sewa',
                    align: 'center',
                    style: {
                        fontSize: '18px',
                        fontWeight: 'bold'
                    }
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 300
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            const chart = new ApexCharts(document.querySelector("#statusSewaChart"), options);
            chart.render();
        });
    </script>
@endsection

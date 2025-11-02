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
                            <div class="row gy-4 gx-4">

                                <!-- Pemilik -->
                                <div class="col-md-6 col-xl-3">
                                    <div class="card border-0 shadow-sm rounded-3">
                                        <div class="card-body text-center py-4">
                                            <div
                                                class="h-50px w-50px mx-auto mb-3 d-flex justify-content-center align-items-center bg-success-subtle text-success rounded-2 fs-3">
                                                <i class="bi bi-person-badge"></i>
                                            </div>
                                            <h2 class="mb-1 fs-24 fw-semibold">{{ $totalPemilik }}</h2>
                                            <p class="text-muted fs-16 mb-0">Total Pemilik</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Admin -->
                                <div class="col-md-6 col-xl-3">
                                    <div class="card border-0 shadow-sm rounded-3">
                                        <div class="card-body text-center py-4">
                                            <div
                                                class="h-50px w-50px mx-auto mb-3 d-flex justify-content-center align-items-center bg-primary-subtle text-primary rounded-2 fs-3">
                                                <i class="bi bi-person-gear"></i>
                                            </div>
                                            <h2 class="mb-1 fs-24 fw-semibold">{{ $totalAdmin }}</h2>
                                            <p class="text-muted fs-16 mb-0">Total Admin</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Penyewa -->
                                <div class="col-md-6 col-xl-3">
                                    <div class="card border-0 shadow-sm rounded-3">
                                        <div class="card-body text-center py-4">
                                            <div
                                                class="h-50px w-50px mx-auto mb-3 d-flex justify-content-center align-items-center bg-warning-subtle text-warning rounded-2 fs-3">
                                                <i class="ri-user-line"></i>
                                            </div>
                                            <h2 class="mb-1 fs-24 fw-semibold">{{ $totalPenyewa }}</h2>
                                            <p class="text-muted fs-16 mb-0">Total Penyewa</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Warehouse -->
                                <div class="col-md-6 col-xl-3">
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

                            </div>

                            <!-- Baris kedua -->
                            <div class="row gy-4 gx-4 mt-3">

                                <!-- Total Barang -->
                                <div class="col-md-6 col-xl-6">
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
                                <div class="col-md-6 col-xl-6">
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

                            </div>
                        </div>



                    </div>
                </div>
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Grafik Stok Barang</h4>
                        </div>
                        <div class="card-body">
                            <div id="stokBarangChart"></div>
                        </div>
                    </div>
                </div>

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
@endsection

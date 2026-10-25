@extends('templates.base')

@section('content')

    <div class="sidebar-backdrop" id="sidebar-backdrop"></div>
    <main class="app-wrapper">
        <div class="container-fluid py-3">

            {{-- 1. Stepper Alur Penyewaan --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-3 p-md-4">
                    <div class="row align-items-center gy-3">
                        <div class="col-lg-7">
                            <h4 class="fw-bold text-primary mb-1">
                                <i class="ri-store-2-line me-2"></i>Katalog Perlengkapan Sewa
                            </h4>
                            <p class="text-muted mb-0 fs-13">Pilih alat dan perlengkapan terbaik yang Anda butuhkan untuk acara Anda.</p>
                        </div>
                        <div class="col-lg-5">
                            {{-- Visual Steps --}}
                            <div class="d-flex align-items-center justify-content-lg-end gap-2 text-center fs-12">
                                <div class="step-item active-step px-3 py-2 rounded-3 bg-primary text-white shadow-sm">
                                    <span class="fw-bold">1. Pilih Alat</span>
                                </div>
                                <i class="bi bi-chevron-right text-muted"></i>
                                <div class="step-item px-3 py-2 rounded-3 bg-light text-muted">
                                    <span>2. Jadwal & Lokasi</span>
                                </div>
                                <i class="bi bi-chevron-right text-muted"></i>
                                <div class="step-item px-3 py-2 rounded-3 bg-light text-muted">
                                    <span>3. Pembayaran</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="alert alert-subtle-success d-flex align-items-center mb-3 alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-subtle-danger d-flex align-items-center mb-3 alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- 2. Filter & Toolbar --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-3">
                    <div class="row g-2 align-items-center">
                        {{-- Search Input --}}
                        <div class="col-12 col-md-5">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted">
                                    <i class="ri-search-line"></i>
                                </span>
                                <input type="text" id="searchInput" class="form-control border-start-0 ps-0" placeholder="Cari nama alat, sound, panggung, tenda...">
                                <button class="btn btn-outline-secondary border-start-0 bg-white text-muted" type="button" id="clearSearch" style="display: none;">
                                    <i class="bi bi-x-circle-fill"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Sort Options --}}
                        <div class="col-6 col-md-3">
                            <select id="sortSelect" class="form-select">
                                <option value="default">Urutan Default</option>
                                <option value="price-asc">Harga: Termurah</option>
                                <option value="price-desc">Harga: Tertinggi</option>
                                <option value="stock-desc">Stok: Terbanyak</option>
                                <option value="name-asc">Nama (A - Z)</option>
                            </select>
                        </div>

                        {{-- View Toggle & Cart Button --}}
                        <div class="col-6 col-md-4 d-flex justify-content-end gap-2">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-primary active" id="btnViewGrid" title="Tampilan Kartu">
                                    <i class="ri-grid-fill"></i>
                                </button>
                                <button type="button" class="btn btn-outline-primary" id="btnViewTable" title="Tampilan Tabel">
                                    <i class="ri-list-check"></i>
                                </button>
                            </div>

                            <button type="button" class="btn btn-primary d-flex align-items-center gap-2 position-relative" data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas">
                                <i class="ri-shopping-cart-2-line fs-16"></i>
                                <span class="d-none d-sm-inline">Keranjang</span>
                                <span class="badge bg-danger rounded-pill cart-count-badge">
                                    {{ count($cart) }}
                                </span>
                            </button>
                        </div>
                    </div>

                    {{-- Category Pills --}}
                    <div class="d-flex align-items-center gap-2 mt-3 overflow-auto pb-1" id="categoryTabs">
                        <button type="button" class="btn btn-sm rounded-pill btn-category active" data-category="all">
                            Semua Kategori <span class="badge bg-secondary-subtle text-secondary ms-1">{{ count($barangs) }}</span>
                        </button>
                        <button type="button" class="btn btn-sm rounded-pill btn-category" data-category="promo">
                            🔥 Sedang Promo
                        </button>
                        @foreach ($categories as $cat)
                            @php
                                $countInCat = $barangs->where('jenis_barang_id', $cat->id)->count();
                            @endphp
                            @if ($countInCat > 0)
                                <button type="button" class="btn btn-sm rounded-pill btn-category" data-category="{{ $cat->id }}">
                                    {{ $cat->nama_jenis_barang }} <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $countInCat }}</span>
                                </button>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- 3. Grid Card View --}}
            <div id="gridViewContainer" class="row g-3">
                @foreach ($barangs as $barang)
                    @php
                        $isPromo = $barang->isDiskonAktif();
                        $hargaFinal = $barang->getHargaDiskon();
                        $inCart = isset($cart[$barang->id]);
                        $qtyInCart = $inCart ? $cart[$barang->id]['qty'] : 0;
                    @endphp
                    <div class="col-12 col-sm-6 col-lg-4 col-xl-3 product-item"
                         data-name="{{ strtolower($barang->nama_barang) }}"
                         data-category="{{ $barang->jenis_barang_id }}"
                         data-promo="{{ $isPromo ? '1' : '0' }}"
                         data-price="{{ $hargaFinal }}"
                         data-stock="{{ $barang->stok }}">
                        <div class="card h-100 border-0 shadow-sm product-card transition-all position-relative">
                            
                            {{-- Image Container --}}
                            <div class="position-relative overflow-hidden product-image-wrapper rounded-top">
                                @if ($barang->gambar && file_exists(public_path('storage/' . $barang->gambar)))
                                    <img src="{{ asset('storage/' . $barang->gambar) }}" alt="{{ $barang->nama_barang }}" class="card-img-top product-img">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center product-img-placeholder">
                                        <i class="ri-image-line text-muted display-6"></i>
                                    </div>
                                @endif

                                {{-- Top Badges --}}
                                <div class="position-absolute top-0 start-0 m-2 d-flex flex-column gap-1">
                                    @if ($barang->jenisBarang)
                                        <span class="badge bg-dark bg-opacity-75 backdrop-blur fs-11">
                                            {{ $barang->jenisBarang->nama_jenis_barang }}
                                        </span>
                                    @endif
                                    @if ($isPromo)
                                        <span class="badge bg-danger shadow-sm fs-11">
                                            @if ($barang->diskon_persen)
                                                PROMO -{{ $barang->diskon_persen }}%
                                            @else
                                                PROMO DISKON
                                            @endif
                                        </span>
                                    @endif
                                </div>

                                {{-- Stock Badge --}}
                                <div class="position-absolute bottom-0 end-0 m-2">
                                    @if ($barang->stok > 5)
                                        <span class="badge bg-success bg-opacity-90 fs-11">Stok: {{ $barang->stok }}</span>
                                    @elseif ($barang->stok > 0)
                                        <span class="badge bg-warning text-dark fs-11">Sisa {{ $barang->stok }}</span>
                                    @else
                                        <span class="badge bg-danger fs-11">Habis</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Card Body --}}
                            <div class="card-body p-3 d-flex flex-column">
                                <h6 class="card-title fw-bold text-dark mb-1 product-title" title="{{ $barang->nama_barang }}">
                                    {{ $barang->nama_barang }}
                                </h6>

                                @if ($barang->keterangan)
                                    <p class="text-muted fs-12 mb-3 product-desc text-truncate-2">
                                        {{ $barang->keterangan }}
                                    </p>
                                @else
                                    <p class="text-muted fs-12 mb-3 fst-italic">Kualitas terjamin untuk acara Anda</p>
                                @endif

                                <div class="mt-auto">
                                    {{-- Price --}}
                                    <div class="mb-3">
                                        @if ($isPromo)
                                            <div class="text-muted fs-12 text-decoration-line-through">
                                                Rp {{ number_format($barang->harga, 0, ',', '.') }}
                                            </div>
                                            <div class="d-flex align-items-baseline gap-1">
                                                <span class="fs-16 fw-bold text-danger">
                                                    Rp {{ number_format($hargaFinal, 0, ',', '.') }}
                                                </span>
                                                <span class="text-muted fs-12">/ hari</span>
                                            </div>
                                        @else
                                            <div class="d-flex align-items-baseline gap-1">
                                                <span class="fs-16 fw-bold text-primary">
                                                    Rp {{ number_format($barang->harga, 0, ',', '.') }}
                                                </span>
                                                <span class="text-muted fs-12">/ hari</span>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- In Cart Indicator --}}
                                    <div class="in-cart-pill mb-2 {{ $inCart ? '' : 'd-none' }}" id="inCartBadge_{{ $barang->id }}">
                                        <span class="badge bg-info-subtle text-info border border-info-subtle w-100 py-1 fs-11">
                                            <i class="bi bi-check2-circle me-1"></i> <span class="qty-in-cart-label">{{ $qtyInCart }}</span> unit di keranjang
                                        </span>
                                    </div>

                                    {{-- Action Form --}}
                                    <form action="{{ route('cart.add') }}" method="POST" class="form-add-to-cart" data-id="{{ $barang->id }}">
                                        @csrf
                                        <input type="hidden" name="barang_id" value="{{ $barang->id }}">
                                        
                                        <div class="row g-2 align-items-center">
                                            <div class="col-5">
                                                <div class="input-group input-group-sm quantity-stepper">
                                                    <button type="button" class="btn btn-light border btn-step-minus" style="padding: 0.25rem 0.5rem;">-</button>
                                                    <input type="number" name="qty" class="form-control text-center px-1 input-qty" value="1" min="1" max="{{ $barang->stok }}" required>
                                                    <button type="button" class="btn btn-light border btn-step-plus" style="padding: 0.25rem 0.5rem;">+</button>
                                                </div>
                                            </div>
                                            <div class="col-7">
                                                <button type="submit" class="btn btn-sm btn-primary w-100 d-flex align-items-center justify-content-center gap-1 btn-add-cart">
                                                    <i class="ri-add-line"></i>
                                                    <span>Sewa</span>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- 4. Table View (Alternative) --}}
            <div id="tableViewContainer" class="card border-0 shadow-sm d-none">
                <div class="card-body p-0 table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 70px;">Gambar</th>
                                <th>Alat & Kategori</th>
                                <th style="width: 140px;">Harga / Hari</th>
                                <th style="width: 110px;">Stok</th>
                                <th style="width: 130px;">Jumlah</th>
                                <th style="width: 130px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($barangs as $barang)
                                @php
                                    $isPromo = $barang->isDiskonAktif();
                                    $hargaFinal = $barang->getHargaDiskon();
                                    $inCart = isset($cart[$barang->id]);
                                    $qtyInCart = $inCart ? $cart[$barang->id]['qty'] : 0;
                                @endphp
                                <tr class="table-product-row"
                                    data-name="{{ strtolower($barang->nama_barang) }}"
                                    data-category="{{ $barang->jenis_barang_id }}"
                                    data-promo="{{ $isPromo ? '1' : '0' }}"
                                    data-price="{{ $hargaFinal }}"
                                    data-stock="{{ $barang->stok }}">
                                    <td>
                                        @if ($barang->gambar && file_exists(public_path('storage/' . $barang->gambar)))
                                            <img src="{{ asset('storage/' . $barang->gambar) }}" alt="{{ $barang->nama_barang }}" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                        @else
                                            <div class="rounded bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                <i class="ri-image-line text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $barang->nama_barang }}</div>
                                        <div class="d-flex align-items-center gap-1 mt-1">
                                            @if ($barang->jenisBarang)
                                                <span class="badge bg-secondary-subtle text-secondary fs-11">{{ $barang->jenisBarang->nama_jenis_barang }}</span>
                                            @endif
                                            @if ($isPromo)
                                                <span class="badge bg-danger-subtle text-danger fs-11">Promo Diskon</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if ($isPromo)
                                            <small class="text-muted text-decoration-line-through">Rp {{ number_format($barang->harga, 0, ',', '.') }}</small><br>
                                            <span class="fw-bold text-danger">Rp {{ number_format($hargaFinal, 0, ',', '.') }}</span>
                                        @else
                                            <span class="fw-bold text-primary">Rp {{ number_format($barang->harga, 0, ',', '.') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($barang->stok > 5)
                                            <span class="badge bg-success-subtle text-success">{{ $barang->stok }} unit</span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning">{{ $barang->stok }} unit</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm quantity-stepper" style="width: 110px;">
                                            <button type="button" class="btn btn-light border btn-step-minus">-</button>
                                            <input type="number" class="form-control text-center input-qty-table" value="1" min="1" max="{{ $barang->stok }}">
                                            <button type="button" class="btn btn-light border btn-step-plus">+</button>
                                        </div>
                                    </td>
                                    <td>
                                        <form action="{{ route('cart.add') }}" method="POST" class="form-add-to-cart" data-id="{{ $barang->id }}">
                                            @csrf
                                            <input type="hidden" name="barang_id" value="{{ $barang->id }}">
                                            <input type="hidden" name="qty" value="1" class="hidden-qty-input">
                                            <button type="submit" class="btn btn-sm btn-primary d-flex align-items-center gap-1 btn-add-cart">
                                                <i class="ri-shopping-cart-line"></i> Sewa
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 5. Empty Search State --}}
            <div id="emptySearchState" class="card border-0 shadow-sm text-center py-5 d-none my-4">
                <div class="card-body">
                    <div class="avatar-lg mx-auto mb-3 bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                        <i class="ri-search-eye-line text-muted display-6"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Alat Tidak Ditemukan</h5>
                    <p class="text-muted fs-13">Tidak ada peralatan yang cocok dengan filter atau kata kunci pencarian Anda.</p>
                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill" id="btnResetFilters">
                        <i class="ri-refresh-line me-1"></i> Reset Filter
                    </button>
                </div>
            </div>

        </div><!-- End container-fluid -->
    </main>

    {{-- 6. Floating Sticky Cart Bar (Always Visible if Cart has Items) --}}
    @php
        $cartCount = count($cart);
        $cartTotalPrice = collect($cart)->sum(fn($item) => $item['harga'] * $item['qty']);
    @endphp
    <div id="floatingCartBar" class="position-fixed bottom-0 start-50 translate-middle-x mb-4 shadow-lg rounded-pill bg-dark text-white p-2 px-4 d-flex align-items-center justify-content-between gap-4 z-3 {{ $cartCount > 0 ? '' : 'd-none' }}" style="max-width: 90%; min-width: 320px; backdrop-filter: blur(10px); background: rgba(30, 41, 59, 0.95) !important;">
        <div class="d-flex align-items-center gap-3">
            <div class="position-relative">
                <i class="ri-shopping-bag-3-fill fs-3 text-warning"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger fs-11 cart-count-badge">
                    {{ $cartCount }}
                </span>
            </div>
            <div>
                <div class="fs-12 text-light opacity-75">Keranjang Sewa</div>
                <div class="fw-bold fs-14 text-white" id="floatingCartTotal">
                    Rp {{ number_format($cartTotalPrice, 0, ',', '.') }}
                </div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-3" data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas">
                Lihat Detail
            </button>
            <a href="{{ route('cart.show') }}" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold">
                Checkout <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>

    {{-- 7. Offcanvas Quick Cart Drawer --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="cartOffcanvas" aria-labelledby="cartOffcanvasLabel" style="width: 400px; max-width: 100vw;">
        <div class="offcanvas-header border-bottom bg-light">
            <h5 class="offcanvas-title fw-bold" id="cartOffcanvasLabel">
                <i class="ri-shopping-cart-2-fill text-primary me-2"></i>Keranjang Sewa
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0 d-flex flex-column">
            
            {{-- List of Cart Items --}}
            <div class="flex-grow-1 overflow-auto p-3" id="cartItemsList">
                @if (empty($cart))
                    <div class="text-center py-5 text-muted" id="emptyCartMessage">
                        <i class="ri-shopping-cart-line display-4 text-muted opacity-50 mb-2"></i>
                        <p class="mb-0">Keranjang sewa Anda masih kosong.</p>
                        <small>Pilih alat dari katalog untuk mulai menyewa.</small>
                    </div>
                @else
                    @foreach ($cart as $item)
                        <div class="card border mb-2 cart-drawer-item" id="drawerItem_{{ $item['id'] }}">
                            <div class="card-body p-2 d-flex align-items-center gap-2">
                                @if (!empty($item['gambar']) && file_exists(public_path('storage/' . $item['gambar'])))
                                    <img src="{{ asset('storage/' . $item['gambar']) }}" alt="{{ $item['nama_barang'] }}" class="rounded" style="width: 45px; height: 45px; object-fit: cover;">
                                @else
                                    <div class="rounded bg-light d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                        <i class="ri-box-3-line text-muted"></i>
                                    </div>
                                @endif
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-semibold text-truncate fs-13">{{ $item['nama_barang'] }}</div>
                                    <div class="text-primary fs-12 fw-bold">Rp {{ number_format($item['harga'], 0, ',', '.') }} / hari</div>
                                    <div class="d-flex align-items-center justify-content-between mt-1">
                                        <div class="input-group input-group-sm" style="width: 85px;">
                                            <button type="button" class="btn btn-light border py-0 px-2 btn-cart-dec" data-id="{{ $item['id'] }}">-</button>
                                            <input type="text" class="form-control text-center px-0 py-0 fs-12 cart-item-qty-input" value="{{ $item['qty'] }}" readonly>
                                            <button type="button" class="btn btn-light border py-0 px-2 btn-cart-inc" data-id="{{ $item['id'] }}">+</button>
                                        </div>
                                        <span class="fs-12 fw-bold text-dark item-subtotal">
                                            Rp {{ number_format($item['harga'] * $item['qty'], 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-link text-danger p-1 btn-cart-remove" data-id="{{ $item['id'] }}" title="Hapus">
                                    <i class="bi bi-trash fs-14"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            {{-- Footer Summary --}}
            <div class="p-3 border-top bg-light">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted fs-13">Total Biaya Alat:</span>
                    <span class="fs-16 fw-bold text-primary" id="drawerCartTotal">
                        Rp {{ number_format($cartTotalPrice, 0, ',', '.') }}
                    </span>
                </div>
                <small class="text-muted d-block mb-3 fs-11">
                    *Total final disesuaikan dengan durasi hari sewa pada formulir jadwal berikutnya.
                </small>
                <a href="{{ route('cart.show') }}" class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2 py-2 fw-semibold">
                    <span>Lanjut ke Jadwal & Sewa</span>
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Toast Notification Container --}}
    <div class="toast-container position-fixed p-3" style="top: 85px !important; right: 25px !important; z-index: 99999 !important;">
        <div id="cartToast" class="toast align-items-center bg-white border border-success-subtle shadow-lg rounded-3" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex align-items-center p-2">
                <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center me-3" style="width: 38px; height: 38px; flex-shrink: 0;">
                    <i class="bi bi-check2-circle fs-4"></i>
                </div>
                <div class="toast-body py-1 flex-grow-1 ps-0">
                    <div class="fw-bold text-dark fs-13 mb-0" id="toastTitle">Berhasil!</div>
                    <div class="text-muted fs-12" id="toastMessage">Alat berhasil ditambahkan ke keranjang.</div>
                </div>
                <button type="button" class="btn-close ms-2 me-1" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

<style>
/* Custom Interactive Styling */
.product-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border-radius: 12px;
}
.product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08) !important;
}
.product-image-wrapper {
    height: 180px;
    background: #f8fafc;
}
.product-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}
.product-card:hover .product-img {
    transform: scale(1.05);
}
.product-img-placeholder {
    width: 100%;
    height: 100%;
}
.text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.btn-category {
    background-color: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
    font-weight: 500;
    white-space: nowrap;
    transition: all 0.2s;
}
.btn-category:hover, .btn-category.active {
    background-color: #2563eb;
    color: #fff;
    border-color: #2563eb;
}
.btn-category.active .badge {
    background-color: rgba(255, 255, 255, 0.2) !important;
    color: #fff !important;
}
.backdrop-blur {
    backdrop-filter: blur(4px);
}
.cart-drawer-item {
    border-radius: 8px;
    transition: background 0.15s;
}
.cart-drawer-item:hover {
    background-color: #f8fafc;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const clearSearch = document.getElementById('clearSearch');
    const sortSelect = document.getElementById('sortSelect');
    const categoryTabs = document.getElementById('categoryTabs');
    const gridViewContainer = document.getElementById('gridViewContainer');
    const tableViewContainer = document.getElementById('tableViewContainer');
    const emptySearchState = document.getElementById('emptySearchState');
    const btnViewGrid = document.getElementById('btnViewGrid');
    const btnViewTable = document.getElementById('btnViewTable');
    const btnResetFilters = document.getElementById('btnResetFilters');
    const floatingCartBar = document.getElementById('floatingCartBar');
    const floatingCartTotal = document.getElementById('floatingCartTotal');
    const drawerCartTotal = document.getElementById('drawerCartTotal');
    const cartBadges = document.querySelectorAll('.cart-count-badge');
    const cartItemsList = document.getElementById('cartItemsList');

    const cartToastEl = document.getElementById('cartToast');
    const toastTitle = document.getElementById('toastTitle');
    const toastMessage = document.getElementById('toastMessage');
    const toast = new bootstrap.Toast(cartToastEl, { delay: 3500 });

    let activeCategory = 'all';
    let currentSearchTerm = '';

    // 1. Quantity Stepper Functionality
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-step-plus')) {
            const input = e.target.parentElement.querySelector('input[type="number"]');
            const max = parseInt(input.getAttribute('max')) || 9999;
            let val = parseInt(input.value) || 1;
            if (val < max) input.value = val + 1;
            syncTableQty(input);
        } else if (e.target.classList.contains('btn-step-minus')) {
            const input = e.target.parentElement.querySelector('input[type="number"]');
            let val = parseInt(input.value) || 1;
            if (val > 1) input.value = val - 1;
            syncTableQty(input);
        }
    });

    function syncTableQty(input) {
        const row = input.closest('tr');
        if (row) {
            const hiddenQty = row.querySelector('.hidden-qty-input');
            if (hiddenQty) hiddenQty.value = input.value;
        }
    }

    // 2. View Mode Switcher (Grid vs Table)
    btnViewGrid.addEventListener('click', function() {
        btnViewGrid.classList.add('active');
        btnViewTable.classList.remove('active');
        gridViewContainer.classList.remove('d-none');
        tableViewContainer.classList.add('d-none');
    });

    btnViewTable.addEventListener('click', function() {
        btnViewTable.classList.add('active');
        btnViewGrid.classList.remove('active');
        tableViewContainer.classList.remove('d-none');
        gridViewContainer.classList.add('d-none');
    });

    // 3. Category Filter
    categoryTabs.querySelectorAll('.btn-category').forEach(btn => {
        btn.addEventListener('click', function() {
            categoryTabs.querySelectorAll('.btn-category').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            activeCategory = this.dataset.category;
            filterItems();
        });
    });

    // 4. Live Search
    searchInput.addEventListener('input', function() {
        currentSearchTerm = this.value.trim().toLowerCase();
        clearSearch.style.display = currentSearchTerm.length > 0 ? 'block' : 'none';
        filterItems();
    });

    clearSearch.addEventListener('click', function() {
        searchInput.value = '';
        currentSearchTerm = '';
        clearSearch.style.display = 'none';
        filterItems();
        searchInput.focus();
    });

    btnResetFilters.addEventListener('click', function() {
        searchInput.value = '';
        currentSearchTerm = '';
        clearSearch.style.display = 'none';
        categoryTabs.querySelectorAll('.btn-category').forEach(b => b.classList.remove('active'));
        categoryTabs.querySelector('[data-category="all"]').classList.add('active');
        activeCategory = 'all';
        sortSelect.value = 'default';
        filterItems();
    });

    // 5. Sorting
    sortSelect.addEventListener('change', function() {
        sortItems(this.value);
    });

    function filterItems() {
        let visibleCount = 0;

        // Filter Grid Items
        const gridItems = gridViewContainer.querySelectorAll('.product-item');
        gridItems.forEach(item => {
            const name = item.dataset.name;
            const category = item.dataset.category;
            const isPromo = item.dataset.promo === '1';

            const matchSearch = !currentSearchTerm || name.includes(currentSearchTerm);
            let matchCategory = true;
            if (activeCategory === 'promo') {
                matchCategory = isPromo;
            } else if (activeCategory !== 'all') {
                matchCategory = category === activeCategory;
            }

            if (matchSearch && matchCategory) {
                item.classList.remove('d-none');
                visibleCount++;
            } else {
                item.classList.add('d-none');
            }
        });

        // Filter Table Items
        const tableRows = tableViewContainer.querySelectorAll('.table-product-row');
        tableRows.forEach(row => {
            const name = row.dataset.name;
            const category = row.dataset.category;
            const isPromo = row.dataset.promo === '1';

            const matchSearch = !currentSearchTerm || name.includes(currentSearchTerm);
            let matchCategory = true;
            if (activeCategory === 'promo') {
                matchCategory = isPromo;
            } else if (activeCategory !== 'all') {
                matchCategory = category === activeCategory;
            }

            if (matchSearch && matchCategory) {
                row.classList.remove('d-none');
            } else {
                row.classList.add('d-none');
            }
        });

        // Show/Hide Empty State
        if (visibleCount === 0) {
            emptySearchState.classList.remove('d-none');
            gridViewContainer.classList.add('d-none');
            tableViewContainer.classList.add('d-none');
        } else {
            emptySearchState.classList.add('d-none');
            if (btnViewGrid.classList.contains('active')) {
                gridViewContainer.classList.remove('d-none');
            } else {
                tableViewContainer.classList.remove('d-none');
            }
        }
    }

    function sortItems(sortType) {
        const itemsArray = Array.from(gridViewContainer.querySelectorAll('.product-item'));
        const tableRowsArray = Array.from(tableViewContainer.querySelectorAll('.table-product-row'));

        const comparator = (a, b) => {
            if (sortType === 'price-asc') {
                return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
            } else if (sortType === 'price-desc') {
                return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
            } else if (sortType === 'stock-desc') {
                return parseInt(b.dataset.stock) - parseInt(a.dataset.stock);
            } else if (sortType === 'name-asc') {
                return a.dataset.name.localeCompare(b.dataset.name);
            }
            return 0; // default
        };

        if (sortType !== 'default') {
            itemsArray.sort(comparator);
            tableRowsArray.sort(comparator);

            itemsArray.forEach(item => gridViewContainer.appendChild(item));
            const tbody = tableViewContainer.querySelector('tbody');
            tableRowsArray.forEach(row => tbody.appendChild(row));
        }
    }

    // 6. AJAX Add to Cart
    document.querySelectorAll('.form-add-to-cart').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const submitBtn = this.querySelector('.btn-add-cart');
            const originalBtnHtml = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

            const formData = new FormData(this);

            fetch("{{ route('cart.add') }}", {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                submitBtn.disabled = false;
                if (data.success) {
                    submitBtn.innerHTML = '<i class="bi bi-check2"></i> Ditambah!';
                    setTimeout(() => {
                        submitBtn.innerHTML = originalBtnHtml;
                    }, 1500);

                    // Update UI Badges & Floating bar
                    updateCartUI(data);

                    // Update "in-cart" pill on the card
                    const cardBadge = document.getElementById('inCartBadge_' + data.item_id);
                    if (cardBadge) {
                        cardBadge.classList.remove('d-none');
                        const label = cardBadge.querySelector('.qty-in-cart-label');
                        if (label) label.textContent = data.current_qty;
                    }

                    // Show Toast
                    if (toastTitle) toastTitle.textContent = 'Berhasil Ditambahkan!';
                    toastMessage.textContent = data.message;
                    toast.show();
                } else {
                    submitBtn.innerHTML = originalBtnHtml;
                    alert(data.message || 'Gagal menambahkan barang ke keranjang.');
                }
            })
            .catch(err => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;
                console.error(err);
                // Fallback submit regular form if AJAX fails
                form.submit();
            });
        });
    });

    // 7. Cart Drawer Operations (Dec/Inc/Remove)
    document.addEventListener('click', function(e) {
        const decBtn = e.target.closest('.btn-cart-dec');
        const incBtn = e.target.closest('.btn-cart-inc');
        const removeBtn = e.target.closest('.btn-cart-remove');

        if (decBtn) {
            const id = decBtn.dataset.id;
            const input = decBtn.parentElement.querySelector('.cart-item-qty-input');
            const currentQty = parseInt(input.value) || 1;
            updateDrawerItemQty(id, currentQty - 1);
        } else if (incBtn) {
            const id = incBtn.dataset.id;
            const input = incBtn.parentElement.querySelector('.cart-item-qty-input');
            const currentQty = parseInt(input.value) || 1;
            updateDrawerItemQty(id, currentQty + 1);
        } else if (removeBtn) {
            const id = removeBtn.dataset.id;
            removeDrawerItem(id);
        }
    });

    function updateDrawerItemQty(barangId, newQty) {
        const formData = new FormData();
        formData.append('barang_id', barangId);
        formData.append('qty', newQty);

        fetch("{{ route('cart.update') }}", {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (newQty <= 0) {
                    const row = document.getElementById('drawerItem_' + barangId);
                    if (row) row.remove();
                    const cardBadge = document.getElementById('inCartBadge_' + barangId);
                    if (cardBadge) cardBadge.classList.add('d-none');
                } else {
                    const item = data.cart[barangId];
                    const row = document.getElementById('drawerItem_' + barangId);
                    if (row && item) {
                        row.querySelector('.cart-item-qty-input').value = item.qty;
                        row.querySelector('.item-subtotal').textContent = 'Rp ' + (item.harga * item.qty).toLocaleString('id-ID');
                    }
                    const cardBadge = document.getElementById('inCartBadge_' + barangId);
                    if (cardBadge) {
                        cardBadge.querySelector('.qty-in-cart-label').textContent = newQty;
                    }
                }
                updateCartUI(data);
            } else {
                alert(data.message);
            }
        })
        .catch(err => console.error(err));
    }

    function removeDrawerItem(barangId) {
        const formData = new FormData();
        formData.append('barang_id', barangId);

        fetch("{{ route('cart.remove') }}", {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const row = document.getElementById('drawerItem_' + barangId);
                if (row) row.remove();
                const cardBadge = document.getElementById('inCartBadge_' + barangId);
                if (cardBadge) cardBadge.classList.add('d-none');

                updateCartUI(data);

                if (toastTitle) toastTitle.textContent = 'Dihapus dari Keranjang';
                toastMessage.textContent = data.message;
                toast.show();
            }
        })
        .catch(err => console.error(err));
    }

    function updateCartUI(data) {
        const totalItems = data.total_items;
        const totalFormatted = data.formatted_total;

        cartBadges.forEach(b => b.textContent = totalItems);
        if (floatingCartTotal) floatingCartTotal.textContent = totalFormatted;
        if (drawerCartTotal) drawerCartTotal.textContent = totalFormatted;

        if (totalItems > 0) {
            floatingCartBar.classList.remove('d-none');
        } else {
            floatingCartBar.classList.add('d-none');
            if (cartItemsList) {
                cartItemsList.innerHTML = `
                    <div class="text-center py-5 text-muted" id="emptyCartMessage">
                        <i class="ri-shopping-cart-line display-4 text-muted opacity-50 mb-2"></i>
                        <p class="mb-0">Keranjang sewa Anda masih kosong.</p>
                        <small>Pilih alat dari katalog untuk mulai menyewa.</small>
                    </div>
                `;
            }
        }
    }
});
</script>

@endsection

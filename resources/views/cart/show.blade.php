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
                                <i class="ri-shopping-cart-2-line me-2"></i>Keranjang & Formulir Jadwal Sewa
                            </h4>
                            <p class="text-muted mb-0 fs-13">Tinjau daftar alat yang disewa dan lengkapi jadwal serta alamat acara Anda.</p>
                        </div>
                        <div class="col-lg-5">
                            <div class="d-flex align-items-center justify-content-lg-end gap-2 text-center fs-12">
                                <a href="{{ route('sewa.create') }}" class="step-item px-3 py-2 rounded-3 bg-light text-primary text-decoration-none">
                                    <i class="bi bi-check2 me-1"></i>1. Pilih Alat
                                </a>
                                <i class="bi bi-chevron-right text-muted"></i>
                                <div class="step-item active-step px-3 py-2 rounded-3 bg-primary text-white shadow-sm">
                                    <span class="fw-bold">2. Jadwal & Lokasi</span>
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
            @if (isset($errors) && $errors->any())
                <div class="alert alert-subtle-danger d-flex align-items-center mb-3 alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

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

            @if (empty($cart))
                <div class="card border-0 shadow-sm text-center py-5">
                    <div class="card-body">
                        <div class="avatar-lg mx-auto mb-3 bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="ri-shopping-cart-line display-5 text-muted"></i>
                        </div>
                        <h4 class="fw-bold text-dark">Keranjang Anda Kosong</h4>
                        <p class="text-muted fs-14 mb-4">Anda belum memilih peralatan apapun untuk disewa.</p>
                        <a href="{{ route('sewa.create') }}" class="btn btn-primary px-4 py-2 rounded-pill fw-semibold">
                            <i class="ri-store-2-line me-1"></i> Buka Katalog Alat
                        </a>
                    </div>
                </div>
            @else
                <div class="row g-4">
                    {{-- Left Column: Items Table --}}
                    <div class="col-lg-7">
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0 fw-bold">
                                    <i class="ri-box-3-line text-primary me-2"></i>Daftar Alat Dipilih ({{ count($cart) }})
                                </h5>
                                <a href="{{ route('sewa.create') }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                    <i class="ri-add-line me-1"></i> Tambah Alat Lain
                                </a>
                            </div>
                            <div class="card-body p-0 table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light fs-12">
                                        <tr>
                                            <th>Alat</th>
                                            <th style="width: 110px;">Harga / Hari</th>
                                            <th style="width: 120px;" class="text-center">Jumlah</th>
                                            <th style="width: 120px;" class="text-end">Subtotal / Hari</th>
                                            <th style="width: 50px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $dailyTotal = 0; @endphp
                                        @foreach ($cart as $item)
                                            @php
                                                $itemSubtotal = $item['harga'] * $item['qty'];
                                                $dailyTotal += $itemSubtotal;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        @if (!empty($item['gambar']) && file_exists(public_path('storage/' . $item['gambar'])))
                                                            <img src="{{ asset('storage/' . $item['gambar']) }}" alt="{{ $item['nama_barang'] }}" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                                        @else
                                                            <div class="rounded bg-light d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                                <i class="ri-image-line text-muted"></i>
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <div class="fw-bold text-dark fs-13">{{ $item['nama_barang'] }}</div>
                                                            <small class="text-muted">Stok: {{ $item['stok'] }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="fs-13">
                                                    Rp {{ number_format($item['harga'], 0, ',', '.') }}
                                                </td>
                                                <td>
                                                    <form action="{{ route('cart.update') }}" method="POST" class="d-flex align-items-center justify-content-center">
                                                        @csrf
                                                        <input type="hidden" name="barang_id" value="{{ $item['id'] }}">
                                                        <div class="input-group input-group-sm" style="width: 95px;">
                                                            <button type="submit" name="qty" value="{{ $item['qty'] - 1 }}" class="btn btn-light border py-0 px-2" title="Kurangi">-</button>
                                                            <input type="text" class="form-control text-center px-1 py-0 fs-12" value="{{ $item['qty'] }}" readonly>
                                                            <button type="submit" name="qty" value="{{ $item['qty'] + 1 }}" class="btn btn-light border py-0 px-2" {{ $item['qty'] >= $item['stok'] ? 'disabled' : '' }} title="Tambah">+</button>
                                                        </div>
                                                    </form>
                                                </td>
                                                <td class="text-end fw-bold text-primary fs-13">
                                                    Rp {{ number_format($itemSubtotal, 0, ',', '.') }}
                                                </td>
                                                <td class="text-center">
                                                    <form action="{{ route('cart.remove') }}" method="POST" onsubmit="return confirm('Hapus {{ $item['nama_barang'] }} dari keranjang?')">
                                                        @csrf
                                                        <input type="hidden" name="barang_id" value="{{ $item['id'] }}">
                                                        <button type="submit" class="btn btn-link text-danger p-0" title="Hapus">
                                                            <i class="bi bi-trash fs-16"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <td colspan="3" class="fw-bold text-dark text-end">Total Biaya Alat per Hari:</td>
                                            <td class="text-end fw-bold text-primary fs-15" id="cartDailyTotal" data-daily="{{ $dailyTotal }}">
                                                Rp {{ number_format($dailyTotal, 0, ',', '.') }}
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column: Event Schedule & Checkout Form --}}
                    <div class="col-lg-5">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white py-3">
                                <h5 class="card-title mb-0 fw-bold">
                                    <i class="ri-calendar-event-line text-primary me-2"></i>Informasi Acara & Jadwal
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <form action="{{ route('checkout') }}" method="POST" id="checkoutForm">
                                    @csrf

                                    {{-- Tanggal Sewa --}}
                                    <div class="mb-3">
                                        <label for="tgl_sewa" class="form-label fw-semibold fs-13">Tanggal Pengajuan Sewa <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="tgl_sewa" name="tgl_sewa" value="{{ date('Y-m-d') }}" required>
                                    </div>

                                    {{-- Tanggal & Jam Loading (Pasang) --}}
                                    <div class="row g-2 mb-3">
                                        <div class="col-7">
                                            <label for="tgl_loading" class="form-label fw-semibold fs-13">Tanggal Loading <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="tgl_loading" name="tgl_loading" min="{{ date('Y-m-d') }}" required>
                                            <small class="text-muted fs-11">Waktu pemasangan alat di lokasi</small>
                                        </div>
                                        <div class="col-5">
                                            <label for="jam_loading" class="form-label fw-semibold fs-13">Jam Loading <span class="text-danger">*</span></label>
                                            <input type="time" class="form-control" id="jam_loading" name="jam_loading" value="09:00" required>
                                        </div>
                                    </div>

                                    {{-- Tanggal & Jam Acara --}}
                                    <div class="row g-2 mb-3">
                                        <div class="col-7">
                                            <label for="tgl_acara" class="form-label fw-semibold fs-13">Tanggal Acara <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="tgl_acara" name="tgl_acara" required>
                                        </div>
                                        <div class="col-5">
                                            <label for="jam_acara" class="form-label fw-semibold fs-13">Jam Acara <span class="text-danger">*</span></label>
                                            <input type="time" class="form-control" id="jam_acara" name="jam_acara" value="10:00" required>
                                        </div>
                                    </div>

                                    {{-- Tanggal Loading Out (Bongkar) --}}
                                    <div class="mb-3">
                                        <label for="tgl_loading_out" class="form-label fw-semibold fs-13">Tanggal Loading Out (Bongkar) <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="tgl_loading_out" name="tgl_loading_out" required>
                                        <small class="text-muted fs-11">Pengambilan alat kembali oleh kru</small>
                                    </div>

                                    {{-- Warning Box if loading out is invalid --}}
                                    <div class="alert alert-danger d-none py-2 px-3 mb-3 fs-12" id="dateWarning">
                                        <i class="bi bi-exclamation-circle-fill me-1"></i>
                                        <strong>Peringatan:</strong> Tanggal Loading Out tidak boleh sebelum Tanggal Acara!
                                    </div>

                                    {{-- Durasi Info Box --}}
                                    <div class="alert alert-info py-2 px-3 mb-3 fs-13 d-flex justify-content-between align-items-center" id="durasiBox">
                                        <span><i class="bi bi-clock-history me-1"></i> Durasi Sewa:</span>
                                        <span class="badge bg-primary fs-13" id="durasiBadge">1 Hari</span>
                                    </div>

                                    {{-- Alamat Acara --}}
                                    <div class="mb-4">
                                        <label for="alamat_acara" class="form-label fw-semibold fs-13">Alamat Lengkap Acara <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="alamat_acara" name="alamat_acara" rows="3" placeholder="Nama gedung, jalan, nomor, RT/RW, kelurahan, kecamatan..." required>{{ old('alamat_acara') }}</textarea>
                                    </div>

                                    {{-- Live Cost Calculation Card --}}
                                    <div class="card bg-light border-0 mb-4 p-3 rounded-3">
                                        <div class="d-flex justify-content-between text-muted fs-13 mb-2">
                                            <span>Biaya Alat per Hari:</span>
                                            <span class="fw-semibold">Rp {{ number_format($dailyTotal, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between text-muted fs-13 mb-2">
                                            <span>Durasi Hari Sewa:</span>
                                            <span class="fw-semibold" id="calcDurasiHari">1 Hari</span>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between align-items-baseline mb-3">
                                            <span class="fw-bold text-dark">Total Biaya Sewa:</span>
                                            <span class="fw-bold text-primary fs-18" id="calcGrandTotal">
                                                Rp {{ number_format($dailyTotal, 0, ',', '.') }}
                                            </span>
                                        </div>

                                        {{-- DP Input Section --}}
                                        <div class="mb-3 p-3 bg-white rounded-3 border">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <label for="uang_muka" class="form-label fw-bold fs-13 mb-0 text-dark">
                                                    <i class="bi bi-cash-stack text-success me-1"></i> Uang Muka (DP) <span class="text-danger">*</span>
                                                </label>
                                                <span class="badge bg-primary text-white" id="dpPercentBadge">50%</span>
                                            </div>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-light fw-bold">Rp</span>
                                                <input type="number" name="uang_muka" id="uang_muka" 
                                                       class="form-control fw-bold"
                                                       value="{{ old('uang_muka', round($dailyTotal * 0.5)) }}" 
                                                       min="0" step="1000" 
                                                       placeholder="Minimal 50% dari total sewa" required>
                                                <button type="button" class="btn btn-outline-primary btn-sm px-2" id="btnSetDP50" title="Set DP ke 50%">
                                                    50%
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary btn-sm px-2" id="btnSetDP100" title="Set Lunas (100%)">
                                                    100%
                                                </button>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mt-1">
                                                <small class="text-muted fs-11" id="dpMinHint">Minimal 50%: <span id="calcDPMin">Rp {{ number_format($dailyTotal * 0.5, 0, ',', '.') }}</span></small>
                                                <small id="dpValidationFeedback" class="fs-11 fw-semibold"></small>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between text-muted fs-13 pt-1">
                                            <span class="fw-semibold">Sisa Pelunasan:</span>
                                            <span class="fw-bold text-danger" id="calcSisa">
                                                Rp {{ number_format($dailyTotal - round($dailyTotal * 0.5), 0, ',', '.') }}
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Submit Buttons --}}
                                    <div class="d-flex flex-column gap-2">
                                        <button type="submit" class="btn btn-primary py-2 fw-semibold d-flex align-items-center justify-content-center gap-2" id="btnCheckout">
                                            <span>Konfirmasi & Lanjut Pembayaran</span>
                                            <i class="bi bi-arrow-right"></i>
                                        </button>
                                        <a href="{{ route('sewa.create') }}" class="btn btn-outline-secondary py-2">
                                            <i class="bi bi-arrow-left me-1"></i> Kembali Pilih Alat
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div><!-- End container-fluid -->
    </main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tglLoading = document.getElementById('tgl_loading');
    const tglAcara = document.getElementById('tgl_acara');
    const tglLoadingOut = document.getElementById('tgl_loading_out');
    const dateWarning = document.getElementById('dateWarning');
    const durasiBadge = document.getElementById('durasiBadge');
    const calcDurasiHari = document.getElementById('calcDurasiHari');
    const calcGrandTotal = document.getElementById('calcGrandTotal');
    const btnCheckout = document.getElementById('btnCheckout');
    const cartDailyTotalEl = document.getElementById('cartDailyTotal');

    const uangMukaInput = document.getElementById('uang_muka');
    const btnSetDP50 = document.getElementById('btnSetDP50');
    const btnSetDP100 = document.getElementById('btnSetDP100');
    const dpPercentBadge = document.getElementById('dpPercentBadge');
    const calcDPMin = document.getElementById('calcDPMin');
    const dpValidationFeedback = document.getElementById('dpValidationFeedback');
    const calcSisa = document.getElementById('calcSisa');
    const checkoutForm = document.getElementById('checkoutForm');

    const dailyTotal = cartDailyTotalEl ? parseFloat(cartDailyTotalEl.dataset.daily) || 0 : 0;
    let currentGrandTotal = dailyTotal;
    let isDateValid = true;
    let userEnteredCustomDP = {{ old('uang_muka') ? 'true' : 'false' }};

    if (tglLoading && tglAcara && tglLoadingOut) {
        // When tglLoading changes, constrain tglAcara
        tglLoading.addEventListener('change', function() {
            if (tglLoading.value) {
                tglAcara.min = tglLoading.value;
                if (tglAcara.value && tglAcara.value < tglLoading.value) {
                    tglAcara.value = tglLoading.value;
                }
            }
            recalculateTotals();
        });

        // When tglAcara changes, constrain tglLoadingOut
        tglAcara.addEventListener('change', function() {
            if (tglAcara.value) {
                tglLoadingOut.min = tglAcara.value;
                if (!tglLoadingOut.value || tglLoadingOut.value < tglAcara.value) {
                    tglLoadingOut.value = tglAcara.value;
                }
            }
            recalculateTotals();
        });

        tglLoadingOut.addEventListener('change', recalculateTotals);
    }

    function validateDP() {
        const total = currentGrandTotal;
        const minDP = Math.round(total * 0.5);
        const dpRaw = uangMukaInput.value.trim();
        const dpVal = parseFloat(dpRaw);

        if (total <= 0) {
            uangMukaInput.classList.remove('is-invalid', 'is-valid');
            dpValidationFeedback.textContent = '';
            dpPercentBadge.textContent = '50%';
            dpPercentBadge.className = 'badge bg-primary text-white';
            calcSisa.textContent = 'Rp 0';
            if (btnCheckout) btnCheckout.disabled = !isDateValid;
            return true;
        }

        if (dpRaw === '' || isNaN(dpVal)) {
            uangMukaInput.classList.add('is-invalid');
            uangMukaInput.classList.remove('is-valid');
            dpValidationFeedback.className = 'text-danger fw-semibold';
            dpValidationFeedback.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i>DP wajib diisi!';
            dpPercentBadge.textContent = '0%';
            dpPercentBadge.className = 'badge bg-danger text-white';
            if (btnCheckout) btnCheckout.disabled = true;
            return false;
        }

        if (dpVal < minDP) {
            uangMukaInput.classList.add('is-invalid');
            uangMukaInput.classList.remove('is-valid');
            dpValidationFeedback.className = 'text-danger fw-semibold';
            dpValidationFeedback.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1"></i>DP minimal 50% (Rp ' + minDP.toLocaleString('id-ID') + ')';
            const pct = Math.max(0, (dpVal / total) * 100).toFixed(1);
            dpPercentBadge.textContent = pct + '%';
            dpPercentBadge.className = 'badge bg-danger text-white';
            calcSisa.textContent = 'Rp ' + Math.max(0, total - dpVal).toLocaleString('id-ID');
            if (btnCheckout) btnCheckout.disabled = true;
            return false;
        }

        if (dpVal > total) {
            uangMukaInput.classList.add('is-invalid');
            uangMukaInput.classList.remove('is-valid');
            dpValidationFeedback.className = 'text-danger fw-semibold';
            dpValidationFeedback.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1"></i>DP tidak boleh melebihi total!';
            dpPercentBadge.textContent = '> 100%';
            dpPercentBadge.className = 'badge bg-danger text-white';
            calcSisa.textContent = 'Rp 0';
            if (btnCheckout) btnCheckout.disabled = true;
            return false;
        }

        // DP Valid
        uangMukaInput.classList.remove('is-invalid');
        uangMukaInput.classList.add('is-valid');
        const pct = ((dpVal / total) * 100).toFixed(1);
        dpPercentBadge.textContent = (pct.endsWith('.0') ? parseInt(pct) : pct) + '%';
        dpPercentBadge.className = 'badge bg-success text-white';
        dpValidationFeedback.className = 'text-success fw-semibold';
        dpValidationFeedback.innerHTML = '<i class="bi bi-check-circle me-1"></i>DP valid';

        const sisa = Math.max(0, total - dpVal);
        calcSisa.textContent = 'Rp ' + sisa.toLocaleString('id-ID');

        if (btnCheckout) {
            btnCheckout.disabled = !isDateValid;
        }
        return true;
    }

    function recalculateTotals() {
        let durasiHari = 1;

        if (tglAcara.value && tglLoadingOut.value) {
            const acara = new Date(tglAcara.value);
            const loadingOut = new Date(tglLoadingOut.value);

            if (loadingOut < acara) {
                dateWarning.classList.remove('d-none');
                isDateValid = false;
                btnCheckout.disabled = true;
                return;
            } else {
                dateWarning.classList.add('d-none');
                isDateValid = true;
                const diffTime = Math.abs(loadingOut - acara);
                durasiHari = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
            }
        }

        durasiBadge.textContent = durasiHari + ' Hari';
        calcDurasiHari.textContent = durasiHari + ' Hari';

        currentGrandTotal = dailyTotal * durasiHari;
        const minDP = Math.round(currentGrandTotal * 0.5);

        calcGrandTotal.textContent = 'Rp ' + currentGrandTotal.toLocaleString('id-ID');
        if (calcDPMin) {
            calcDPMin.textContent = 'Rp ' + minDP.toLocaleString('id-ID');
        }

        // Update DP input value if not manually set or if lower than new minDP
        if (!userEnteredCustomDP || !uangMukaInput.value || parseFloat(uangMukaInput.value) < minDP) {
            uangMukaInput.value = minDP;
        }

        validateDP();
    }

    uangMukaInput.addEventListener('input', function() {
        userEnteredCustomDP = true;
        validateDP();
    });

    btnSetDP50.addEventListener('click', function() {
        userEnteredCustomDP = true;
        uangMukaInput.value = Math.round(currentGrandTotal * 0.5);
        validateDP();
    });

    btnSetDP100.addEventListener('click', function() {
        userEnteredCustomDP = true;
        uangMukaInput.value = currentGrandTotal;
        validateDP();
    });

    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            if (!validateDP()) {
                e.preventDefault();
                uangMukaInput.focus();
            }
        });
    }

    // Initial run
    recalculateTotals();
});
</script>

@endsection

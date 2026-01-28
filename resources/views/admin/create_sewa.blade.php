@extends('templates.base')

@section('content')

<div class="sidebar-backdrop" id="sidebar-backdrop"></div>
    <main class="app-wrapper">
        <div class="container-fluid">

            <div class="main-breadcrumb d-flex align-items-center my-3 position-relative">
                <h2 class="breadcrumb-title mb-0 flex-grow-1 fs-14">{{ $title }}</h2>
                <div class="flex-shrink-0">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-end mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.penyewaan') }}">Penyewaan</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
                        </ol>
                    </nav>
                </div>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0"><i class="bi bi-plus-circle me-2"></i>Form Sewa Offline</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.store_sewa') }}" method="POST" id="formSewaOffline">
                        @csrf
                        
                        <div class="row">
                            <!-- Pilih Customer -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Pilih Customer <span class="text-danger">*</span></label>
                                <select name="id_user" class="form-select" required>
                                    <option value="">-- Pilih Customer --</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ old('id_user') == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }} ({{ $customer->email }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Jika customer belum terdaftar, <a href="{{ route('customer.customer') }}" target="_blank">daftarkan dulu</a></small>
                            </div>

                            <!-- Status Pembayaran -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Status Pembayaran <span class="text-danger">*</span></label>
                                <select name="status_pembayaran" class="form-select" required>
                                    <option value="belum_bayar" {{ old('status_pembayaran') == 'belum_bayar' ? 'selected' : '' }}>Belum Bayar</option>
                                    <option value="dp_lunas" {{ old('status_pembayaran') == 'dp_lunas' ? 'selected' : '' }}>DP Lunas (50%)</option>
                                    <option value="lunas" {{ old('status_pembayaran') == 'lunas' ? 'selected' : '' }}>Lunas (100%)</option>
                                </select>
                            </div>
                        </div>

                        <hr>
                        <h6 class="fw-bold mb-3"><i class="bi bi-calendar-event me-2"></i>Informasi Jadwal</h6>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tanggal Sewa <span class="text-danger">*</span></label>
                                <input type="date" name="tgl_sewa" class="form-control" value="{{ old('tgl_sewa', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tanggal Acara <span class="text-danger">*</span></label>
                                <input type="date" name="tgl_acara" class="form-control" value="{{ old('tgl_acara') }}" required id="tgl_acara">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Jam Acara <span class="text-danger">*</span></label>
                                <input type="time" name="jam_acara" class="form-control" value="{{ old('jam_acara') }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tanggal Loading <span class="text-danger">*</span></label>
                                <input type="date" name="tgl_loading" class="form-control" value="{{ old('tgl_loading') }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Jam Loading <span class="text-danger">*</span></label>
                                <input type="time" name="jam_loading" class="form-control" value="{{ old('jam_loading') }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tanggal Loading Out <span class="text-danger">*</span></label>
                                <input type="date" name="tgl_loading_out" class="form-control" value="{{ old('tgl_loading_out') }}" required id="tgl_loading_out">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label">Alamat Acara <span class="text-danger">*</span></label>
                                <textarea name="alamat_acara" class="form-control" rows="2" required>{{ old('alamat_acara') }}</textarea>
                            </div>
                        </div>

                        <div class="alert alert-info mb-3" id="durasiInfo" style="display: none;">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Durasi Sewa:</strong> <span id="durasiHari">0</span> hari
                        </div>

                        <hr>
                        <h6 class="fw-bold mb-3"><i class="bi bi-box-seam me-2"></i>Pilih Barang</h6>

                        <div id="barangContainer">
                            <div class="row barang-item mb-2">
                                <div class="col-md-6">
                                    <select name="barang_id[]" class="form-select barang-select" required>
                                        <option value="">-- Pilih Barang --</option>
                                        @foreach($barangs as $barang)
                                            <option value="{{ $barang->id }}" data-harga="{{ $barang->harga }}" data-stok="{{ $barang->stok }}">
                                                {{ $barang->nama_barang }} - Rp {{ number_format($barang->harga, 0, ',', '.') }} (Stok: {{ $barang->stok }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" name="qty[]" class="form-control qty-input" placeholder="Qty" min="1" value="1" required>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-danger btn-remove-barang" style="display: none;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-outline-primary btn-sm mb-3" id="btnAddBarang">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Barang Lain
                        </button>

                        <hr>
                        <h6 class="fw-bold mb-3"><i class="bi bi-percent me-2"></i>Diskon (Opsional)</h6>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tipe Diskon</label>
                                <select name="diskon_tipe" class="form-select" id="diskon_tipe">
                                    <option value="">-- Tanpa Diskon --</option>
                                    <option value="persen" {{ old('diskon_tipe') == 'persen' ? 'selected' : '' }}>Persen (%)</option>
                                    <option value="nominal" {{ old('diskon_tipe') == 'nominal' ? 'selected' : '' }}>Nominal (Rp)</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Nilai Diskon</label>
                                <input type="number" name="diskon_nilai" class="form-control" id="diskon_nilai" 
                                       value="{{ old('diskon_nilai') }}" min="0" placeholder="Masukkan nilai diskon">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Preview Diskon</label>
                                <div class="form-control bg-light" id="previewDiskon">- Rp 0</div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="alert alert-secondary mb-2">
                                    <strong>Subtotal (Sebelum Diskon):</strong> 
                                    <span class="fs-6" id="subtotalSebelumDiskon">Rp 0</span>
                                </div>
                                <div class="alert alert-success">
                                    <strong>Total Setelah Diskon:</strong> 
                                    <span class="fs-5 text-success fw-bold" id="estimasiTotal">Rp 0</span>
                                    <br>
                                    <small class="text-muted">DP (50%): <span id="estimasiDP">Rp 0</span></small>
                                </div>
                            </div>
                            <div class="col-md-6 text-end d-flex align-items-end justify-content-end">
                                <a href="{{ route('admin.penyewaan') }}" class="btn btn-secondary me-2">
                                    <i class="bi bi-arrow-left me-1"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle me-1"></i> Simpan Sewa
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const barangContainer = document.getElementById('barangContainer');
    const btnAddBarang = document.getElementById('btnAddBarang');
    const tglAcara = document.getElementById('tgl_acara');
    const tglLoadingOut = document.getElementById('tgl_loading_out');
    const durasiInfo = document.getElementById('durasiInfo');
    const durasiHari = document.getElementById('durasiHari');
    
    // Template for new barang row
    const barangTemplate = barangContainer.querySelector('.barang-item').cloneNode(true);
    barangTemplate.querySelector('.btn-remove-barang').style.display = 'block';
    barangTemplate.querySelector('.barang-select').value = '';
    barangTemplate.querySelector('.qty-input').value = 1;

    // Add new barang row
    btnAddBarang.addEventListener('click', function() {
        const newRow = barangTemplate.cloneNode(true);
        barangContainer.appendChild(newRow);
        updateRemoveButtons();
        attachEventListeners(newRow);
    });

    // Remove barang row
    barangContainer.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-barang')) {
            e.target.closest('.barang-item').remove();
            updateRemoveButtons();
            calculateTotal();
        }
    });

    function updateRemoveButtons() {
        const items = barangContainer.querySelectorAll('.barang-item');
        items.forEach((item, index) => {
            item.querySelector('.btn-remove-barang').style.display = items.length > 1 ? 'block' : 'none';
        });
    }

    function attachEventListeners(row) {
        row.querySelector('.barang-select').addEventListener('change', calculateTotal);
        row.querySelector('.qty-input').addEventListener('input', calculateTotal);
    }

    // Initial event listeners
    document.querySelectorAll('.barang-select').forEach(el => el.addEventListener('change', calculateTotal));
    document.querySelectorAll('.qty-input').forEach(el => el.addEventListener('input', calculateTotal));
    tglAcara.addEventListener('change', updateDurasi);
    tglLoadingOut.addEventListener('change', updateDurasi);
    
    // Diskon event listeners
    const diskonTipe = document.getElementById('diskon_tipe');
    const diskonNilai = document.getElementById('diskon_nilai');
    diskonTipe.addEventListener('change', calculateTotal);
    diskonNilai.addEventListener('input', calculateTotal);

    function updateDurasi() {
        const acara = new Date(tglAcara.value);
        const loadingOut = new Date(tglLoadingOut.value);
        
        if (tglAcara.value && tglLoadingOut.value && loadingOut >= acara) {
            const diffTime = Math.abs(loadingOut - acara);
            // Durasi inklusif: tanggal sama = 1 hari, 11 ke 12 = 2 hari
            let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
            
            durasiHari.textContent = diffDays;
            durasiInfo.style.display = 'block';
        } else {
            durasiInfo.style.display = 'none';
        }
        calculateTotal();
    }

    function calculateTotal() {
        const acara = new Date(tglAcara.value);
        const loadingOut = new Date(tglLoadingOut.value);
        let durasi = 1;
        
        if (tglAcara.value && tglLoadingOut.value && loadingOut >= acara) {
            const diffTime = Math.abs(loadingOut - acara);
            // Durasi inklusif: tanggal sama = 1 hari, 11 ke 12 = 2 hari
            durasi = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
        }

        // Hitung subtotal (sebelum diskon)
        let subtotal = 0;
        document.querySelectorAll('.barang-item').forEach(item => {
            const select = item.querySelector('.barang-select');
            const qty = parseInt(item.querySelector('.qty-input').value) || 0;
            const harga = parseInt(select.options[select.selectedIndex]?.dataset?.harga) || 0;
            
            subtotal += harga * qty * durasi;
        });

        // Hitung diskon
        let diskon = 0;
        const tipe = diskonTipe.value;
        const nilai = parseFloat(diskonNilai.value) || 0;
        
        if (tipe === 'persen' && nilai > 0) {
            const persen = Math.min(nilai, 100); // Max 100%
            diskon = (subtotal * persen) / 100;
        } else if (tipe === 'nominal' && nilai > 0) {
            diskon = Math.min(nilai, subtotal); // Tidak lebih dari subtotal
        }

        // Total setelah diskon
        const total = subtotal - diskon;

        // Update tampilan
        document.getElementById('subtotalSebelumDiskon').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
        document.getElementById('previewDiskon').textContent = '- Rp ' + diskon.toLocaleString('id-ID');
        document.getElementById('estimasiTotal').textContent = 'Rp ' + total.toLocaleString('id-ID');
        document.getElementById('estimasiDP').textContent = 'Rp ' + (total * 0.5).toLocaleString('id-ID');
    }
});
</script>

@endsection

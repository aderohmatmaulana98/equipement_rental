@extends('templates.base')

@section('content')

    <div class="sidebar-backdrop" id="sidebar-backdrop"></div>
    <main class="app-wrapper">
        <div class="container py-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h4 class="fw-bold text-primary mb-0">
                        Detail Penyewaan - {{ $sewa->kode_sewa }}
                    </h4>
                    <span
                        class="badge 
                @if ($sewa->status == 'belum bayar') bg-danger
                @elseif($sewa->status == 'pending') bg-warning text-dark
                @elseif($sewa->status == 'dp_lunas') bg-info
                @elseif($sewa->status == 'disetujui') bg-success
                @elseif($sewa->status == 'berjalan') bg-info text-dark
                @elseif($sewa->status == 'selesai') bg-primary
                @elseif($sewa->status == 'expired') bg-dark
                @else bg-secondary @endif">
                        @if($sewa->status == 'dp_lunas')
                            DP Lunas
                        @elseif($sewa->status == 'disetujui')
                            Lunas
                        @else
                            {{ ucfirst($sewa->status) }}
                        @endif
                    </span>
                </div>

                <div class="card-body">
                    {{-- Informasi Penyewa --}}
                    <div class="alert alert-light border mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="fw-bold mb-2"><i class="bi bi-person-circle me-2"></i>Informasi Penyewa</h6>
                                <table class="table table-borderless table-sm mb-0">
                                    <tr>
                                        <th class="text-muted" width="40%">Nama Penyewa</th>
                                        <td class="fw-semibold">{{ $sewa->user->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Email</th>
                                        <td>{{ $sewa->user->email ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">No. HP</th>
                                        <td>{{ $sewa->user->no_hp ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row gy-3">
                        <div class="col-md-6">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <th class="text-muted" width="40%">Kode Sewa</th>
                                    <td>{{ $sewa->kode_sewa }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Tanggal Sewa</th>
                                    <td>{{ \Carbon\Carbon::parse($sewa->tgl_sewa)->locale('id')->isoFormat('dddd, D MMMM Y') }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Tanggal Acara</th>
                                    <td>{{ \Carbon\Carbon::parse($sewa->tgl_acara)->locale('id')->isoFormat('dddd, D MMMM Y') }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Jam Acara</th>
                                    <td>{{ \Carbon\Carbon::parse($sewa->jam_acara)->locale('id')->isoFormat('HH.mm [WIB]') }}</td>
                                <tr>
                                    <th class="text-muted">Alamat Acara</th>
                                    <td>{{ $sewa->alamat_acara }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            @php
                                $tglAcara = \Carbon\Carbon::parse($sewa->tgl_acara);
                                $tglLoadingOut = \Carbon\Carbon::parse($sewa->tgl_loading_out);
                                // Hitung inklusif: tanggal sama = 1 hari, 11 ke 12 = 2 hari
                                $durasiHari = $tglAcara->diffInDays($tglLoadingOut) + 1;
                            @endphp
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <th class="text-muted">Tanggal Loading</th>
                                    <td>{{ \Carbon\Carbon::parse($sewa->tgl_loading)->locale('id')->isoFormat('dddd, D MMMM Y') }}

                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Jam Loading</th>
                                    <td>{{ \Carbon\Carbon::parse($sewa->jam_loading)->locale('id')->isoFormat('HH.mm [WIB]') }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Tanggal Loading Out</th>
                                    <td>{{ \Carbon\Carbon::parse($sewa->tgl_loading_out)->locale('id')->isoFormat('dddd, D MMMM Y') }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Durasi Sewa</th>
                                    <td><span class="badge bg-primary fs-6">{{ $durasiHari }} Hari</span></td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Batas Konfirmasi</th>
                                    <td>{{ \Carbon\Carbon::parse($sewa->batas_waktu_pembayaran)->locale('id')->isoFormat('dddd, D MMMM Y HH:mm [WIB]') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-2">Rincian Pembayaran</h6>
                            
                            @php
                                // Hitung status pembayaran
                                $isLunas = in_array($sewa->status, ['disetujui', 'berjalan', 'selesai']);
                                $isDPLunas = $sewa->status === 'dp_lunas';
                                $sudahBayar = $isLunas ? $sewa->total_biaya : ($isDPLunas ? $sewa->uang_muka : 0);
                                $sisaBayar = $sewa->total_biaya - $sudahBayar;
                                $progress = $sewa->total_biaya > 0 ? ($sudahBayar / $sewa->total_biaya) * 100 : 0;
                            @endphp
                            
                            <table class="table table-sm">
                                @if($sewa->diskon_nominal && $sewa->diskon_nominal > 0)
                                <tr>
                                    <th>Subtotal</th>
                                    <td>Rp {{ number_format($sewa->total_sebelum_diskon, 0, ',', '.') }}</td>
                                </tr>
                                <tr class="text-success">
                                    <th>Diskon {{ $sewa->diskon_persen ? '(' . $sewa->diskon_persen . '%)' : '' }}</th>
                                    <td>- Rp {{ number_format($sewa->diskon_nominal, 0, ',', '.') }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <th>Total Biaya</th>
                                    <td>Rp {{ number_format($sewa->total_biaya, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>DP (50%)</th>
                                    <td>
                                        Rp {{ number_format($sewa->uang_muka, 0, ',', '.') }}
                                        @if($isDPLunas || $isLunas)
                                            <span class="badge bg-success ms-2">Lunas</span>
                                        @else
                                            <span class="badge bg-danger ms-2">Belum Bayar</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Pelunasan (50%)</th>
                                    <td>
                                        Rp {{ number_format($sewa->sisa_pembayaran ?? ($sewa->total_biaya - $sewa->uang_muka), 0, ',', '.') }}
                                        @if($isLunas)
                                            <span class="badge bg-success ms-2">Lunas</span>
                                        @elseif($isDPLunas)
                                            <span class="badge bg-warning text-dark ms-2">Menunggu Pelunasan</span>
                                        @else
                                            <span class="badge bg-secondary ms-2">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr class="table-active">
                                    <th>Sudah Dibayar</th>
                                    <td class="fw-bold text-success">Rp {{ number_format($sudahBayar, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Sisa yang Harus Dibayar</th>
                                    <td class="fw-bold {{ $sisaBayar > 0 ? 'text-danger' : 'text-success' }}">
                                        Rp {{ number_format($sisaBayar, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </table>

                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar {{ $isLunas ? 'bg-success' : ($isDPLunas ? 'bg-info' : 'bg-danger') }}" 
                                    role="progressbar"
                                    style="width: {{ $progress }}%;" 
                                    aria-valuenow="{{ $progress }}"
                                    aria-valuemin="0" 
                                    aria-valuemax="100">
                                    {{ number_format($progress, 0) }}%
                                </div>
                            </div>
                            <div class="mt-2">
                                @if($isLunas)
                                    <span class="badge bg-success fs-6"><i class="bi bi-check-circle me-1"></i> Pembayaran Sudah Lunas</span>
                                @elseif($isDPLunas)
                                    <span class="badge bg-info fs-6"><i class="bi bi-hourglass-split me-1"></i> DP Sudah Lunas - Menunggu Pelunasan</span>
                                @else
                                    <span class="badge bg-danger fs-6"><i class="bi bi-exclamation-circle me-1"></i> Belum Ada Pembayaran</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6 text-end align-self-end">
                            @if (!in_array($sewa->status, ['selesai', 'dibatalkan', 'batal', 'expired', 'belum bayar', 'pending']))
                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                data-bs-target="#ubahStatusModal" data-id="{{ $sewa->id }}"
                                data-status="{{ $sewa->status }}">
                                <i class="bi bi-check-circle me-1"></i> Selesaikan Sewa
                            </button>
                            @endif

                            <div class="modal fade" id="ubahStatusModal" tabindex="-1"
                                aria-labelledby="ubahStatusModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form id="ubahStatusForm" method="POST"
                                            action="{{ route('warehouse.updateStatus') }}">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="ubahStatusModalLabel">Ubah Status Sewa</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>

                                            <div class="modal-body">
                                                <input type="hidden" name="id" id="sewaId">

                                                <div class="mb-3">
                                                    <label class="form-label">Konfirmasi Status</label>
                                                    <input type="hidden" name="status" value="selesai">
                                                    <div class="alert alert-info">
                                                        <i class="bi bi-info-circle me-2"></i>
                                                        Anda akan mengubah status penyewaan menjadi <strong>"Selesai"</strong>.
                                                        <br><br>
                                                        <small class="text-muted">
                                                            Stok barang akan otomatis dikembalikan setelah status diubah menjadi selesai.
                                                        </small>
                                                    </div>
                                                    
                                                    <div class="mt-3">
                                                        <label class="form-label fw-bold">Catatan Pengembalian (Opsional)</label>
                                                        <textarea name="catatan_pengembalian" class="form-control" rows="3" placeholder="Contoh: Barang lengkap, Kursi 2 unit rusak ringan..."></textarea>
                                                        <small class="text-muted">Catatan ini akan terlihat oleh penyewa.</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light"
                                                    data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-success">Simpan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    @if ($detailSewa && $detailSewa->count())
                        <hr>
                        <h6 class="fw-bold mb-3 mt-3">Daftar Barang yang Disewa</h6>
                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Barang</th>
                                        <th>Qty</th>
                                        <th>Harga Satuan</th>
                                        <th>Durasi</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($detailSewa as $detail)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $detail->barang->nama_barang ?? '-' }}</td>
                                            <td>{{ $detail->qty }}</td>
                                            <td>Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                                            <td>{{ $durasiHari }} hari</td>
                                            <td>
                                                Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                                <br><small class="text-muted">({{ number_format($detail->harga_satuan, 0, ',', '.') }} × {{ $detail->qty }} × {{ $durasiHari }})</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-dark">
                                        <th colspan="5" class="text-end">Total</th>
                                        <th>Rp {{ number_format($sewa->total_biaya, 0, ',', '.') }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('warehouse.penyewaan') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali ke Daftar
                </a>
                <a href="{{ route('warehouse.invoice', $sewa->id) }}" class="btn btn-success" target="_blank">
                    <i class="bi bi-printer"></i> Cetak Invoice
                </a>
            </div>
        </div>
    </main><!--End app-wrapper-->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ubahStatusModal = document.getElementById('ubahStatusModal');
            const statusSelect = document.getElementById('status');

            // Saat modal ditampilkan
            ubahStatusModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const sewaId = button.getAttribute('data-id');
                const status = button.getAttribute('data-status');

                // Set data ke modal
                document.getElementById('sewaId').value = sewaId;
                statusSelect.value = status;

                updateStatusColor(statusSelect.value);
            });

            // Saat pilihan status berubah, update warna preview label
            statusSelect.addEventListener('change', function() {
                updateStatusColor(this.value);
            });

            function updateStatusColor(status) {
                const label = document.querySelector('#ubahStatusModalLabel');
                label.className = 'modal-title fw-bold'; // reset dulu

                switch (status) {
                    case 'pending':
                        label.classList.add('text-warning');
                        break;
                    case 'disetujui':
                        label.classList.add('text-success');
                        break;
                    case 'berjalan':
                        label.classList.add('text-info');
                        break;
                    case 'selesai':
                        label.classList.add('text-primary');
                        break;
                    case 'dibatalkan':
                        label.classList.add('text-danger');
                        break;
                    default:
                        label.classList.add('text-muted');
                }
            }
        });
    </script>



@endsection

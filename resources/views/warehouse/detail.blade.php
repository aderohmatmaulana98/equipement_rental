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
                @if ($sewa->status == 'pending') bg-warning text-dark
                @elseif($sewa->status == 'disetujui') bg-success
                @elseif($sewa->status == 'berjalan') bg-info text-dark
                @elseif($sewa->status == 'selesai') bg-primary
                @else bg-secondary @endif">
                        {{ ucfirst($sewa->status) }}
                    </span>
                </div>

                <div class="card-body">
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
                                    <td>{{ \Carbon\Carbon::parse($sewa->jam_acara)->locale('id')->isoFormat('HH.mm [WIB]') }}

</td>
                                <tr>
                                    <th class="text-muted">Alamat Acara</th>
                                    <td>{{ $sewa->alamat_acara }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
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
                                    <th class="text-muted">Batas Konfirmasi</th>
                                    <td>{{ \Carbon\Carbon::parse($sewa->batas_waktu_pembayaran)->locale('id')->isoFormat('dddd, D MMMM Y') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-2">Rincian Pembayaran</h6>
                            <table class="table table-sm">
                                <tr>
                                    <th>Total Biaya</th>
                                    <td>Rp {{ number_format($sewa->total_biaya, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Uang Muka (DP)</th>
                                    <td>Rp {{ number_format($sewa->uang_muka, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Sisa Pembayaran</th>
                                    <td>
                                        Rp {{ number_format($sewa->total_biaya - $sewa->uang_muka, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </table>

                            <div class="progress" style="height: 10px;">
                                @php
                                    $progress =
                                        $sewa->uang_muka > 0 ? ($sewa->uang_muka / $sewa->total_biaya) * 100 : 0;
                                @endphp
                                <div class="progress-bar bg-success" role="progressbar"
                                    style="width: {{ $progress }}%;" aria-valuenow="{{ $progress }}"
                                    aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                            <small class="text-muted">
                                Pembayaran {{ number_format($progress, 0) }}% selesai
                            </small>
                        </div>

                        <div class="col-md-6 text-end align-self-end">
                            {{-- @if ($sewa->status === 'belum bayar' || $sewa->status === 'pending') --}}
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                data-bs-target="#ubahStatusModal" data-id="{{ $sewa->id }}"
                                data-status="{{ $sewa->status }}">
                                Ubah Status
                            </button>
                            {{-- @endif --}}

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
                                                    <select class="form-select" name="status" id="status">
                                                        <option value="pending">Pending</option>
                                                        <option value="disetujui">Disetujui</option>
                                                        <option value="berjalan">Berjalan</option>
                                                        <option value="selesai">Selesai</option>
                                                        <option value="dibatalkan">Dibatalkan</option>
                                                    </select>
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

                    @if ($sewa->barang && $sewa->barang->count())
                        <hr>
                        <h6 class="fw-bold mb-3 mt-3">Daftar Barang yang Disewa</h6>
                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Barang</th>
                                        <th>Harga</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sewa->barang as $barang)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $barang->nama_barang }}</td>
                                            <td>Rp {{ number_format($barang->harga, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('warehouse.penyewaan') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali ke Daftar
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

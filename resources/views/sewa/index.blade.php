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
                            <li class="breadcrumb-item"><a href="javascript:void(0)">Pages</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if(session('success'))
              <div class="alert alert-subtle-success d-flex align-items-center mb-2" id="success-alert" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
              </div>
              <script>
              // Auto hide setelah 3 detik
              setTimeout(function() {
                  var alert = document.getElementById('success-alert');
                  if (alert) {
                      var bsAlert = new bootstrap.Alert(alert);
                      bsAlert.close();
                  }
              }, 3000);
          </script>
          @endif

          @if(session('error'))
            <div class="alert alert-subtle-danger d-flex align-items-center mb-2" id="error-alert" role="alert">
                <i class="bi bi-x-circle-fill me-2"></i>
                {{ session('error') }}
            </div>
            <script>
                // Auto hide setelah 3 detik
                setTimeout(function() {
                    var alert = document.getElementById('error-alert');
                    if (alert) {
                        var bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }
                }, 3000);
            </script>
            @endif

            <div class="col-12">
                <div class="card">
                    <!--start::card-->
                   <div class="card-header">
                        <h5 class="card-title mb-0"> Table {{ $title }} </h5>
                        <!-- Static Modal Button -->
                        <a href="{{ route('sewa.create') }}" type="button" class="btn btn-primary" >
                            + Tambah Sewa
                        </a>

                    </div>
                        
                    <div class="card-body table-responsive">
                        <!-- start:: Default Navbar -->
                        <table id="example" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No</th >
                                    <th>Kode Sewa</th >
                                    <th>Tanggal Sewa</th >
                                    <th>Tanggal Acara</th >
                                    <th>Alamat Acara</th >
                                    <th>Jam Acara</th >
                                    <th>Tanggal Loading</th >
                                    <th>Jam Loading</th >
                                    <th>Tanggal Loading Out</th >
                                    <th>Total Biaya</th >
                                    <th>Uang Muka</th >
                                    <th>Batas Konfirmasi</th >
                                    <th>Status</th >
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>                   
                                @foreach($sewas as $row)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $row->kode_sewa }}</td>
                                        <td>{{ $row->tgl_sewa }}</td>
                                        <td>{{ $row->tgl_acara }}</td>
                                        <td>{{ $row->alamat_acara }}</td>
                                        <td>{{ $row->jam_acara }}</td>
                                        <td>{{ $row->tgl_loading }}</td>
                                        <td>{{ $row->jam_loading }}</td>
                                        <td>{{ $row->tgl_loading_out }}</td>
                                        <td>{{ $row->total_biaya }}</td>
                                        <td>{{ $row->uang_muka }}</td>
                                        <td>{{ $row->batas_waktu_pembayaran }}</td>
                                        <td>
                                            @if(in_array($row->status, ['pending', 'disetujui', 'berjalan', 'selesai']))
                                                @if ($row->status === 'pending')
                                                    <span class="badge bg-danger">{{ $row->status }}</span>

                                                @elseif ($row->status === 'disetujui')
                                                    <span class="badge bg-success">{{ $row->status }}</span>

                                                @elseif ($row->status === 'berjalan')
                                                    <span class="badge bg-info">{{ $row->status }}</span>

                                                @elseif ($row->status === 'selesai')
                                                    <span class="badge bg-primary">{{ $row->status }}</span>
                                                    
                                                @endif
                                            @elseif($row->status === 'belum bayar' && $row->batas_waktu_pembayaran)
                                                <span class="badge bg-danger">Belum Bayar</span>
                                                <span>Bayar dan konfirmasi sebelum :</span>
                                                <span id="countdown-{{ $row->id }}" class="text-danger" 
                                                    data-deadline="{{ \Carbon\Carbon::parse($row->batas_waktu_pembayaran)->toIso8601String() }}">
                                                </span>
                                            @elseif($row->status === 'batal' || $row->status === 'dibatalkan')
                                                <span class="badge bg-secondary">Batal</span>
                                            @else
                                                <span class="badge bg-secondary">Tidak diketahui</span>
                                            @endif
                                        </td>                                 
                                        <td>
                                            @if (!in_array($row->status, ['pending', 'disetujui', 'berjalan', 'selesai']))
                                            {{-- <a href="/sewa/confirm_pay/{{ $row->id }}" class="btn btn-primary btn-sm">Konfirmasi Pembayaran</a> --}}
                                            <a href="javascript:void(0)" class="btn btn-primary btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editModal{{ $row->id }}">
                                                Konfirmasi Pembayaran
                                            </a>

                                            <!-- Modal Edit -->
                                            <div class="modal fade" id="editModal{{ $row->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editModalLabel{{ $row->id }}" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="editModalLabel{{ $row->id }}">Konfirmasi Pembayaran</h5>
                                                            <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close">
                                                                <i class="ri-close-large-line fw-semibold"></i>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form id="editForm{{ $row->id }}" action="{{ route('sewa.confirm', $row->id) }}" method="post" enctype="multipart/form-data">
                                                                @csrf
                                                                @method('PUT')                                                                
                                                             
                                                                   
                                                                <input type="text" class="form-control" id="sisa_pembayaran" name="sisa_pembayaran" hidden value="{{ $row->uang_muka }}" required>
                                                            
                                                                <div class="mb-3">
                                                                    <label for="no_rekening" class="col-form-label">No Rekening:</label>
                                                                    <input type="text" class="form-control" id="no_rekening" name="no_rekening"  required>
                                                                </div>                                                             
                                                                <div class="mb-3">
                                                                    <label for="bukti_pembayaran" class="col-form-label">Bukti Pembayaran:</label>
                                                                    <input type="file" class="form-control" id="bukti_pembayaran" name="bukti_pembayaran">
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-primary">Save changes</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            @else
                                                <span>-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>                           
                        </table>

                        <!-- end:: Default Navbar -->

                    </div>
                </div>
                <!--end::card-->
            </div>
        </div><!--End container-fluid-->
    </main><!--End app-wrapper-->


<script>
document.addEventListener("DOMContentLoaded", function () {
    const countdownElements = document.querySelectorAll("[id^='countdown-']");

    countdownElements.forEach(function (element) {
        const deadline = new Date(element.dataset.deadline).getTime();

        const timer = setInterval(function () {
            const now = new Date().getTime();
            const distance = deadline - now;

            if (distance <= 0) {
                clearInterval(timer);
                element.innerHTML = '<span class="badge bg-secondary">Batal</span>';
                element.classList.remove('text-danger');
                return;
            }

            // Hitung sisa waktu
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            // Format tampilannya
            element.textContent = `${hours}j ${minutes}m ${seconds}d`;
        }, 1000);
    });
});
</script>
@endsection
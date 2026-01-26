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
                                    <th>Uang Muka (DP)</th >
                                    <th>Sisa Pembayaran</th >
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
                                        <td>Rp {{ number_format($row->total_biaya, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($row->uang_muka, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($row->sisa_pembayaran, 0, ',', '.') }}</td>
                                        <td>{{ $row->batas_waktu_pembayaran }}</td>
                                        <td>
                                            @if($row->status === 'belum bayar')
                                                <span class="badge bg-danger">Belum Bayar</span>
                                            @elseif($row->status === 'pending')
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @elseif($row->status === 'dp_lunas')
                                                <span class="badge bg-info">DP Lunas</span>
                                            @elseif($row->status === 'disetujui')
                                                <span class="badge bg-success">Disetujui (Lunas)</span>
                                            @elseif($row->status === 'berjalan')
                                                <span class="badge bg-primary">Berjalan</span>
                                            @elseif($row->status === 'selesai')
                                                <span class="badge bg-secondary">Selesai</span>
                                            @elseif($row->status === 'batal' || $row->status === 'dibatalkan')
                                                <span class="badge bg-dark">Batal</span>
                                            @elseif($row->status === 'expired')
                                                <span class="badge bg-dark">Expired</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $row->status }}</span>
                                            @endif
                                        </td>                                 
                                        <td>
                                            {{-- Tombol Bayar DP --}}
                                            @if ($row->status === 'belum bayar')
                                                <form id="payment-form-{{ $row->id }}" onsubmit="payNow(event, {{ $row->id }}, 'dp')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-primary btn-sm">Bayar DP</button>
                                                </form>
                                            @endif

                                            {{-- Tombol Pelunasan --}}
                                            @if ($row->status === 'dp_lunas')
                                                <form id="pelunasan-form-{{ $row->id }}" onsubmit="payNow(event, {{ $row->id }}, 'pelunasan')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm">Pelunasan</button>
                                                </form>
                                            @endif

                                            <a href="{{ route('sewa.show', $row->id) }}" class="btn btn-info btn-sm">Detail</a>
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

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>

<script>
async function payNow(event, id, type) {
    event.preventDefault();

    // Tentukan URL berdasarkan tipe pembayaran
    let url = type === 'pelunasan' ? `/payment/${id}/pelunasan` : `/payment/${id}`;

    try {
        const response = await fetch(url, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json",
                "Content-Type": "application/json"
            },
        });

        const data = await response.json();

        if (data.snapToken) {
            snap.pay(data.snapToken, {
                onSuccess: function(result) {
                    alert("Pembayaran sukses!");
                    console.log(result);
                    location.reload();
                },
                onPending: function(result) {
                    alert("Menunggu pembayaran...");
                    console.log(result);
                    location.reload();
                },
                onError: function(result) {
                    alert("Pembayaran gagal!");
                    console.log(result);
                    location.reload();
                },
                onClose: function() {
                    // Tidak perlu alert, user bisa klik tombol lagi
                    console.log("Popup ditutup tanpa menyelesaikan pembayaran");
                }
            });
        } else {
            console.error("Token tidak ditemukan:", data);
            alert("Gagal mendapatkan token Midtrans: " + (data.error ?? "Unknown error"));
        }
    } catch (err) {
        console.error("Terjadi error:", err);
        alert("Gagal memproses pembayaran");
    }
}
</script>

@endsection
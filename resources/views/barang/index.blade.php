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
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                            + Tambah Barang
                        </button>

                        <!-- start:: Static Modal -->
                        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="staticBackdropLabel">Tambah Barang</h5>
                                        <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close">
                                            <i class="ri-close-large-line fw-semibold"></i>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('barang.store') }}" method="post" enctype="multipart/form-data">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="jenis_barang_id" class="col-form-label">Jenis Barang:</label>
                                                <select name="jenis_barang_id" id="jenis_barang_id" class="form-select">
                                                    <option value="">-- Pilih Jenis Barang --</option>
                                                    @foreach ($jenisBarang as $jenis)
                                                        <option value="{{ $jenis->id }}">{{ $jenis->nama_jenis_barang }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="nama_barang" class="col-form-label">Nama Barang:</label>
                                                <input type="text" class="form-control" id="nama_barang" name="nama_barang">
                                            </div>
                                            <div class="mb-3">
                                                <label for="satuan" class="col-form-label">Satuan:</label>
                                                <input type="text" class="form-control" id="satuan" name="satuan">
                                            </div>
                                            <div class="mb-3">
                                                <label for="harga" class="col-form-label">Harga:</label>
                                                <input type="number" class="form-control" id="harga" name="harga">
                                            </div>
                                            <div class="mb-3">
                                                <label for="keterangan" class="col-form-label">Keterangan:</label>
                                                <textarea name="keterangan" class="form-control" id="keterangan" cols="30" rows="10"></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label for="stok" class="col-form-label">Stok:</label>
                                                <input type="number" class="form-control" id="stok" name="stok">
                                            </div>
                                            <div class="mb-3">
                                                <label for="gambar" class="col-form-label">Gambar:</label>
                                                <input type="file" class="form-control" id="gambar" name="gambar">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Save changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div><!--End modal-->
                    </div>
                    <div class="card-body">
                        <!-- start:: Default Navbar -->
                        <table id="example" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No</th >
                                    <th>Jenis Barang</th >
                                    <th>Nama Barang</th >
                                    <th>Satuan</th >
                                    <th>Harga</th >
                                    <th>Keterangan</th >
                                    <th>Stok</th >
                                    <th>Gambar</th >
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>                   
                                @foreach($barangs as $row)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $row->jenis_barang }}</td>
                                        <td>{{ $row->nama_barang }}</td>
                                        <td>{{ $row->satuan }}</td>
                                        <td>{{ $row->harga }}</td>
                                        <td>{{ $row->keterangan }}</td>
                                        <td>{{ $row->stok }}</td>
                                        <td>
                                            <img src="{{ asset('storage/' . $row->gambar) }}" alt="gambar" height="100">
                                        </td>
                                        <td>
                                            <!-- Tombol Edit -->
                                            <a href="javascript:void(0)" class="btn btn-primary btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editModal{{ $row->id }}">
                                                Edit
                                            </a>

                                            <!-- Modal Edit -->
                                            <div class="modal fade" id="editModal{{ $row->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editModalLabel{{ $row->id }}" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="editModalLabel{{ $row->id }}">Edit Barang</h5>
                                                            <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close">
                                                                <i class="ri-close-large-line fw-semibold"></i>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form id="editForm{{ $row->id }}" action="{{ route('barang.update', $row->id) }}" method="post" enctype="multipart/form-data">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="mb-3">
                                                                    <label for="jenis_barang_id" class="col-form-label">Jenis Barang:</label>
                                                                    <select name="jenis_barang_id" id="jenis_barang_id" class="form-select">
                                                                        <option value="">-- Pilih Jenis Barang --</option>
                                                                        @foreach ($jenisBarang as $jenis)
                                                                            <option value="{{ $jenis->id }}" {{ $row->jenis_barang_id == $jenis->id ? 'selected' : '' }}>
                                                                                {{ $jenis->nama_jenis_barang }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="nama_barang" class="col-form-label">Nama Barang:</label>
                                                                    <input type="text" class="form-control" id="nama_barang" name="nama_barang" value="{{ $row->nama_barang }}" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="satuan" class="col-form-label">Satuan:</label>
                                                                    <input type="text" class="form-control" id="satuan" name="satuan" value="{{ $row->satuan }}" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="harga" class="col-form-label">Harga:</label>
                                                                    <input type="number" class="form-control" id="harga" name="harga" value="{{ $row->harga }}" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="keterangan" class="col-form-label">Keterangan:</label>
                                                                    <textarea name="keterangan" class="form-control" id="keterangan" cols="30" rows="10" required>{{ $row->keterangan }}</textarea>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="stok" class="col-form-label">Stok:</label>
                                                                    <input type="text" class="form-control" id="stok" name="stok" value="{{ $row->stok }}" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="gambar" class="col-form-label">Gambar:</label>
                                                                    <input type="file" class="form-control" id="gambar" name="gambar">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <img src="{{ asset('storage/' . $row->gambar) }}" alt="gambar" height="100">
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
                                            <form action="{{ route('barang.destroy', $row->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin mau hapus?')">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>No</th >
                                    <th>Jenis Barang</th >
                                    <th>Nama Barang</th >
                                    <th>Satuan</th >
                                    <th>Harga</th >
                                    <th>Keterangan</th >
                                    <th>Stok</th >
                                    <th>Gambar</th >
                                    <th>Aksi</th>
                                </tr>
                            </tfoot>
                        </table>

                        <!-- end:: Default Navbar -->

                    </div>
                </div>
                <!--end::card-->
            </div>
        </div><!--End container-fluid-->
    </main><!--End app-wrapper-->

@endsection
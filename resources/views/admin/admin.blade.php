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
                            + Tambah Admin
                        </button>

                        <!-- start:: Static Modal -->
                        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="staticBackdropLabel">Tambah Admin</h5>
                                        <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close">
                                            <i class="ri-close-large-line fw-semibold"></i>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('admin.store') }}" method="post">
                                            @csrf
                                                <div class="mb-3">
                                                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="name" placeholder="name" name="name" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="no_hp" class="form-label">No HP<span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="no_hp" placeholder="No HP" name="no_hp" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="email" placeholder="Email" name="email" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                                    <div class="position-relative">
                                                        <input type="password" class="form-control" id="password" placeholder="Password" name="password" required>
                                                        <button type="button" class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted toggle-password" id="toggle-password" data-target="password"><i class="ri-eye-off-line align-middle"></i></button>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="confirmationPassword" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                                    <div class="position-relative">
                                                        <input type="password" class="form-control" id="confirmationPassword" name="confirmationPassword" placeholder="Confirm Password" required>
                                                        <button type="button" class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted toggle-password" id="toggle-password" data-target="ConfirmPassword"><i class="ri-eye-off-line align-middle"></i></button>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="role_id" class="col-form-label">Role <span class="text-danger">*</span></label>
                                                    <select name="role_id" id="role_id" class="form-select">
                                                        <option value="">-- Pilih Role --</option>
                                                        @foreach ($roles as $role)
                                                            <option value="{{ $role->id }}" >
                                                                {{ $role->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
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
                                    <th>Nama Admin</th >
                                    <th>Email</th >
                                    <th>Nomor HP</th >
                                    <th>Role</th >
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>                   
                                @foreach($users as $row)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $row->name }}</td>
                                        <td>{{ $row->email }}</td>
                                        <td>{{ $row->no_hp }}</td>
                                        <td>{{ $row->role->name }}</td>
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
                                                            <h5 class="modal-title" id="editModalLabel{{ $row->id }}">Edit Admin</h5>
                                                            <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close">
                                                                <i class="ri-close-large-line fw-semibold"></i>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form id="editForm{{ $row->id }}" action="{{ route('admin.update', $row->id) }}" method="post">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="mb-3">
                                                                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control" id="name" placeholder="name" name="name" value="{{ $row->name }}" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="no_hp" class="form-label">No HP<span class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control" id="no_hp" placeholder="No HP" name="no_hp" value="{{ $row->no_hp }}" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control" id="email" placeholder="Email" name="email" value="{{ $row->email }}" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="role_id" class="col-form-label">Role:</label>
                                                                    <select name="role_id" id="role_id" class="form-select">
                                                                        <option value="">-- Pilih Role --</option>
                                                                        @foreach ($roles as $role)
                                                                            <option value="{{ $role->id }}" {{ $row->role->id == $role->id ? 'selected' : '' }}>
                                                                                {{ $role->name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
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
                                            <form action="{{ route('admin.delete', $row->id) }}" method="POST" style="display:inline;">
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
                                    <th>Nama Admin</th >
                                    <th>Email</th >
                                    <th>Nomor HP</th >
                                    <th>Role</th >
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

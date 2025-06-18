@extends('layouts.app')

@section('content')

<div class="container-fluid">
  <div class="card shadow mb-4">
        <div class="card-header py-3">
        <h1 class="mb-1">Daftar Pengguna</h1> <!-- Mengubah judul menjadi 'Daftar Pengguna' -->
        <div class="card-body">
            <!-- Tampilkan pesan sukses jika ada -->
@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

            <!-- Form Filter dan Search -->
            <form method="GET" action="{{ route('user.index') }}" class="mb-4">
                <div class="row g-2">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" placeholder="Cari Nama, NIP, Telepon" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="tgllahir" class="form-control" value="{{ request('tgllahir') }}">
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="tglmasuk" class="form-control" value="{{ request('tglmasuk') }}">
                    </div>
                    <div class="col-md-3">
                      <select name="is_active" class="form-control">
                          <option value="">-- Status Akun --</option>
                          <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Aktif</option>
                          <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Nonaktif</option>
                      </select>
                    </div>

                    <div class="col-md-3">
                        <select name="division_id" class="form-control">
                            <option value="">-- Pilih Divisi --</option>
                            @foreach($divisions as $division)
                                <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>
                                    {{ $division->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-3 d-flex">
                        <button type="submit" class="btn btn-primary me-4">Filter</button> <!-- Margin kanan lebih besar -->
                        <a href="{{ route('user.index') }}" class="btn btn-secondary ms-4">Reset</a> <!-- Margin kiri lebih besar -->
                    </div>
                    
                </div>
            </form>

            <div class="d-flex justify-content-start gap-3 mb-4">
                <!-- Tombol Tambah Karyawan -->
            <a href="{{ route('user.create') }}" class="btn btn-success">
                <i class="fas fa-plus-circle"></i> Tambah Karyawan
            </a>

    <!-- Tombol Lihat Divisi -->
            <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#divisiModal">
                Lihat Divisi
            </button>

            <!-- Tombol Lihat Posisi -->
            <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#posisiModal">
                Lihat Posisi
            </button>

            <!-- Tombol Tambah Divisi -->
            <a href="#" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addDivisionModal">
                <i class="fas fa-plus-circle"></i> Tambah Divisi
            </a>

            <!-- Tombol Tambah Posisi -->
            <a href="#" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#addPositionModal">
                <i class="fas fa-plus-circle"></i> Tambah Posisi
            </a>
            </div>


            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>jenis kelamin</th>
                        <th>NIP</th>
                        <th>No Telepon</th>
                        <th>Tanggal Lahir</th>
                        <th>Tanggal Masuk</th>
                        <th>Alamat</th>
                        <th>Divisi</th>
                        <th>Posisi</th> 
                        <th>Status</th>
                        <th>Sisa Cuti</th>
                        <th>Aksi</th> <!-- Kolom Aksi -->
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $user)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $user->name }}</td>
                             <td>{{ $user->gender }}</td>
                            <td>{{ $user->nip }}</td>
                            <td>{{ $user->phone_number }}</td>
                            <td>{{ \Carbon\Carbon::parse($user->tgllahir)->format('d-m-Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($user->tglmasuk)->format('d-m-Y') }}</td>
                            <td>{{ $user->address }}</td>
                            <td>
                                {{ optional(optional(optional($user->user)->details->first())->workDivisions->first())->division->name ?? '-' }}
                            </td>
                            <td>
                                {{ optional(optional($user->user)->details->first())->position->name ?? '-' }}
                            </td>
                            <td>
                                @if($user->user->status === 'active')
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                {{ $user->user->leaveBalance->remaining_leave ?? '0' }} hari
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center align-items-center">
                                    <a href="{{ route('user.edit', $user->id) }}" class="btn btn-sm btn-warning me-2">Edit</a>

                                    <form action="{{ route('user.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus?')" class="me-2">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                    </form>

                                    <form action="{{ route('user.toggleStatus', $user->user->id) }}" method="POST" onsubmit="return confirm('Ubah status pengguna ini?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $user->user->status === 'active' ? 'btn-secondary' : 'btn-success' }}">
                                            {{ $user->user->status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center">Tidak ada data pengguna ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>

                
                </table>
            </div>
        </div>
  </div>
</div>
{{-- modal daftar divisi --}}
<!-- Modal Divisi -->
<div class="modal fade" id="divisiModal" tabindex="-1" aria-labelledby="divisiModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Daftar Divisi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <table class="table">
          <thead>
            <tr>
              <th>Nama Divisi</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach($divisions as $division)
              <tr>
                <form action="{{ route('division.update', $division->id) }}" method="POST">
                  @csrf
                  @method('PUT')
                  <td>
                    <input type="text" name="name" class="form-control" value="{{ $division->name }}">
                  </td>
                  <td>
                    <button type="submit" class="btn btn-sm btn-primary">Update</button>
                </form>
                <form action="{{ route('division.destroy', $division->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus divisi ini?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                </form>
                  </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

{{-- modal daftar posisi --}}
<!-- Modal Posisi -->
<div class="modal fade" id="posisiModal" tabindex="-1" aria-labelledby="posisiModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Daftar Posisi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <table class="table">
          <thead>
            <tr>
              <th>Nama Posisi</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach($positions as $position)
              <tr>
                <form action="{{ route('position.update', $position->id) }}" method="POST">
                  @csrf
                  @method('PUT')
                  <td>
                    <input type="text" name="name" class="form-control" value="{{ $position->name }}">
                  </td>
                  <td>
                    <button type="submit" class="btn btn-sm btn-primary">Update</button>
                </form>
                <form action="{{ route('position.destroy', $position->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus posisi ini?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                </form>
                  </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
{{-- modal tambah divisi --}}
<!-- Modal Tambah Divisi -->
<div class="modal fade" id="addDivisionModal" tabindex="-1" aria-labelledby="addDivisionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDivisionModalLabel">Tambah Divisi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('division.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="id" class="form-label">ID divisi</label>
                        <input type="text" name="id" id="id" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Divisi</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Tambah Divisi</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal untuk Posisi -->
<div class="modal fade" id="addPositionModal" tabindex="-1" aria-labelledby="addPositionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPositionModalLabel">Tambah Posisi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('position.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="id" class="form-label">ID Posisi</label>
                        <input type="text" name="id" id="id" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="position_name" class="form-label">Nama Posisi</label>
                        <input type="text" name="position_name" id="position_name" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Tambah Posisi</button>
                </div>
            </form>
        </div>
    </div>
</div>
s
{{--  --}}
@endsection

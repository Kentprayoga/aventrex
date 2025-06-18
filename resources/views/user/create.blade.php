@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Tambah User Baru</h2>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-alert">
        <strong>Sukses!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <form action="{{ route('user.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Nama</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <!-- NIP -->
        <div class="mb-3">
            <label for="nip" class="form-label">NIP</label>
            <input type="text" name="nip" class="form-control" required>
        </div>

        <!-- Nomor Telepon -->
        <div class="mb-3">
            <label for="phone_number" class="form-label">Nomor Telepon</label>
            <input type="text" name="phone_number" class="form-control" required>
        </div>

        <!-- Tanggal Lahir -->
        <div class="mb-3">
            <label for="tgllahir" class="form-label">Tanggal Lahir</label>
            <input type="date" name="tgllahir" class="form-control" required>
        </div>

        <!-- Gender -->
        <div class="mb-4">
            <label for="gender" class="block">Jenis Kelamin</label>
            <select name="gender" id="gender" class="form-select">
                <option value="">-- Pilih --</option>
                <option value="Laki-laki" {{ old('gender', $profile->gender ?? '') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                <option value="Perempuan" {{ old('gender', $profile->gender ?? '') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
            </select>
        </div>

        <!-- Tanggal Masuk -->
        <div class="mb-3">
            <label for="tglmasuk" class="form-label">Tanggal Masuk</label>
            <input type="date" name="tglmasuk" class="form-control" required>
        </div>

        <!-- Alamat -->
        <div class="mb-3">
            <label for="address" class="form-label">Alamat</label>
            <textarea name="address" class="form-control" rows="2"></textarea>
        </div>

        <!-- Posisi -->
        <div class="mb-3">
            <label for="position_id" class="form-label">Posisi</label>
            <select name="position_id" class="form-control" required>
                <option value="">-- Pilih Posisi --</option>
                @foreach($positions as $position)
                    <option value="{{ $position->id }}">{{ $position->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Divisi -->
        <div class="mb-3">
            <label for="division_id" class="form-label">Divisi</label>
            <select name="division_id" class="form-control" required>
                <option value="">-- Pilih Divisi --</option>
                @foreach($divisions as $division)
                    <option value="{{ $division->id }}">{{ $division->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Role (di sini role user otomatis ID 2) -->
        <input type="hidden" name="role_id" value="2"> <!-- ID role 'user' -->

        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>

</div>
<script>
    // Pastikan tanggal lahir dan tanggal masuk dalam format yang benar (YYYY-MM-DD)
    document.addEventListener("DOMContentLoaded", function() {
        let tgllahirInput = document.querySelector('input[name="tgllahir"]');
        let tglmasukInput = document.querySelector('input[name="tglmasuk"]');
        
        if (tgllahirInput && tgllahirInput.value) {
            let tgllahirDate = new Date(tgllahirInput.value);
            tgllahirInput.value = tgllahirDate.toISOString().split('T')[0];
        }
        
        if (tglmasukInput && tglmasukInput.value) {
            let tglmasukDate = new Date(tglmasukInput.value);
            tglmasukInput.value = tglmasukDate.toISOString().split('T')[0];
        }
    });
</script>
@endsection

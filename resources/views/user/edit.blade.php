@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Edit Data Karyawan</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('user.update', $profile->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Nama -->
        <div class="mb-3">
            <label for="name" class="form-label">Nama</label>
            <input type="text" name="name" value="{{ old('name', $profile->name) }}" class="form-control" required>
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" value="{{ old('email', $profile->user->email) }}" class="form-control" required>
        </div>

        <!-- NIP -->
        <div class="mb-3">
            <label for="nip" class="form-label">NIP</label>
            <input type="text" name="nip" value="{{ old('nip', $profile->nip) }}" class="form-control" required>
        </div>

        <!-- No Telepon -->
        <div class="mb-3">
            <label for="phone_number" class="form-label">No Telepon</label>
            <input type="text" name="phone_number" value="{{ old('phone_number', $profile->phone_number) }}" class="form-control" required>
        </div>

        <!-- Tanggal Lahir -->
        <div class="mb-3">
            <label for="tgllahir" class="form-label">Tanggal Lahir</label>
            <input type="date" name="tgllahir" value="{{ old('tgllahir', $profile->tgllahir) }}" class="form-control" required>
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
            <input type="date" name="tglmasuk" value="{{ old('tglmasuk', $profile->tglmasuk) }}" class="form-control" required>
        </div>

        <!-- Alamat -->
        <div class="mb-3">
            <label for="address" class="form-label">Alamat</label>
            <textarea name="address" class="form-control" rows="2">{{ old('address', $profile->address) }}</textarea>
        </div>

        <!-- Posisi -->
        <div class="mb-3">
            <label for="position_id" class="form-label">Posisi</label>
            <select name="position_id" class="form-control" required>
                @foreach($positions as $position)
                    <option value="{{ $position->id }}" {{ $profile->user->details->first()->position_id == $position->id ? 'selected' : '' }}>
                        {{ $position->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Divisi -->
        <div class="mb-3">
            <label for="division_id" class="form-label">Divisi</label>
            <select name="division_id" class="form-control" required>
                @foreach($divisions as $division)
                    <option value="{{ $division->id }}"
                        {{ $profile->user->details->first()->workDivisions->first()->division_id == $division->id ? 'selected' : '' }}>
                        {{ $division->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Tombol Submit -->
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('user.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection

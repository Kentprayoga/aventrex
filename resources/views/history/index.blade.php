@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h1 class="mb-4">Daftar Pengajuan</h1>
            <div class="card-body">

                <!-- Form Filter -->
                <form action="{{ route('history.index') }}" method="get" class="card p-4 mb-4 shadow-sm">
                    <div class="row">
                        <!-- Kategori -->
                        <div class="col-md-3 mb-3">
                            <label for="category_id" class="form-label">Kategori</label>
                            <select name="categorie_id" id="categorie_id" class="form-select">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('categorie_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>

                        </div>

                        <!-- Status -->
                        <div class="col-md-3 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">-- Pilih Status --</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>

                        <!-- Tanggal Mulai -->
                        <div class="col-md-3 mb-3">
                            <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ request('tanggal_mulai') }}" class="form-control">
                        </div>

                        <!-- Tanggal Selesai -->
                        <div class="col-md-3 mb-3">
                            <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" id="tanggal_selesai" value="{{ request('tanggal_selesai') }}" class="form-control">
                        </div>
                    </div>

                    <!-- Filter Button -->
                    <button type="submit" class="btn btn-primary">Filter</button>
                </form>

                <!-- Cetak Semua Dokumen Button -->
                <a href="{{ route('history.cetak') }}" class="btn btn-success mb-4">Cetak Semua Dokumen</a>

                <!-- Tabel Dokumen -->
                <div class="table-responsive">
                    @if($documents->isEmpty())
                        <p class="text-center">Tidak ada data yang ditemukan.</p>
                    @else
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Nomor Dokumen</th>
                                    <th>Kategori</th>
                                    <th>Status</th>
                                    <th>File</th> 
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($documents as $doc)
                                    <tr>
                                        <td>{{ $doc->name }}</td>
                                        <td>{{ $doc->document_number }}</td>
                                        <td>{{ $doc->template->category->name ?? 'Kategori tidak tersedia' }}</td>
                                        <td>{{ $doc->approval->status ?? 'Belum ada' }}</td>
                                        <td>
                                            @if ($doc->template->file_path)
                                                <a href="{{ asset('storage/' . $doc->template->file_path) }}" target="_blank">Lihat File</a>
                                            @else
                                                <span class="text-muted">Tidak ada file</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('history.cetak.satu', $doc->id) }}" class="btn btn-info btn-sm">Cetak</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

            </div>
        </div>
    </div>        
</div>
@endsection

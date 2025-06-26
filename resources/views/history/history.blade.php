@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4 fw-bold">📜 Riwayat Pengajuan Dokumen</h2>

    {{-- Filter Form --}}
    <form method="GET" action="{{ route('history.history') }}" class="row g-3 mb-4">
        <div class="col-md-3">
            <label for="from" class="form-label">Dari Tanggal</label>
            <input type="date" name="from" class="form-control" value="{{ request('from') }}">
        </div>
        <div class="col-md-3">
            <label for="to" class="form-label">Sampai Tanggal</label>
            <input type="date" name="to" class="form-control" value="{{ request('to') }}">
        </div>
        <div class="col-md-3">
            <label for="category" class="form-label">Kategori</label>
            <select name="category" class="form-select">
                <option value="all">Semua</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->name }}" {{ request('category') == $cat->name ? 'selected' : '' }}>
                        {{ ucfirst($cat->name) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-filter"></i> Filter
            </button>
        </div>
    </form>

    {{-- Tombol Download Filtered --}}
    <form method="GET" action="{{ route('approvals.downloadFiltered') }}" class="mb-3">
        <input type="hidden" name="from" value="{{ request('from') }}">
        <input type="hidden" name="to" value="{{ request('to') }}">
        <input type="hidden" name="category" value="{{ request('category') }}">
        <button type="submit" class="btn btn-outline-success">
            <i class="bi bi-download"></i> Download Filtered PDF
        </button>
    </form>

    {{-- Tabel Data --}}
    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>NIP</th>
                        <th>Kategori</th>
                        <th>No Dokumen</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Alasan</th>
                        <th>File</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($approvals as $index => $approval)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $approval->document->user->profile->name }}</td>
                            <td>{{ $approval->document->user->profile->nip }}</td>
                            <td>{{ ucfirst($approval->document->template->category->name) }}</td>
                            <td>{{ $approval->document->document_number }}</td>
                            <td>{{ \Carbon\Carbon::parse($approval->document->tanggal_pengajuan)->format('d-m-Y H:i') }}</td>
                            <td>
                                <span class="badge text-uppercase bg-{{ 
                                    $approval->status === 'approved' ? 'success' : 
                                    ($approval->status === 'rejected' ? 'danger' : 
                                    ($approval->status === 'cancelled' ? 'secondary' : 'warning'))
                                }}">
                                    {{ $approval->status }}
                                </span>
                            </td>
                            <td>{{ $approval->alasan ?? '-' }}</td>
                            <td>
                                @if ($approval->document->file_path)
                                    <a href="{{ asset('storage/' . $approval->document->file_path) }}" 
                                    target="_blank" 
                                    class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Lihat
                                    </a>
                                @else
                                    <span class="text-muted">Tidak ada</span>
                                @endif
                            </td>

                        </tr>

                    @empty
                        <tr>
                            <td colspan="8" class="text-muted">Tidak ada data pengajuan ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

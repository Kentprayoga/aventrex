@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="fw-bold mb-4">🛡️ Riwayat Aktivitas Admin</h2>

    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-3">
            <label for="tanggal" class="form-label">Tanggal Aktivitas</label>
            <input type="date" name="tanggal" class="form-control" value="{{ request('tanggal') }}">
        </div>
        <div class="col-md-3">
            <label for="action" class="form-label">Jenis Aksi</label>
            <select name="action" class="form-select">
                <option value="all">Semua Aksi</option>
                @foreach ($actions as $act)
                    <option value="{{ $act }}" {{ request('action') == $act ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $act)) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Terapkan</button>
        </div>
    </form>
    <div class="mb-3 d-flex gap-2">
        <a href="{{ route('log.admin.export.pdf', request()->all()) }}" class="btn btn-outline-danger btn-sm">
            🧾 Export PDF
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered align-middle text-center">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Admin</th>
                    <th>Aksi</th>
                    <th>Detail</th>
                    <th>IP</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $index => $log)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $log->user->profile->name ?? '-' }}</td>
                        <td><span class="badge bg-secondary">{{ $log->action }}</span></td>
                        <td>{{ $log->detail ?? '-' }}</td>
                        <td>{{ $log->ip_address ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d-m-Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-muted text-center">Tidak ada log aktivitas admin.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

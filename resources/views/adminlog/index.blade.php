@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">Log Aktivitas Pengguna</h2>

    <!-- Filter -->
    <form method="GET" action="{{ route('adminlog.index') }}" class="mb-4">
        <div class="row g-2 align-items-center">
            <div class="col-auto">
                <input type="date" name="date" value="{{ request('date') }}" class="form-control" />
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Filter Tanggal</button>
                <a href="{{ route('adminlog.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </div>
    </form>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h4 class="m-0 fw-bold text-primary">Riwayat Log (Terbaru)</h4>
        </div>

        <div class="card-body">
            <a href="{{ route('adminlog.pdf', ['date' => request('date')]) }}" class="btn btn-danger mb-3">
                Download PDF
            </a>
            @php
                $perPage = 30;
                $currentPage = request()->get('page', 1);
                $offset = ($currentPage - 1) * $perPage;

                $paginatedLogs = $logs->slice($offset, $perPage)->values();
                $paginator = new Illuminate\Pagination\LengthAwarePaginator(
                    $paginatedLogs,
                    $logs->count(),
                    $perPage,
                    $currentPage,
                    ['path' => request()->url(), 'query' => request()->query()]
                );
            @endphp

            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pengguna</th>
                            <th>Aksi</th>
                            <th>Detail</th>
                            <th>IP Address</th>
                            <th>User Agent</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($paginator as $index => $log)
                            <tr>
                                <!-- Penomoran mundur -->
                                <td>{{ $logs->count() - ($offset + $index) }}</td>
                                <td>{{ optional($log->user->profile)->name ?? 'User dihapus' }}</td>
                                <td>{{ $log->action }}</td>
                                <td>{{ $log->detail ?? '-' }}</td>
                                <td>{{ $log->ip_address ?? '-' }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($log->user_agent, 50) }}</td>
                                <td>{{ $log->created_at->format('d-m-Y H:i:s') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data log aktivitas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination manual -->
            <nav>
                {{ $paginator->links('pagination::bootstrap-5') }}
            </nav>
        </div>
    </div>
</div>
@endsection

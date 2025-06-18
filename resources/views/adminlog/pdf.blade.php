<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Log Aktivitas</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 4px; text-align: left; }
    </style>
</head>
<body>
    <h2>Laporan Log Aktivitas Pengguna</h2>

    @if($filterDate)
        <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($filterDate)->format('d-m-Y') }}</p>
    @else
        <p><strong>Tanggal:</strong> Semua (terbaru maksimal 100)</p>
    @endif

    <table>
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
            @forelse($logs as $index => $log)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ optional($log->user->profile)->name ?? 'User dihapus' }}</td>
                    <td>{{ $log->action }}</td>
                    <td>{{ $log->detail ?? '-' }}</td>
                    <td>{{ $log->ip_address ?? '-' }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($log->user_agent, 40) }}</td>
                    <td>{{ $log->created_at->format('d-m-Y H:i:s') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>

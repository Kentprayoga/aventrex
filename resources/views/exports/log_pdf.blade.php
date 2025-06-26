<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Log PDF</title>
    <style>
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { border: 1px solid #000; padding: 4px; text-align: center; }
    </style>
</head>
<body>
    <h3 style="text-align: center">Riwayat Aktivitas</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Aksi</th>
                <th>Detail</th>
                <th>IP</th>
                <th>Waktu</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($logs as $i => $log)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $log->user->profile->name ?? '-' }}</td>
                <td>{{ $log->action }}</td>
                <td>{{ $log->detail }}</td>
                <td>{{ $log->ip_address }}</td>
                <td>{{ $log->created_at->format('d-m-Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

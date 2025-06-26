@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="fw-bold mb-4">📊 Riwayat Aktivitas</h2>
    <p class="mb-4">Silakan pilih jenis aktivitas yang ingin ditampilkan:</p>

    <div class="row justify-content-center">
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-primary">
                <div class="card-body text-center">
                    <h5 class="card-title">🛡️ Aktivitas Admin</h5>
                    <p class="card-text">Lihat semua aktivitas yang dilakukan oleh admin.</p>
                    <a href="{{ route('log.admin') }}" class="btn btn-outline-primary">Lihat Log Admin</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-success">
                <div class="card-body text-center">
                    <h5 class="card-title">👥 Aktivitas Pengguna</h5>
                    <p class="card-text">Lihat semua aktivitas dari pengguna biasa.</p>
                    <a href="{{ route('log.user') }}" class="btn btn-outline-success">Lihat Log Pengguna</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

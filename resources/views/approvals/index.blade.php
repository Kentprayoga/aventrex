@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">
            📝 Daftar Pengajuan Kategori: <span class="text-primary text-uppercase">{{ $category ?? 'cuti' }}</span>
        </h2>
        <a href="{{ route('approvals.list') }}" class="btn btn-outline-secondary">
            ← Kembali ke Kategori
        </a>
    </div>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Judul sesuai kategori --}}
    @php
        $judulKategori = match($category) {
            'cuti' => '📅 Pengajuan Cuti Tahunan',
            'izin' => '📋 Pengajuan Izin',
            'tugas_luar' => '📂 Pengajuan Tugas Luar',
            default => '📄 Pengajuan Dokumen',
        };
    @endphp

    <div class="card shadow-sm mb-5">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">{{ $judulKategori }}</h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>NIP</th>
                        <th>Nama Surat</th>
                        <th>Nama Pengaju</th>
                        <th>Nomor Dokumen</th>
                        <th>Tanggal Pengajuan</th>
                        @if ($category === 'cuti')
                            <th>Sisa Cuti</th>
                        @endif
                        <th>Status</th>
                        <th>Aksi</th>
                        <th>Upload File TTD</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($cutiTahunanApprovals as $index => $approval)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $approval->document->user->profile->nip }}</td>
                            <td>{{ $approval->document->template->name ?? '-' }}</td>
                            <td>{{ $approval->document->user->profile->name }}</td>
                            <td>{{ $approval->document->document_number }}</td>
                            <td>{{ \Carbon\Carbon::parse($approval->document->tanggal_pengajuan)->format('d-m-Y H:i') }}</td>

                            @if ($category === 'cuti')
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ $approval->document->user->leaveBalance->remaining_leave ?? '0' }} hari
                                    </span>
                                </td>
                            @endif

                            <td>
                                <span class="badge bg-warning text-dark text-uppercase">{{ $approval->status }}</span>
                            </td>
                            <td>
                                <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalCuti{{ $approval->id }}">
                                    <i class="bi bi-eye"></i> Detail
                                </button>
                            </td>
                            <td>
                                <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#uploadModal{{ $approval->id }}">
                                    <i class="bi bi-upload"></i> Upload
                                </button>
                            </td>
                        </tr>

                        {{-- Modal Detail --}}
                        <div class="modal fade" id="modalCuti{{ $approval->id }}" tabindex="-1" aria-labelledby="modalLabelCuti{{ $approval->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header bg-light">
                                        <h5 class="modal-title">🗂 Detail Pengajuan</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                    </div>
                                    <div class="modal-body text-start">
                                        <p><strong>Nama Pengaju:</strong> {{ $approval->document->user->profile->name }}</p>
                                        <p><strong>NIP:</strong> {{ $approval->document->user->profile->nip }}</p>
                                        <p><strong>Nomor Dokumen:</strong> {{ $approval->document->document_number }}</p>
                                        <p><strong>Tanggal Pengajuan:</strong> {{ \Carbon\Carbon::parse($approval->document->tanggal_pengajuan)->format('d-m-Y H:i') }}</p>
                                        @if ($category === 'cuti')
                                            <p><strong>Sisa Cuti:</strong> {{ $approval->document->user->leaveBalance->remaining_leave ?? '0' }} hari</p>
                                        @endif
                                        <p><strong>Alasan:</strong> {{ $approval->document->alasan ?? '-' }}</p>

                                        @if ($approval->document->file_path)
                                            <p><strong>Preview File Dokumen:</strong></p>
                                            <div class="ratio ratio-4x3">
                                                <iframe src="{{ asset('storage/' . $approval->document->file_path) }}" allowfullscreen></iframe>
                                            </div>
                                            <p class="mt-2">
                                                <a href="{{ asset('storage/' . $approval->document->file_path) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                                                    <i class="bi bi-download"></i> Unduh PDF
                                                </a>
                                            </p>
                                        @endif
                                    </div>
                                    <div class="modal-footer justify-content-between">
                                        <form action="{{ route('approvals.approve', $approval->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success">
                                                <i class="bi bi-check-circle"></i> Setujui
                                            </button>
                                        </form>
                                        <form action="{{ route('approvals.reject', $approval->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <div class="form-group mb-2">
                                                <label for="alasan_{{ $approval->id }}" class="form-label">Alasan Penolakan</label>
                                                <textarea name="alasan" id="alasan_{{ $approval->id }}" class="form-control" rows="2" required></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-danger">
                                                <i class="bi bi-x-circle"></i> Tolak
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Modal Upload File TTD --}}
                        <div class="modal fade" id="uploadModal{{ $approval->id }}" tabindex="-1" aria-labelledby="uploadModalLabel{{ $approval->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('approvals.uploadSigned', $approval->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">📤 Upload File Sudah Ditandatangani</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="file_signed_{{ $approval->id }}" class="form-label">Pilih File PDF</label>
                                                <input type="file" name="file_signed" id="file_signed_{{ $approval->id }}" class="form-control" accept="application/pdf" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-success">Upload & Ganti</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="{{ $category === 'cuti' ? 10 : 9 }}" class="text-muted text-center">
                                Tidak ada pengajuan yang menunggu approval.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

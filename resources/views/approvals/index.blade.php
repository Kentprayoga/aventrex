@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4 fw-bold">📝 Halaman Approval Dokumen</h2>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Cuti Tahunan --}}
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">📅 Pengajuan Cuti Tahunan</h5>
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
                        <th>Sisa Cuti</th>
                        <th>Status</th>
                        <th>Aksi</th>
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
                            <td><span class="badge bg-secondary">{{ $approval->document->user->leaveBalance->remaining_leave ?? '0' }} hari</span></td>
                            <td>
                                <span class="badge bg-warning text-dark text-uppercase">{{ $approval->status }}</span>
                            </td>
                            <td>
                                <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalCuti{{ $approval->id }}">
                                    <i class="bi bi-eye"></i> Detail
                                </button>
                            </td>
                        </tr>

                        {{-- Modal Detail --}}
                        <div class="modal fade" id="modalCuti{{ $approval->id }}" tabindex="-1" aria-labelledby="modalLabelCuti{{ $approval->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header bg-light">
                                        <h5 class="modal-title">🗂 Detail Pengajuan Cuti</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                    </div>
                                    <div class="modal-body text-start">
                                        <p><strong>Nama Pengaju:</strong> {{ $approval->document->user->profile->name }}</p>
                                        <p><strong>NIP:</strong> {{ $approval->document->user->profile->nip }}</p>
                                        <p><strong>Nomor Dokumen:</strong> {{ $approval->document->document_number }}</p>
                                        <p><strong>Tanggal Pengajuan:</strong> {{ \Carbon\Carbon::parse($approval->document->tanggal_pengajuan)->format('d-m-Y H:i') }}</p>
                                        <p><strong>Sisa Cuti:</strong> {{ $approval->document->user->leaveBalance->remaining_leave ?? '0' }} hari</p>
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
                    @empty
                        <tr>
                            <td colspan="9" class="text-muted text-center">Tidak ada pengajuan cuti tahunan yang menunggu approval.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pengajuan Lainnya --}}
    <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">📁 Pengajuan Lainnya</h5>
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
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($otherApprovals as $index => $approval)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $approval->document->user->profile->nip }}</td>
                            <td>{{ $approval->document->template->name ?? '-' }}</td>
                            <td>{{ $approval->document->user->profile->name }}</td>
                            <td>{{ $approval->document->document_number }}</td>
                            <td>{{ \Carbon\Carbon::parse($approval->document->tanggal_pengajuan)->format('d-m-Y H:i') }}</td>
                            <td><span class="badge bg-warning text-dark text-uppercase">{{ $approval->status }}</span></td>
                            <td>
                                <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalOther{{ $approval->id }}">
                                    <i class="bi bi-eye"></i> Detail
                                </button>
                            </td>
                        </tr>

                        {{-- Modal Detail Pengajuan Lain --}}
                        <div class="modal fade" id="modalOther{{ $approval->id }}" tabindex="-1" aria-labelledby="modalLabelOther{{ $approval->id }}" aria-hidden="true">
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
                                        <form action="{{ route('approvals.approve', $approval->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-success">
                                                <i class="bi bi-check-circle"></i> Setujui
                                            </button>
                                        </form>
                                        <form action="{{ route('approvals.reject', $approval->id) }}" method="POST">
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
                    @empty
                        <tr>
                            <td colspan="8" class="text-muted text-center">Tidak ada pengajuan lainnya yang menunggu approval.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

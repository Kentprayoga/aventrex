@extends('layouts.app') 
@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h3>Daftar Template</h3>
            <div>
    <button
      type="button"
      class="flex items-center gap-2 border border-black rounded-xl px-5 py-2 font-semibold text-[18px] text-[#1B1330] hover:bg-gray-100"
      data-bs-toggle="modal" data-bs-target="#templateModal"
    >
      <i class="fas fa-plus text-[20px] font-bold"></i>
      Upload File
    </button>
                <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#categoryModal">Lihat Daftar Kategori</button>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#categoryAddModal">Tambah Kategori</button>
            </div>
        </div>

        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Nama Template</th>
                        <th>Kategori</th>
                        <th>Format Nomor</th>
                        <th>File</th>
                        <th>Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($templates as $template)
                        <tr>
                            <td>{{ $template->name }}</td>
                            <td>{{ $template->category->name ?? '-' }}</td>
                            <td>{{ $template->format_nomor }}</td>
                            <td>
                                <a href="{{ asset('storage/' . $template->file_path) }}" target="_blank">Lihat File</a>
                            </td>
                            <td>{{ $template->created_at->format('d-m-Y') }}</td>
                            <td>
                                <a href="{{ route('template.edit', $template->id) }}" class="btn btn-sm btn-warning">Edit</a>

                                <form action="{{ route('template.destroy', $template->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Hapus template ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>

                        </tr>

                        <!-- Modal Edit Template -->
                        <div class="modal fade" id="editTemplateModal{{ $template->id }}" tabindex="-1" aria-labelledby="editTemplateModalLabel{{ $template->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <form action="{{ route('template.update', $template->id) }}" method="POST" enctype="multipart/form-data" class="modal-content">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Template</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label>Nama Template</label>
                                            <input type="text" name="name" value="{{ $template->name }}" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label>File Template (kosongkan jika tidak diganti)</label>
                                            <input type="file" name="file_path" class="form-control" accept=".doc,.docx,.pdf">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-primary">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="6">Belum ada template.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Template -->
    <div class="modal fade" id="templateModal" tabindex="-1" aria-labelledby="templateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('template.store') }}" method="POST" enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Template</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Kategori</label>
                        <select name="categorie_id" class="form-control" required>
                            <option value="">Pilih Kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Nama Template</label>
                        <input type="text" name="name" class="form-control" placeholder="Nama Template" required>
                    </div>
                    <div class="mb-3">
                        <label>File Template</label>
                        <input type="file" name="file_path" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Tambah Kategori -->
    <div class="modal fade" id="categoryAddModal" tabindex="-1" aria-labelledby="categoryAddModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('categories.store') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nama Kategori</label>
                        <input type="text" name="name" class="form-control" placeholder="Nama Kategori" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Lihat/Edit Kategori -->
    <div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Daftar Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Jumlah Template</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $category)
                                <tr>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ $category->templates->count() }}</td>
                                    <td>
                                        <form action="{{ route('categories.update', $category->id) }}" method="POST" class="d-inline-block">
                                            @csrf
                                            @method('PUT')
                                            <input type="text" name="name" value="{{ $category->name }}" class="form-control d-inline-block w-auto" required>
                                            <button class="btn btn-sm btn-warning">Edit</button>
                                        </form>
                                        <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Yakin hapus kategori?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            @if ($categories->count() == 0)
                                <tr><td colspan="3">Belum ada kategori.</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

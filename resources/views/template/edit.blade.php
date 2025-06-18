@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow">
        <div class="card-header">
            <h4>Edit Template</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('template.update', $template->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label>Kategori</label>
                    <select name="categorie_id" class="form-control" required>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ $category->id == $template->categorie_id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label>Nama Template</label>
                    <input type="text" name="name" value="{{ $template->name }}" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Ganti File Template (Opsional)</label>
                    <input type="file" name="file_path" class="form-control">
                    <small>File saat ini: <a href="{{ asset('storage/' . $template->file_path) }}" target="_blank">Lihat File</a></small>
                </div>

                <button type="submit" class="btn btn-primary">Perbarui</button>
                <a href="{{ route('template.index') }}" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>
@endsection

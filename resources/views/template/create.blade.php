@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Upload Template</h2>

    {{-- Tampilkan error validasi --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Tampilkan pesan sukses --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('template.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="category_id">Kategori</label>
            <select name="category_id" class="form-control" required>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="name">Nama Template</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="format_nomor">Format Nomor</label>
            <input type="text" name="format_nomor" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="file">Upload File</label>
            <input type="file" name="file" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success">Simpan</button>
    </form>
</div>
@endsection

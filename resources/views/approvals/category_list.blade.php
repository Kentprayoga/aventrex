@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4 fw-bold">📂 Daftar Kategori Dokumen untuk Approval</h2>

    <div class="row">
        @foreach ($categories as $category)
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title">{{ ucfirst($category->name) }}</h5>
                        <p class="card-text">
                            <span class="badge bg-warning text-dark">
                                {{ $category->pending_count }}
                            </span> dokumen menunggu approval
                        </p>
                        <a href="{{ route('approvals.index', ['category' => $category->name]) }}" class="btn btn-primary">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

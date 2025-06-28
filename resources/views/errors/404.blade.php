<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>404 - Halaman Tidak Ditemukan</title>

    <!-- Fonts & CSS -->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,400,700" rel="stylesheet" />
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet" />
</head>

<body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;">
    <div class="text-center">

        {{-- LOGO --}}
        <div class="mb-4">
            <img src="{{ asset('logo.png') }}" alt="Logo Web" style="max-width: 150px;">
        </div>

        {{-- ERROR CODE --}}
        <div class="error mx-auto" data-text="404" style="font-size: 10rem;">404</div>
        <p class="lead text-gray-800 mb-3">Halaman Tidak Ditemukan</p>
        <p class="text-gray-500 mb-4">Sepertinya URL yang kamu tuju tidak tersedia.</p>
        <a href="{{ url('/') }}" class="btn btn-primary">
            <i class="fas fa-home mr-2"></i> Kembali ke Beranda
        </a>
    </div>

    <!-- Optional JS -->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Splash</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab&display=swap" rel="stylesheet"/>

    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.8s ease-out forwards;
        }

        .fade-in-delay {
            animation: fadeIn 0.8s ease-out forwards;
            animation-delay: 0.6s;
        }

        .fade-in-delay-2 {
            animation: fadeIn 0.8s ease-out forwards;
            animation-delay: 1.2s;
        }
    </style>
</head>
<body class="bg-blue-50 min-h-screen flex flex-col items-center justify-center px-4 py-6">

    <img src="{{ asset('logo2.png') }}" alt="Logo"
         class="w-72 object-contain mb-6 opacity-0 fade-in" />

    <p class="text-gray-700 text-sm mb-8 opacity-0 fade-in-delay">
        Selamat datang di aplikasi
    </p>

    <a href="{{ route('login') }}"
       class="opacity-0 fade-in-delay-2 bg-[#1B1240] text-white font-semibold rounded-full py-3 px-6 text-lg hover:bg-[#2f1d71] transition-all duration-300 shadow-md hover:shadow-lg">
        Masuk ke Login
    </a>

</body>
</html>

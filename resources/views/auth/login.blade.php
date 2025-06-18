<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Login Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab&display=swap" rel="stylesheet"/>

    <style>
        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.8s ease-out both;
        }

        .fade-out {
            animation: fadeOut 0.5s ease-out forwards;
        }

        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; visibility: hidden; }
        }
    </style>
</head>
<body class="bg-blue-50 flex flex-col items-center justify-center min-h-screen px-4 py-6 font-[Roboto_Slab]">

    <!-- Splash screen -->
    <div id="splash" class="fixed inset-0 bg-white z-50 flex items-center justify-center transition-opacity duration-500">
        <img src="{{ asset('logo2.png') }}" alt="Splash Logo" class="w-48 h-auto animate-pulse"/>
    </div>

    <!-- Logo -->
    <img id="main-logo" src="{{ asset('logo2.png') }}" alt="Logo"
         class="w-72 mb-4 object-contain block mx-auto opacity-0"/>

    <!-- Error Message -->
    @if(session('error'))
    <div class="text-red-600 mb-4 text-center fade-in-up">
        {{ session('error') }}
    </div>
    @endif

    <!-- Login Form -->
    <form method="POST" action="/login"
          id="login-form"
          class="bg-[#E9E9E4] border border-[#00000080] rounded-lg p-6 w-full max-w-sm shadow-md opacity-0">
        @csrf

        <input name="email" type="email" placeholder="Email" autocomplete="email"
               class="w-full bg-[#CFCFCF] border border-black rounded-xl py-3 px-4 mb-4 font-semibold text-sm placeholder-black placeholder-opacity-80 focus:outline-none focus:ring-2 focus:ring-[#1B1240] transition-all duration-200"
               value="{{ old('email') }}" required/>

        <input name="password" type="password" placeholder="Password" autocomplete="current-password"
               class="w-full bg-[#CFCFCF] border border-black rounded-xl py-3 px-4 mb-2 font-semibold text-sm placeholder-black placeholder-opacity-80 focus:outline-none focus:ring-2 focus:ring-[#1B1240] transition-all duration-200"
               required/>

        <button type="submit"
                class="w-full bg-gradient-to-b from-[#1B1240] to-[#1B1240] text-white font-semibold rounded-full py-3 text-lg hover:from-[#2f1d71] hover:to-[#2f1d71] transition-all duration-300 shadow-md hover:shadow-lg">
            Login
        </button>
    </form>

    <script>
        window.addEventListener('load', () => {
            const splash = document.getElementById('splash');
            const loginForm = document.getElementById('login-form');
            const logo = document.getElementById('main-logo');

            setTimeout(() => {
                splash.classList.add('fade-out');
                setTimeout(() => splash.remove(), 600);

                logo.classList.add('fade-in-up');
                logo.classList.remove('opacity-0');

                loginForm.classList.add('fade-in-up');
                loginForm.classList.remove('opacity-0');
            }, 2000); // splash timeout 2 detik
        });
    </script>
</body>
</html>

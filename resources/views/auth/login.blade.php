<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Superseed Academy</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            blue: '#0066FF',
                            navy: '#0A192F',
                            light: '#F0F5FF',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4 antialiased">

    <div class="max-w-md w-full bg-white rounded-xl shadow-lg overflow-hidden border border-slate-200">
        <div class="bg-brand-navy p-8 text-center">
            <h1 class="text-2xl font-bold tracking-tight text-white mb-1">SUPERSEED</h1>
            <p class="text-xs text-slate-400 font-semibold uppercase tracking-widest">Portal Login Admin & Wali</p>
        </div>

        <div class="p-8">
            <!-- Alert Error -->
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-600 p-4 mb-6 rounded-lg text-sm">
                    <p class="font-bold mb-1">Login Gagal</p>
                    <p>{{ $errors->first() }}</p>
                </div>
            @endif

            <form action="{{ route('login.process') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label for="username" class="block text-sm font-semibold text-slate-700 mb-1.5">Username</label>
                    <input type="text" name="username" id="username" value="{{ old('username') }}" required autofocus
                        class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-brand-light focus:border-brand-blue outline-none transition-colors text-sm"
                        placeholder="Masukkan username Anda">
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700 mb-1.5">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" required
                            class="w-full px-4 py-3 pr-10 border border-slate-200 rounded-lg focus:ring-2 focus:ring-brand-light focus:border-brand-blue outline-none transition-colors text-sm"
                            placeholder="••••••••">
                        
                        <!-- Tombol Eye Icon Toggle -->
                        <button type="button" id="togglePassword" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-brand-blue focus:outline-none transition-colors">
                            <!-- Ikon Mata Tertutup (Default) -->
                            <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                            <!-- Ikon Mata Terbuka (Disembunyikan awalnya) -->
                            <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 hidden">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center text-slate-600 font-medium cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded text-brand-blue focus:ring-brand-blue border-slate-300 mr-2 cursor-pointer">
                        Ingat Saya
                    </label>
                </div>

                <button type="submit"
                    class="w-full bg-brand-blue hover:bg-blue-700 text-white font-bold py-3.5 rounded-lg transition-colors text-sm tracking-wide mt-2">
                    MASUK KE PORTAL
                </button>
            </form>

            <div class="mt-8 text-center text-xs text-slate-500">
                <p>Belum memiliki akun? Silahkan hubungi admin sekretariat.</p>
                <a href="{{ route('landing.home') }}" class="text-brand-blue font-semibold hover:underline mt-2 inline-block transition-colors">&larr; Kembali ke Beranda</a>
            </div>
        </div>
    </div>

    <!-- Script Toggle Show/Hide Password -->
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const passwordInput = document.querySelector('#password');
        const eyeClosed = document.querySelector('#eyeClosed');
        const eyeOpen = document.querySelector('#eyeOpen');

        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            eyeClosed.classList.toggle('hidden');
            eyeOpen.classList.toggle('hidden');
        });
    </script>

</body>
</html>
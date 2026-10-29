<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50%      { transform: translateY(-14px) rotate(3deg); }
        }
        .fade-up { animation: fadeUp .5s ease-out both; }
        .delay-1 { animation-delay: .1s; }
        .delay-2 { animation-delay: .2s; }
        .delay-3 { animation-delay: .3s; }
        .blob { animation: float 7s ease-in-out infinite; }
    </style>
</head>
<body class="min-h-screen bg-gray-950 flex items-center justify-center p-4">
    <div class="w-full max-w-4xl bg-white rounded-2xl shadow-2xl overflow-hidden grid md:grid-cols-2">

        <!-- Panel kiri: branding -->
        <div class="relative hidden md:flex flex-col justify-between p-10 text-white overflow-hidden bg-gradient-to-br from-indigo-700 via-purple-700 to-fuchsia-800">
            <div class="absolute -top-16 -left-16 w-64 h-64 bg-white/10 rounded-full blur-2xl blob"></div>
            <div class="absolute bottom-10 -right-10 w-52 h-52 bg-fuchsia-400/20 rounded-full blur-2xl blob" style="animation-delay:-3s"></div>

            <div class="relative fade-up">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-white/15 backdrop-blur flex items-center justify-center shadow-lg">
                        <i class="fas fa-store text-xl"></i>
                    </div>
                    <h1 class="text-2xl font-extrabold tracking-tight">{{ config('app.name') }}</h1>
                </div>
                <p class="mt-4 text-indigo-100 leading-relaxed">
                    Aplikasi kasir modern untuk mengelola penjualan, stok produk, dan laporan toko Anda dalam satu tempat.
                </p>
            </div>

            <ul class="relative space-y-4 my-8">
                <li class="flex items-center gap-3 fade-up delay-1">
                    <span class="w-9 h-9 rounded-lg bg-white/15 flex items-center justify-center"><i class="fas fa-bolt text-sm"></i></span>
                    <span class="text-sm">Transaksi cepat &amp; akurat</span>
                </li>
                <li class="flex items-center gap-3 fade-up delay-2">
                    <span class="w-9 h-9 rounded-lg bg-white/15 flex items-center justify-center"><i class="fas fa-boxes-stacked text-sm"></i></span>
                    <span class="text-sm">Manajemen stok otomatis</span>
                </li>
                <li class="flex items-center gap-3 fade-up delay-3">
                    <span class="w-9 h-9 rounded-lg bg-white/15 flex items-center justify-center"><i class="fas fa-chart-line text-sm"></i></span>
                    <span class="text-sm">Laporan penjualan real-time</span>
                </li>
            </ul>

            <p class="relative text-xs text-indigo-200">&copy; {{ date('Y') }} {{ config('app.name') }}. Semua hak dilindungi.</p>
        </div>

        <!-- Panel kanan: form -->
        <div class="p-8 sm:p-10 flex flex-col justify-center bg-white">
            <div class="md:hidden mb-6 flex items-center justify-center gap-2 text-gray-900">
                <i class="fas fa-store text-indigo-600"></i>
                <span class="text-xl font-bold">{{ config('app.name') }}</span>
            </div>

            <h2 class="text-2xl font-bold text-gray-900">Selamat datang kembali! <span aria-hidden="true">👋</span></h2>
            <p class="mt-1 text-sm text-gray-500">Silakan login untuk melanjutkan.</p>

            @if($errors->any())
                <div class="mt-4 flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm fade-up">
                    <i class="fas fa-circle-exclamation mt-0.5"></i>
                    <div>
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400 pointer-events-none">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                            placeholder="nama@contoh.com"
                            class="w-full border border-gray-300 rounded-xl pl-11 pr-4 py-2.5 text-gray-900 placeholder-gray-400
                                   focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400 pointer-events-none">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input id="password" type="password" name="password" required
                            placeholder="••••••••"
                            class="w-full border border-gray-300 rounded-xl pl-11 pr-11 py-2.5 text-gray-900 placeholder-gray-400
                                   focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <button type="button" onclick="togglePassword()" aria-label="Tampilkan password"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-indigo-600 transition">
                            <i id="toggle-icon" class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-gray-600">Ingat saya</span>
                    </label>
                </div>

                <button type="submit"
                    class="group relative w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-indigo-600 to-purple-600
                           text-white py-3 rounded-xl font-semibold shadow-lg shadow-indigo-500/30 hover:shadow-indigo-500/50
                           hover:brightness-110 active:scale-[.98] transition">
                    <i class="fas fa-right-to-bracket"></i> Login
                </button>
            </form>

            <div class="mt-6 flex items-start gap-2.5 bg-indigo-50 border border-indigo-100 text-indigo-700 px-4 py-3 rounded-xl text-xs">
                <i class="fas fa-circle-info mt-0.5"></i>
                <p><strong>Akun demo:</strong> admin@kasir.com &nbsp;/&nbsp; password</p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('toggle-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</body>
</html>

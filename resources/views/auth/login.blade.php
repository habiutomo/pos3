<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        body { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50%      { transform: translateY(-18px) rotate(6deg); }
        }
        @keyframes drift {
            0%   { transform: translate(0,0); }
            50%  { transform: translate(40px,-20px); }
            100% { transform: translate(0,0); }
        }
        @keyframes shimmer {
            0%   { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        .fade-up { animation: fadeUp .6s ease-out both; }
        .delay-1 { animation-delay: .12s; }
        .delay-2 { animation-delay: .24s; }
        .delay-3 { animation-delay: .36s; }
        .delay-4 { animation-delay: .48s; }

        .blob { animation: float 8s ease-in-out infinite; }
        .orb { animation: drift 12s ease-in-out infinite; }

        .glass {
            background: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.12);
        }

        .btn-shimmer {
            background: linear-gradient(90deg, #10b981, #14b8a6, #0ea5e9, #10b981);
            background-size: 300% 100%;
            animation: shimmer 6s linear infinite;
        }

        .input-dark {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.14);
        }
        .input-dark:focus {
            outline: none;
            border-color: rgba(16, 185, 129, 0.7);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.18);
            background: rgba(255, 255, 255, 0.08);
        }
        .grid-bg {
            background-image:
                linear-gradient(rgba(255,255,255,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.04) 1px, transparent 1px);
            background-size: 42px 42px;
        }
    </style>
</head>
<body class="min-h-screen bg-[#05070d] text-white relative overflow-hidden flex items-center justify-center p-4">

    <!-- Grid + orbs dekoratif -->
    <div class="absolute inset-0 grid-bg"></div>
    <div class="absolute -top-32 -left-32 w-[28rem] h-[28rem] bg-emerald-500/20 rounded-full blur-3xl orb"></div>
    <div class="absolute -bottom-40 -right-24 w-[30rem] h-[30rem] bg-cyan-500/20 rounded-full blur-3xl orb" style="animation-delay:-4s"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[24rem] h-[24rem] bg-teal-400/10 rounded-full blur-3xl"></div>

    <div class="relative w-full max-w-5xl grid lg:grid-cols-2 bg-white/[0.03] backdrop-blur-xl border border-white/10 rounded-3xl shadow-2xl overflow-hidden">

        <!-- Panel kiri: branding -->
        <div class="relative hidden lg:flex flex-col justify-between p-12 overflow-hidden bg-gradient-to-br from-emerald-900/60 via-teal-900/50 to-slate-900/40">
            <div class="absolute -top-20 -right-20 w-72 h-72 bg-emerald-400/20 rounded-full blur-2xl blob"></div>
            <div class="absolute bottom-16 -left-16 w-60 h-60 bg-cyan-400/15 rounded-full blur-2xl blob" style="animation-delay:-3s"></div>

            <div class="relative fade-up">
                <div class="flex items-center gap-3">
                    <div class="w-13 h-13 p-3 rounded-2xl btn-shimmer flex items-center justify-center shadow-lg shadow-emerald-500/30">
                        <i class="fas fa-store text-xl"></i>
                    </div>
                    <h1 class="text-2xl font-extrabold tracking-tight">{{ config('app.name') }}</h1>
                </div>
                <p class="mt-5 text-emerald-100/90 leading-relaxed text-sm">
                    Solusi kasir pintar untuk toko Anda. Kelola penjualan, stok, dan laporan dengan cepat dalam satu tampilan elegan.
                </p>
            </div>

            <div class="relative space-y-4 my-10">
                <div class="flex items-center gap-3 fade-up delay-1">
                    <span class="w-10 h-10 rounded-xl bg-emerald-400/15 border border-emerald-300/20 flex items-center justify-center text-emerald-300"><i class="fas fa-bolt text-sm"></i></span>
                    <div>
                        <p class="text-sm font-semibold">Transaksi secepat kilat</p>
                        <p class="text-xs text-emerald-200/60">Checkout & cetak struk dalam sekejap</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 fade-up delay-2">
                    <span class="w-10 h-10 rounded-xl bg-cyan-400/15 border border-cyan-300/20 flex items-center justify-center text-cyan-300"><i class="fas fa-boxes-stacked text-sm"></i></span>
                    <div>
                        <p class="text-sm font-semibold">Stok selalu terkontrol</p>
                        <p class="text-xs text-emerald-200/60">Otomatis ter-update setiap transaksi</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 fade-up delay-3">
                    <span class="w-10 h-10 rounded-xl bg-teal-400/15 border border-teal-300/20 flex items-center justify-center text-teal-300"><i class="fas fa-chart-line text-sm"></i></span>
                    <div>
                        <p class="text-sm font-semibold">Laporan real-time</p>
                        <p class="text-xs text-emerald-200/60">Rekap penjualan & retur lengkap</p>
                    </div>
                </div>
            </div>

            <div class="relative flex items-center gap-2 text-xs text-emerald-200/50">
                <i class="fas fa-fingerprint text-emerald-300"></i>
                <span>Aman &amp; terenkripsi untuk keamanan data toko Anda</span>
            </div>
        </div>

        <!-- Panel kanan: form -->
        <div class="p-8 sm:p-12 flex flex-col justify-center">
            <div class="lg:hidden mb-8 flex flex-col items-center justify-center gap-2">
                <div class="w-14 h-14 p-3 rounded-2xl btn-shimmer flex items-center justify-center shadow-lg shadow-emerald-500/30">
                    <i class="fas fa-store text-xl"></i>
                </div>
                <span class="text-xl font-bold">{{ config('app.name') }}</span>
            </div>

            <div class="fade-up">
                <h2 class="text-2xl font-extrabold text-white">Masuk ke Akun</h2>
                <p class="mt-1 text-sm text-gray-400">Selamat datang! Silakan masuk untuk melanjutkan.</p>
            </div>

            @if($errors->any())
                <div class="mt-5 flex items-start gap-3 bg-red-500/10 border border-red-500/30 text-red-300 px-4 py-3 rounded-xl text-sm fade-up">
                    <i class="fas fa-circle-exclamation mt-0.5"></i>
                    <div>
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
                @csrf

                <div class="fade-up delay-1">
                    <label for="email" class="block text-sm font-medium text-gray-300 mb-2">Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-500 pointer-events-none">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                            placeholder="nama@contoh.com"
                            class="input-dark w-full rounded-xl pl-11 pr-4 py-3 text-white placeholder-gray-500 transition">
                    </div>
                </div>

                <div class="fade-up delay-2">
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-sm font-medium text-gray-300">Password</label>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-500 pointer-events-none">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input id="password" type="password" name="password" required
                            placeholder="••••••••"
                            class="input-dark w-full rounded-xl pl-11 pr-11 py-3 text-white placeholder-gray-500 transition">
                        <button type="button" onclick="togglePassword()" aria-label="Tampilkan password"
                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-500 hover:text-emerald-400 transition">
                            <i id="toggle-icon" class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between fade-up delay-3">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-gray-600 bg-gray-800 text-emerald-500 focus:ring-emerald-500">
                        <span class="text-sm text-gray-400">Ingat saya</span>
                    </label>
                    <span class="text-sm text-gray-500">Kamu punya pertanyaan? Tanya admin toko.</span>
                </div>

                <button type="submit"
                    class="fade-up delay-4 group relative w-full inline-flex items-center justify-center gap-2 btn-shimmer
                           text-white py-3 rounded-xl font-semibold shadow-lg shadow-emerald-500/30
                           hover:shadow-emerald-500/50 hover:brightness-110 active:scale-[.98] transition">
                    <i class="fas fa-right-to-bracket"></i> Masuk
                    <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </button>
            </form>
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

@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
@php
    $hour = now()->hour;
    if ($hour < 11) {
        $salam = 'Selamat pagi';
        $ikon_salam = 'fa-sun';
    } elseif ($hour < 15) {
        $salam = 'Selamat siang';
        $ikon_salam = 'fa-cloud-sun';
    } elseif ($hour < 18) {
        $salam = 'Selamat sore';
        $ikon_salam = 'fa-cloud-sunset';
    } else {
        $salam = 'Selamat malam';
        $ikon_salam = 'fa-moon';
    }
    $maxTerjual = $produk_laris->max('total_terjual') ?? 0;
@endphp

<style>
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(14px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .fade-up { animation: fadeUp .45s ease-out both; }
    .delay-1 { animation-delay: .08s; }
    .delay-2 { animation-delay: .16s; }
    .delay-3 { animation-delay: .24s; }
    .delay-4 { animation-delay: .32s; }
</style>

<div class="space-y-6">

    <!-- Hero -->
    <div class="relative overflow-hidden bg-gradient-to-r from-indigo-700 via-purple-700 to-fuchsia-700 rounded-2xl shadow-xl p-8 text-white fade-up">
        <div class="absolute -top-10 -right-10 w-52 h-52 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute -bottom-16 left-1/3 w-48 h-48 bg-fuchsia-400/20 rounded-full blur-2xl"></div>
        <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <p class="text-indigo-200 text-sm mb-1">
                    <i class="fas {{ $ikon_salam }} mr-1.5"></i>{{ now()->locale('id')->translatedFormat('l, d F Y') }}
                </p>
                <h2 class="text-3xl font-extrabold">{{ $salam }}, {{ auth()->user()->name }}! 👋</h2>
                <p class="mt-1 text-indigo-100">Berikut ringkasan performa toko Anda hari ini.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('kasir.index') }}"
                    class="inline-flex items-center gap-2 bg-white text-indigo-700 font-semibold px-5 py-2.5 rounded-xl shadow-lg hover:bg-indigo-50 hover:-translate-y-0.5 transition">
                    <i class="fas fa-cash-register"></i> Transaksi Baru
                </a>
            </div>
        </div>
    </div>

    <!-- Stat cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="fade-up delay-1 relative overflow-hidden rounded-2xl p-5 text-white shadow-lg bg-gradient-to-br from-emerald-500 to-teal-600 hover:-translate-y-1 hover:shadow-xl transition">
            <i class="fas fa-sack-dollar absolute -bottom-3 -right-2 text-7xl text-white/15"></i>
            <p class="text-emerald-100 text-sm">Total Pendapatan</p>
            <div class="flex items-center gap-2 mt-1 cursor-pointer select-none" onclick="togglePendapatan()">
                <p id="pendapatanValue" class="text-2xl font-bold" data-hidden="true">Rp ••••••••</p>
                <i id="pendapatanIcon" class="fas fa-eye-slash text-emerald-200 text-sm"></i>
            </div>
            <p class="text-emerald-100 text-xs mt-2"><i class="fas fa-arrow-trend-up mr-1"></i>{{ $transaksi_terbaru->count() }} transaksi terakhir</p>
        </div>
        <div class="fade-up delay-2 relative overflow-hidden rounded-2xl p-5 text-white shadow-lg bg-gradient-to-br from-blue-500 to-indigo-600 hover:-translate-y-1 hover:shadow-xl transition">
            <i class="fas fa-calendar-day absolute -bottom-3 -right-2 text-7xl text-white/15"></i>
            <p class="text-blue-100 text-sm">Transaksi Hari Ini</p>
            <p class="text-2xl font-bold mt-1">{{ $transaksi_hari_ini }}</p>
            <p class="text-blue-100 text-xs mt-2">Rp {{ number_format($pendapatan_hari_ini, 0, ',', '.') }} masuk hari ini</p>
        </div>
        <div class="fade-up delay-3 relative overflow-hidden rounded-2xl p-5 text-white shadow-lg bg-gradient-to-br from-violet-500 to-purple-600 hover:-translate-y-1 hover:shadow-xl transition">
            <i class="fas fa-boxes-stacked absolute -bottom-3 -right-2 text-7xl text-white/15"></i>
            <p class="text-violet-100 text-sm">Produk &amp; Kategori</p>
            <p class="text-2xl font-bold mt-1">{{ $total_produk }} <span class="text-base font-medium">produk</span></p>
            <p class="text-violet-100 text-xs mt-2">{{ $total_kategori }} kategori aktif</p>
        </div>
        @if(\App\Models\Pengaturan::stokAktif())
        <div class="fade-up delay-4 relative overflow-hidden rounded-2xl p-5 text-white shadow-lg {{ $stok_habis > 0 ? 'bg-gradient-to-br from-rose-500 to-red-600' : 'bg-gradient-to-br from-gray-500 to-gray-700' }} hover:-translate-y-1 hover:shadow-xl transition">
            <i class="fas fa-triangle-exclamation absolute -bottom-3 -right-2 text-7xl text-white/15"></i>
            <p class="{{ $stok_habis > 0 ? 'text-rose-100' : 'text-gray-300' }} text-sm">Stok Habis</p>
            <p class="text-2xl font-bold mt-1">{{ $stok_habis }} <span class="text-base font-medium">produk</span></p>
            @if($stok_habis > 0)
                <a href="{{ route('produk.index') }}" class="inline-block {{ $stok_habis > 0 ? 'text-rose-100' : 'text-gray-300' }} text-xs mt-2 underline underline-offset-2 hover:text-white">Segera restock →</a>
            @else
                <p class="text-gray-300 text-xs mt-2">Semua stok aman 👍</p>
            @endif
        </div>
        @endif
    </div>

    <!-- Charts & lists -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <div class="bg-white rounded-2xl shadow-lg p-6 fade-up delay-1">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-bold"><i class="fas fa-fire mr-2 text-orange-500"></i>Produk Terlaris</h3>
                <span class="text-xs bg-orange-100 text-orange-700 px-2.5 py-1 rounded-full font-medium">Top 5</span>
            </div>
            @if($produk_laris->count())
                <ul class="space-y-4">
                    @foreach($produk_laris as $index => $item)
                        <li>
                            <div class="flex items-center justify-between mb-1.5">
                                <div class="flex items-center gap-3">
                                    <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold {{ $loop->first ? 'bg-amber-400 text-amber-900' : ($loop->iteration === 2 ? 'bg-gray-300 text-gray-700' : ($loop->iteration === 3 ? 'bg-amber-700/20 text-amber-800' : 'bg-indigo-50 text-indigo-600')) }}">
                                        {{ $index + 1 }}
                                    </span>
                                    <span class="font-medium text-gray-800">{{ $item->nama_produk }}</span>
                                </div>
                                <span class="text-sm font-semibold text-gray-600">{{ number_format($item->total_terjual, 0, ',', '.') }} terjual</span>
                            </div>
                            <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full bg-gradient-to-r from-indigo-500 to-fuchsia-500" style="width: {{ $maxTerjual > 0 ? round($item->total_terjual / $maxTerjual * 100) : 0 }}%"></div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="text-center py-10 text-gray-400">
                    <i class="fas fa-chart-simple text-4xl mb-3"></i>
                    <p>Belum ada data penjualan</p>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6 fade-up delay-2">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-bold"><i class="fas fa-clock-rotate-left mr-2 text-indigo-500"></i>Transaksi Terbaru</h3>
                <a href="{{ route('transaksi.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Lihat semua →</a>
            </div>
            @if($transaksi_terbaru->count())
                <ul class="divide-y divide-gray-100">
                    @foreach($transaksi_terbaru as $trx)
                        <li class="flex items-center gap-3 py-3">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                                <i class="fas fa-receipt"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-800 truncate">{{ $trx->no_transaksi }}</p>
                                <p class="text-xs text-gray-500">{{ $trx->created_at->format('d/m/Y H:i') }} • oleh {{ $trx->user->name ?? '-' }}</p>
                            </div>
                            <span class="font-semibold text-emerald-600 whitespace-nowrap">Rp {{ number_format($trx->total_harga, 0, ',', '.') }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="text-center py-10 text-gray-400">
                    <i class="fas fa-receipt text-4xl mb-3"></i>
                    <p>Belum ada transaksi</p>
                    <a href="{{ route('kasir.index') }}" class="inline-block mt-3 text-indigo-600 hover:text-indigo-800 text-sm font-medium">Mulai transaksi pertama →</a>
                </div>
            @endif
        </div>
    </div>

    <!-- Quick actions -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 fade-up delay-2">
        <a href="{{ route('kasir.index') }}" class="group bg-white rounded-2xl shadow p-5 flex items-center gap-4 hover:-translate-y-1 hover:shadow-xl transition">
            <span class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg group-hover:scale-110 transition"><i class="fas fa-cash-register"></i></span>
            <span><span class="block font-semibold text-gray-800">Kasir</span><span class="text-xs text-gray-500">Buat transaksi baru</span></span>
        </a>
        <a href="{{ route('produk.index') }}" class="group bg-white rounded-2xl shadow p-5 flex items-center gap-4 hover:-translate-y-1 hover:shadow-xl transition">
            <span class="w-12 h-12 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center text-lg group-hover:scale-110 transition"><i class="fas fa-box"></i></span>
            <span><span class="block font-semibold text-gray-800">Produk</span><span class="text-xs text-gray-500">Kelola data produk</span></span>
        </a>
        <a href="{{ route('kategori.index') }}" class="group bg-white rounded-2xl shadow p-5 flex items-center gap-4 hover:-translate-y-1 hover:shadow-xl transition">
            <span class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg group-hover:scale-110 transition"><i class="fas fa-tags"></i></span>
            <span><span class="block font-semibold text-gray-800">Kategori</span><span class="text-xs text-gray-500">Kelola kategori produk</span></span>
        </a>
        <a href="{{ route('user.index') }}" class="group bg-white rounded-2xl shadow p-5 flex items-center gap-4 hover:-translate-y-1 hover:shadow-xl transition">
            <span class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg group-hover:scale-110 transition"><i class="fas fa-users-gear"></i></span>
            <span><span class="block font-semibold text-gray-800">User</span><span class="text-xs text-gray-500">Kelola pengguna</span></span>
        </a>
    </div>
</div>

<script>
document.getElementById('pendapatanValue').textContent = 'Rp ••••••••';
document.getElementById('pendapatanIcon').className = 'fas fa-eye-slash text-emerald-200 text-sm';

function togglePendapatan() {
    const val = document.getElementById('pendapatanValue');
    const icon = document.getElementById('pendapatanIcon');
    const hidden = val.getAttribute('data-hidden') === 'true';
    if (hidden) {
        val.textContent = 'Rp {{ number_format($total_pendapatan, 0, ',', '.') }}';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
        val.setAttribute('data-hidden', 'false');
    } else {
        val.textContent = 'Rp ••••••••';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
        val.setAttribute('data-hidden', 'true');
    }
}
</script>
@endsection

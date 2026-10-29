@extends('layouts.app')
@section('title', 'Rekap Penjualan')

@section('content')
<div class="flex justify-between items-center mb-6 flex-wrap gap-3">
    <h2 class="text-2xl font-bold">Rekap Penjualan</h2>
    <div class="flex gap-2">
        <a href="{{ route('rekap.print', request()->only(['start_date', 'end_date'])) }}" target="_blank"
            class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
            <i class="fas fa-print mr-2"></i>Cetak
        </a>
        <a href="{{ route('rekap.export', request()->only(['start_date', 'end_date'])) }}"
            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
            <i class="fas fa-file-csv mr-2"></i>Export CSV
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow p-4 mb-6">
    <form method="GET" action="{{ route('rekap.index') }}" class="flex gap-4 items-end flex-wrap">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
            <input type="date" name="start_date" value="{{ $startDate }}" max="{{ $endDate }}"
                class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
            <input type="date" name="end_date" value="{{ $endDate }}" min="{{ $startDate }}"
                class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700">
            <i class="fas fa-filter mr-2"></i>Tampilkan
        </button>
    </form>
    <p class="text-xs text-gray-500 mt-2"><i class="fas fa-calendar mr-1"></i>Periode: {{ \Carbon\Carbon::parse($startDate)->locale('id')->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->locale('id')->translatedFormat('d F Y') }}</p>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-5">
        <p class="text-xs text-gray-500 uppercase mb-1">Total Omzet</p>
        <p class="text-2xl font-bold text-emerald-600">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-5">
        <p class="text-xs text-gray-500 uppercase mb-1">Laba Kotor</p>
        <p class="text-2xl font-bold text-blue-600">Rp {{ number_format($labaKotor, 0, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-5">
        <p class="text-xs text-gray-500 uppercase mb-1">Jumlah Transaksi</p>
        <p class="text-2xl font-bold">{{ number_format($jumlahTransaksi, 0, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-5">
        <p class="text-xs text-gray-500 uppercase mb-1">Rata-rata / Hari</p>
        <p class="text-2xl font-bold text-purple-600">Rp {{ number_format($rataHarian, 0, ',', '.') }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <h3 class="font-bold px-4 py-3 border-b bg-gray-50"><i class="fas fa-calendar-day mr-2 text-indigo-500"></i>Rekap Per Hari</h3>
        <table class="w-full min-w-[400px]">
            <thead>
                <tr class="text-xs text-gray-500 uppercase border-b">
                    <th class="px-4 py-2 text-left">Tanggal</th>
                    <th class="px-4 py-2 text-right">Transaksi</th>
                    <th class="px-4 py-2 text-right">Omzet</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($perHari as $hari)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2.5">{{ \Carbon\Carbon::parse($hari->tanggal)->locale('id')->translatedFormat('d M Y') }}</td>
                        <td class="px-4 py-2.5 text-right">{{ $hari->jumlah_transaksi }}</td>
                        <td class="px-4 py-2.5 text-right font-medium">Rp {{ number_format($hari->omzet, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-4 py-8 text-center text-gray-500">Tidak ada transaksi pada periode ini</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <h3 class="font-bold px-4 py-3 border-b bg-gray-50"><i class="fas fa-user-tie mr-2 text-emerald-500"></i>Rekap Per Kasir</h3>
        <table class="w-full min-w-[400px]">
            <thead>
                <tr class="text-xs text-gray-500 uppercase border-b">
                    <th class="px-4 py-2 text-left">Kasir</th>
                    <th class="px-4 py-2 text-right">Transaksi</th>
                    <th class="px-4 py-2 text-right">Omzet</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($perKasir as $kasir)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2.5 font-medium">{{ $kasir->name }}</td>
                        <td class="px-4 py-2.5 text-right">{{ $kasir->jumlah_transaksi }}</td>
                        <td class="px-4 py-2.5 text-right font-medium">Rp {{ number_format($kasir->omzet, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-4 py-8 text-center text-gray-500">Tidak ada transaksi pada periode ini</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-x-auto">
    <h3 class="font-bold px-4 py-3 border-b bg-gray-50"><i class="fas fa-fire mr-2 text-orange-500"></i>10 Produk Terlaris Periode Ini</h3>
    <table class="w-full min-w-[640px]">
        <thead>
            <tr class="text-xs text-gray-500 uppercase border-b">
                <th class="px-4 py-2 text-left">No</th>
                <th class="px-4 py-2 text-left">Kode</th>
                <th class="px-4 py-2 text-left">Nama Produk</th>
                <th class="px-4 py-2 text-right">Terjual</th>
                <th class="px-4 py-2 text-right">Omzet</th>
                <th class="px-4 py-2 text-right">Laba</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($produkTerjual as $produk)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2.5">{{ $loop->iteration }}</td>
                    <td class="px-4 py-2.5 font-mono text-sm">{{ $produk->kode_produk }}</td>
                    <td class="px-4 py-2.5 font-medium">{{ $produk->nama_produk }}</td>
                    <td class="px-4 py-2.5 text-right">{{ $produk->total_terjual }}</td>
                    <td class="px-4 py-2.5 text-right">Rp {{ number_format($produk->omzet, 0, ',', '.') }}</td>
                    <td class="px-4 py-2.5 text-right text-emerald-600 font-medium">Rp {{ number_format($produk->laba, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">Tidak ada penjualan pada periode ini</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

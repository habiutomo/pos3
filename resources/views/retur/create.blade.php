@extends('layouts.app')
@section('title', 'Retur Penjualan')

@section('content')
<div class="flex flex-wrap justify-between items-center gap-3 mb-6">
    <h2 class="text-xl lg:text-2xl font-bold">Retur Penjualan</h2>
    <a href="{{ route('retur.index') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">
        <i class="fas fa-history mr-2"></i>Riwayat Retur
    </a>
</div>

<div class="bg-white rounded-lg shadow p-4 mb-4">
    <form method="GET" action="{{ route('retur.create') }}" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1">
            <label class="block text-sm font-medium text-gray-600 mb-1">Cari No. Transaksi</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik no. transaksi lalu tekan Cari..."
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700">
            <i class="fas fa-search mr-2"></i>Cari
        </button>
    </form>
</div>

<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="w-full min-w-[760px]">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Transaksi</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kasir</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($transaksis as $trx)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-mono text-sm">{{ $trx->no_transaksi }}</td>
                    <td class="px-6 py-4 text-sm">{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-6 py-4">{{ $trx->user->name }}</td>
                    <td class="px-6 py-4 text-right font-medium">Rp {{ number_format($trx->total_harga, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('retur.pilih', $trx->id) }}" class="bg-blue-600 text-white px-3 py-1.5 rounded-lg hover:bg-blue-700 text-sm">
                            <i class="fas fa-rotate-left mr-1"></i>Retur
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                        @if(request('search'))
                            Tidak ada transaksi yang cocok dengan "{{ request('search') }}"
                        @else
                            Cari no. transaksi di atas untuk memulai retur
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

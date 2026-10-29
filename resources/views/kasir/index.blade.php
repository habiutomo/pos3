@extends('layouts.app')
@section('title', 'Riwayat Transaksi')

@section('content')
<div class="flex flex-wrap justify-between items-center gap-3 mb-6">
    <h2 class="text-xl lg:text-2xl font-bold">Riwayat Transaksi</h2>
    <a href="{{ route('kasir.index') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">
        <i class="fas fa-cash-register mr-2"></i>Buka Kasir
    </a>
</div>

<div class="bg-white rounded-lg shadow p-4 mb-4">
    <form method="GET" action="{{ route('transaksi.index') }}" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari no. transaksi..."
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <input type="date" name="date" value="{{ request('date') }}"
                class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
            <i class="fas fa-search"></i>
        </button>
    </form>
</div>

<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="w-full min-w-[760px]">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Transaksi</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kasir</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Bayar</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Kembalian</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($transaksis as $trx)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">{{ ($transaksis->currentPage() - 1) * $transaksis->perPage() + $loop->iteration }}</td>
                    <td class="px-6 py-4 font-mono text-sm">{{ $trx->no_transaksi }}</td>
                    <td class="px-6 py-4 text-sm">{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-6 py-4">{{ $trx->user->name }}</td>
                    <td class="px-6 py-4 text-right font-medium">Rp {{ number_format($trx->total_harga, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-right">Rp {{ number_format($trx->bayar, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-right">Rp {{ number_format($trx->kembalian, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('kasir.struk', $trx->id) }}" class="text-blue-600 hover:text-blue-800 mr-2" title="Lihat Struk">
                            <i class="fas fa-receipt"></i>
                        </a>
                        <a href="{{ route('retur.pilih', $trx->id) }}" class="text-orange-600 hover:text-orange-800 mr-2" title="Retur">
                            <i class="fas fa-rotate-left"></i>
                        </a>
                        <form action="{{ route('transaksi.destroy', $trx) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus transaksi ini?{{ \App\Models\Pengaturan::stokAktif() ? ' Stok akan dikembalikan.' : '' }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-6 py-8 text-center text-gray-500">Belum ada transaksi</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $transaksis->withQueryString()->links() }}
</div>
@endsection

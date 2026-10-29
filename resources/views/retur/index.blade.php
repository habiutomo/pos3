@extends('layouts.app')
@section('title', 'Riwayat Retur')

@section('content')
<div class="flex flex-wrap justify-between items-center gap-3 mb-6">
    <h2 class="text-xl lg:text-2xl font-bold">Riwayat Retur</h2>
    <a href="{{ route('retur.create') }}" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
        <i class="fas fa-plus mr-2"></i>Buat Retur
    </a>
</div>

<div class="bg-white rounded-lg shadow p-4 mb-4">
    <form method="GET" action="{{ route('retur.index') }}" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari no. retur..."
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
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Retur</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Transaksi</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kasir</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Retur</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($returs as $retur)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">{{ ($returs->currentPage() - 1) * $returs->perPage() + $loop->iteration }}</td>
                    <td class="px-6 py-4 font-mono text-sm text-red-600">{{ $retur->no_retur }}</td>
                    <td class="px-6 py-4 font-mono text-sm">{{ $retur->transaksi->no_transaksi }}</td>
                    <td class="px-6 py-4 text-sm">{{ $retur->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-6 py-4">{{ $retur->user->name }}</td>
                    <td class="px-6 py-4 text-right font-medium text-red-600">Rp {{ number_format($retur->total_pengembalian, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('retur.show', $retur->id) }}" class="text-blue-600 hover:text-blue-800" title="Lihat Detail">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">Belum ada retur</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $returs->withQueryString()->links() }}
</div>
@endsection

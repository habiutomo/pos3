@extends('layouts.app')
@section('title', 'Stock Opname')

@section('content')
<div class="flex flex-wrap justify-between items-center gap-3 mb-6">
    <h2 class="text-xl lg:text-2xl font-bold">Stock Opname</h2>
    <a href="{{ route('stock-opname.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
        <i class="fas fa-clipboard-check mr-2"></i>Buat Stock Opname
    </a>
</div>

<div class="bg-white rounded-lg shadow p-4 mb-4">
    <form method="GET" action="{{ route('stock-opname.index') }}" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari no. opname..."
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
    <table class="w-full min-w-[640px]">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Opname</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Petugas</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Jumlah Item</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($stockOpnames as $opname)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">{{ ($stockOpnames->currentPage() - 1) * $stockOpnames->perPage() + $loop->iteration }}</td>
                    <td class="px-6 py-4 font-mono text-sm">{{ $opname->no_opname }}</td>
                    <td class="px-6 py-4 text-sm">{{ $opname->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-6 py-4">{{ $opname->user->name }}</td>
                    <td class="px-6 py-4 text-right">{{ $opname->details_count }}</td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('stock-opname.show', $opname) }}" class="text-blue-600 hover:text-blue-800" title="Lihat Detail">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">Belum ada stock opname</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $stockOpnames->withQueryString()->links() }}
</div>
@endsection

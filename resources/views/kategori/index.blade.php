@extends('layouts.app')
@section('title', 'Kategori')

@section('content')
<div class="flex flex-wrap justify-between items-center gap-3 mb-6">
    <h2 class="text-xl lg:text-2xl font-bold">Kategori Produk</h2>
    <a href="{{ route('kategori.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
        <i class="fas fa-plus mr-2"></i>Tambah Kategori
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="w-full min-w-[600px]">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Kategori</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah Produk</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($kategoris as $kategori)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">{{ ($kategoris->currentPage() - 1) * $kategoris->perPage() + $loop->iteration }}</td>
                    <td class="px-6 py-4 font-medium">{{ $kategori->nama_kategori }}</td>
                    <td class="px-6 py-4 text-gray-500">{{ $kategori->deskripsi ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">{{ $kategori->produks_count }} produk</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('kategori.edit', $kategori) }}" class="text-yellow-600 hover:text-yellow-800 mr-3">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('kategori.destroy', $kategori) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus kategori ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada data kategori</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $kategoris->links() }}
</div>
@endsection

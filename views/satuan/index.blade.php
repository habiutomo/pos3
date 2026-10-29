@extends('layouts.app')
@section('title', 'Satuan')

@section('content')
<div class="flex flex-wrap justify-between items-center gap-3 mb-6">
    <h2 class="text-xl lg:text-2xl font-bold">Satuan Produk</h2>
    <a href="{{ route('satuan.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
        <i class="fas fa-plus mr-2"></i>Tambah Satuan
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="w-full min-w-[500px]">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Satuan</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah Produk</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($satuans as $satuan)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">{{ ($satuans->currentPage() - 1) * $satuans->perPage() + $loop->iteration }}</td>
                    <td class="px-6 py-4 font-medium uppercase">{{ $satuan->nama_satuan }}</td>
                    <td class="px-6 py-4">
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">{{ $satuan->produks_count }} produk</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('satuan.edit', $satuan) }}" class="text-yellow-600 hover:text-yellow-800 mr-3">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('satuan.destroy', $satuan) }}" method="POST" class="inline"
                            onsubmit="return confirm('Yakin hapus satuan ini?')">
                            @csrf
                            @method('DELETE')
                            @if($satuan->produks_count > 0)
                                <button type="submit" disabled title="Masih digunakan oleh produk"
                                    class="text-gray-300 cursor-not-allowed">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @else
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @endif
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">Belum ada data satuan</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $satuans->links() }}
</div>
@endsection

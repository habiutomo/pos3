@extends('layouts.app')
@section('title', 'Produk')

@section('content')
<div class="flex flex-wrap justify-between items-center gap-3 mb-6">
    <h2 class="text-xl lg:text-2xl font-bold">Data Produk</h2>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('produk.import.form') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
            <i class="fas fa-file-excel mr-2"></i>Import Excel
        </a>
        <a href="{{ route('produk.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i>Tambah Produk
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow p-4 mb-4">
    <form method="GET" action="{{ route('produk.index') }}" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk..."
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <select name="kategori_id" class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Kategori</option>
                @foreach($kategoris as $kategori)
                    <option value="{{ $kategori->id }}" {{ request('kategori_id') == $kategori->id ? 'selected' : '' }}>
                        {{ $kategori->nama_kategori }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
            <i class="fas fa-search"></i>
        </button>
    </form>
</div>

@if($produks->isEmpty())
    <div class="bg-white rounded-lg shadow p-8 text-center text-gray-500">Belum ada data produk</div>
@else
    <!-- Mobile / tablet card layout -->
    <div class="space-y-4 md:hidden">
        @foreach($produks as $produk)
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-start justify-between gap-2 mb-2">
                    <div>
                        <p class="font-medium">{{ $produk->nama_produk }}</p>
                        <p class="text-xs text-gray-500 font-mono">{{ $produk->kode_produk }}</p>
                    </div>
                    <span class="text-xs bg-gray-100 text-gray-700 px-2 py-0.5 rounded whitespace-nowrap">{{ $produk->kategori->nama_kategori }}</span>
                </div>

                @php $tiersLain = $produk->produkSatuans->reject(fn ($t) => (int) $t->satuan_id === (int) $produk->satuan_id); @endphp
                @if($tiersLain->isNotEmpty())
                    <div class="flex flex-wrap gap-1 mb-2">
                        @foreach($tiersLain as $t)
                            <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2 py-0.5 rounded">
                                {{ strtoupper($t->satuan->nama_satuan) }} = {{ $t->konversi }} | Rp {{ number_format($t->harga_jual, 0, ',', '.') }}
                            </span>
                        @endforeach
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm border-t border-gray-100 pt-3">
                    @if(\App\Models\Pengaturan::stokAktif())
                    <div>
                        <p class="text-xs text-gray-400">Stok</p>
                        @if($produk->stok == 0)
                            <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-0.5 rounded">Habis</span>
                        @elseif($produk->stok <= 5)
                            <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-0.5 rounded">{{ $produk->stok }} {{ $produk->satuan->nama_satuan }}</span>
                        @else
                            <span class="text-green-600 font-medium">{{ $produk->stok }} {{ $produk->satuan->nama_satuan }}</span>
                        @endif
                    </div>
                    @endif
                    <div>
                        <p class="text-xs text-gray-400">Harga Beli</p>
                        <p>Rp {{ number_format($produk->harga_beli, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Harga Jual</p>
                        <p class="font-medium">Rp {{ number_format($produk->harga_jual, 0, ',', '.') }} <span class="text-xs text-gray-400">/ {{ $produk->satuan->nama_satuan }}</span></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Satuan Jual</p>
                        <p>{{ $produk->satuan->nama_satuan }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 mt-3 pt-3 border-t border-gray-100">
                    <a href="{{ route('produk.edit', $produk) }}" class="text-yellow-600 hover:text-yellow-800">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('produk.destroy', $produk) }}" method="POST" onsubmit="return confirm('Yakin hapus produk ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Desktop table layout -->
    <div class="hidden md:block bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full min-w-[900px]">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Produk</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                    @if(\App\Models\Pengaturan::stokAktif())
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Stok</th>
                    @endif
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Harga Beli</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Harga Jual</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Satuan Jual</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($produks as $produk)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ ($produks->currentPage() - 1) * $produks->perPage() + $loop->iteration }}</td>
                        <td class="px-4 py-3 font-mono text-sm">{{ $produk->kode_produk }}</td>
                        <td class="px-4 py-3 font-medium">{{ $produk->nama_produk }}</td>
                        <td class="px-4 py-3">{{ $produk->kategori->nama_kategori }}</td>
                        @if(\App\Models\Pengaturan::stokAktif())
                        <td class="px-4 py-3 text-right">
                            @if($produk->stok == 0)
                                <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-0.5 rounded">Habis</span>
                            @elseif($produk->stok <= 5)
                                <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-0.5 rounded">{{ $produk->stok }} {{ $produk->satuan->nama_satuan }}</span>
                            @else
                                <span class="text-green-600 font-medium">{{ $produk->stok }} {{ $produk->satuan->nama_satuan }}</span>
                            @endif
                        </td>
                        @endif
                        <td class="px-4 py-3 text-right">Rp {{ number_format($produk->harga_beli, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-medium">Rp {{ number_format($produk->harga_jual, 0, ',', '.') }} <span class="text-xs text-gray-400">/ {{ $produk->satuan->nama_satuan }}</span></td>
                        <td class="px-4 py-3 text-center">
                            @php $tiersLain = $produk->produkSatuans->reject(fn ($t) => (int) $t->satuan_id === (int) $produk->satuan_id); @endphp
                            @if($tiersLain->isNotEmpty())
                                @foreach($tiersLain as $t)
                                    <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2 py-0.5 rounded mr-1 mb-1 inline-block">
                                        {{ strtoupper($t->satuan->nama_satuan) }} = {{ $t->konversi }} | Rp {{ number_format($t->harga_jual, 0, ',', '.') }}
                                    </span>
                                @endforeach
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('produk.edit', $produk) }}" class="text-yellow-600 hover:text-yellow-800 mr-2">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('produk.destroy', $produk) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus produk ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

<div class="mt-4">
    {{ $produks->withQueryString()->links() }}
</div>
@endsection

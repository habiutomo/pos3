@extends('layouts.app')
@section('title', 'Tambah Produk')

@section('content')
<div class="flex flex-wrap justify-between items-center gap-3 mb-6">
    <h2 class="text-xl lg:text-2xl font-bold">Tambah Produk</h2>
    <a href="{{ route('produk.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
        <i class="fas fa-arrow-left mr-2"></i>Kembali
    </a>
</div>

<div class="bg-white rounded-lg shadow p-6 max-w-lg">
    <form method="POST" action="{{ route('produk.store') }}">
        @csrf
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Kategori *</label>
            <select name="kategori_id" required
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Pilih Kategori</option>
                @foreach($kategoris as $kategori)
                    <option value="{{ $kategori->id }}" {{ old('kategori_id') == $kategori->id ? 'selected' : '' }}>
                        {{ $kategori->nama_kategori }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Kode Produk *</label>
            <input type="text" name="kode_produk" value="{{ old('kode_produk') }}" required
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Barcode</label>
            <input type="text" name="barcode" value="{{ old('barcode') }}" placeholder="Scan atau ketik barcode..."
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk *</label>
            <input type="text" name="nama_produk" value="{{ old('nama_produk') }}" required
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="grid {{ \App\Models\Pengaturan::stokAktif() ? 'grid-cols-1 sm:grid-cols-2' : 'grid-cols-1' }} gap-3 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Beli (per satuan dasar) *</label>
                <input type="text" inputmode="numeric" name="harga_beli" value="{{ number_format((int) round((float) old('harga_beli', 0)), 0, ',', '.') }}" required
                    class="js-rupiah w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            @if(\App\Models\Pengaturan::stokAktif())
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Stok (satuan dasar) *</label>
                <input type="number" name="stok" value="{{ old('stok', 0) }}" required min="0"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            @endif
        </div>

        <div class="mb-6 border border-gray-200 rounded-lg p-4 bg-gray-50">
            <div class="flex justify-between items-center mb-2">
                <label class="block text-sm font-medium text-gray-700">Satuan & Harga Jual *</label>
                <button type="button" onclick="tambahTier()"
                    class="bg-blue-600 text-white text-xs px-3 py-1.5 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-plus mr-1"></i>Tambah Satuan
                </button>
            </div>
            <p class="text-xs text-gray-500 mb-3">Baris pertama = satuan dasar (stok mengikuti satuan ini). Contoh turunan: 1 dus = 20 tim, isi harga jual per dus.</p>
            <div id="tierContainer" class="space-y-2">
                @if(old('satuan_tiers'))
                    @foreach(old('satuan_tiers') as $i => $tier)
                        @include('produk._tier-row', ['i' => $i, 'tier' => ['satuan_id' => $tier['satuan_id'] ?? '', 'konversi' => $tier['konversi'] ?? '', 'harga_jual' => $tier['harga_jual'] ?? ''], 'isBase' => $i == 0])
                    @endforeach
                @else
                    @include('produk._tier-row', ['i' => 0, 'tier' => ['satuan_id' => '', 'konversi' => 1, 'harga_jual' => ''], 'isBase' => true])
                @endif
            </div>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
            <i class="fas fa-save mr-2"></i>Simpan
        </button>
    </form>
</div>

@push('scripts')
<script>
    let tierIndex = document.querySelectorAll('.tier-row').length;

    function tambahTier() {
        const container = document.getElementById('tierContainer');
        const row = document.createElement('div');
        row.className = 'tier-row grid grid-cols-12 gap-2 items-center';
        row.innerHTML = `
            <select name="satuan_tiers[${tierIndex}][satuan_id]" required class="col-span-12 md:col-span-4 tier-satuan border border-gray-300 rounded-lg px-2 py-2 text-sm">
                <option value="">Satuan</option>
                @foreach($satuans as $satuan)
                    <option value="{{ $satuan->id }}">{{ strtoupper($satuan->nama_satuan) }}</option>
                @endforeach
            </select>
            <div class="col-span-5 md:col-span-3 flex items-center gap-1">
                <span class="text-xs text-gray-500 whitespace-nowrap">=</span>
                <input type="number" name="satuan_tiers[${tierIndex}][konversi]" value="" required min="1" placeholder="1"
                    class="w-full border border-gray-300 rounded-lg px-2 py-2 text-sm" title="Jumlah satuan dasar">
            </div>
            <div class="col-span-12 md:col-span-4 relative">
                <span class="absolute left-2 top-1/2 -translate-y-1/2 text-xs text-gray-400">Rp</span>
                <input type="text" inputmode="numeric" name="satuan_tiers[${tierIndex}][harga_jual]" value="" required placeholder="Harga jual"
                    class="js-rupiah w-full border border-gray-300 rounded-lg pl-7 pr-2 py-2 text-sm">
            </div>
            <button type="button" onclick="hapusTier(this)" class="col-span-1 md:col-span-1 text-red-500 hover:text-red-700"><i class="fas fa-times"></i></button>
        `;
        container.appendChild(row);
        tierIndex++;
    }

    function hapusTier(btn) {
        if (btn.closest('.tier-row').dataset.base === '1') return;
        btn.closest('.tier-row').remove();
    }
</script>
@endpush
@endsection

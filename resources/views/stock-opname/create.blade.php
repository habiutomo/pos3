@extends('layouts.app')
@section('title', 'Buat Stock Opname')

@section('content')
<div class="flex flex-wrap justify-between items-center gap-3 mb-6">
    <h2 class="text-xl lg:text-2xl font-bold">Buat Stock Opname</h2>
    <a href="{{ route('stock-opname.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
        <i class="fas fa-arrow-left mr-2"></i>Kembali
    </a>
</div>

<form method="POST" action="{{ route('stock-opname.store') }}" id="opname-form">
    @csrf
    <div class="bg-white rounded-lg shadow p-4 mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (opsional)</label>
        <textarea name="catatan" rows="2" placeholder="Contoh: Opname bulanan gudang utama"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('catatan') }}</textarea>
    </div>

    <div class="bg-white rounded-lg shadow p-4 mb-4">
        <input type="text" id="filter-produk" placeholder="Cari produk untuk memfilter daftar..."
            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full min-w-[720px]" id="tabel-opname">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Produk</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Stok Sistem</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Stok Fisik</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Selisih</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($produks as $produk)
                    <tr class="hover:bg-gray-50 baris-produk" data-nama="{{ strtolower($produk->nama_produk . ' ' . $produk->kode_produk) }}">
                        <td class="px-4 py-3 font-mono text-sm">{{ $produk->kode_produk }}</td>
                        <td class="px-4 py-3 font-medium">{{ $produk->nama_produk }}</td>
                        <td class="px-4 py-3">{{ $produk->kategori->nama_kategori }}</td>
                        <td class="px-4 py-3 text-right stok-sistem">{{ $produk->stok }}</td>
                        <td class="px-4 py-3 text-right">
                            <input type="number" name="stok_fisik[{{ $produk->id }}]" value="{{ old('stok_fisik.' . $produk->id, $produk->stok) }}"
                                min="0" step="1"
                                class="w-24 border border-gray-300 rounded-lg px-2 py-1.5 text-right stok-fisik focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </td>
                        <td class="px-4 py-3 text-right font-medium selisih">0</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">Belum ada data produk</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="flex justify-end mt-4">
        <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700"
            onclick="return confirm('Simpan stock opname? Stok produk akan disesuaikan dengan stok fisik.')">
            <i class="fas fa-save mr-2"></i>Simpan Stock Opname
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.stok-fisik').forEach(function(input) {
        input.addEventListener('input', function() {
            var row = input.closest('tr');
            var sistem = parseInt(row.querySelector('.stok-sistem').textContent, 10);
            var fisik = parseInt(input.value, 10);
            var selisih = isNaN(fisik) ? 0 : fisik - sistem;
            var selisihEl = row.querySelector('.selisih');
            selisihEl.textContent = selisih > 0 ? '+' + selisih : selisih;
            selisihEl.className = 'px-4 py-3 text-right font-medium selisih ' +
                (selisih > 0 ? 'text-green-600' : (selisih < 0 ? 'text-red-600' : 'text-gray-500'));
        });
    });

    document.getElementById('filter-produk').addEventListener('input', function() {
        var keyword = this.value.toLowerCase();
        document.querySelectorAll('.baris-produk').forEach(function(row) {
            row.style.display = row.dataset.nama.includes(keyword) ? '' : 'none';
        });
    });
</script>
@endpush

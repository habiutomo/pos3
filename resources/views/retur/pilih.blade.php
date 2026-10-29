@extends('layouts.app')
@section('title', 'Retur Transaksi')

@section('content')
<div class="flex flex-wrap justify-between items-center gap-3 mb-6">
    <h2 class="text-xl lg:text-2xl font-bold">Retur: {{ $transaksi->no_transaksi }}</h2>
    <a href="{{ route('retur.create') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
        <i class="fas fa-arrow-left mr-2"></i>Kembali
    </a>
</div>

<div class="bg-white rounded-lg shadow p-4 mb-4">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
        <div>
            <p class="text-gray-500">No. Transaksi</p>
            <p class="font-medium font-mono">{{ $transaksi->no_transaksi }}</p>
        </div>
        <div>
            <p class="text-gray-500">Tanggal</p>
            <p class="font-medium">{{ $transaksi->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <div>
            <p class="text-gray-500">Kasir</p>
            <p class="font-medium">{{ $transaksi->user->name }}</p>
        </div>
        <div>
            <p class="text-gray-500">Total Transaksi</p>
            <p class="font-medium">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</p>
        </div>
    </div>
</div>

<form id="returForm" method="POST" action="{{ route('retur.store', $transaksi->id) }}">
    @csrf
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full min-w-[720px]">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Terjual</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Sudah Retur</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Sisa</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Harga</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Jumlah Retur</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @php $hasItem = false; @endphp
                @foreach($transaksi->detailTransaksis as $detail)
                    @php
                        $sudahRetur = $detail->returs()->sum('jumlah');
                        $sisa = $detail->jumlah - $sudahRetur;
                        if ($sisa > 0) $hasItem = true;
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <p class="font-medium">{{ $detail->produk->nama_produk }}</p>
                            <p class="text-xs text-gray-500">{{ $detail->produk->kode_produk }}</p>
                        </td>
                        <td class="px-6 py-4 text-right">{{ $detail->jumlah }} {{ $detail->satuan_jual }}</td>
                        <td class="px-6 py-4 text-right">{{ $sudahRetur }}</td>
                        <td class="px-6 py-4 text-right font-medium">{{ $sisa }}</td>
                        <td class="px-6 py-4 text-right">Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($sisa > 0)
                                <input type="number" name="quantities[{{ $detail->id }}]" min="0" max="{{ $sisa }}" value="0"
                                    data-price="{{ $detail->harga_satuan }}" data-id="{{ $detail->id }}"
                                    oninput="hitungRetur(this)"
                                    class="qty-retur w-20 border border-gray-300 rounded-lg px-2 py-1 text-center focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @else
                                <span class="text-xs text-green-600">Semua sudah diretur</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right font-medium sub-{{ $detail->id }}">0</td>
                    </tr>
                @endforeach
                @if(!$hasItem)
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">Semua item pada transaksi ini sudah diretur</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="bg-white rounded-lg shadow p-4 mt-4">
        <label class="block text-sm font-medium text-gray-600 mb-1">Alasan Retur (opsional)</label>
        <textarea name="alasan" rows="2" placeholder="Contoh: barang rusak, salah ukuran, dll."
            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>

        <div class="flex flex-wrap items-center justify-between gap-3 mt-4">
            <div>
                <p class="text-sm text-gray-500">Total Pengembalian</p>
                <p id="totalRetur" class="text-2xl font-bold text-red-600">Rp 0</p>
            </div>
            @if($hasItem)
            <button type="submit" id="btnProsesRetur" class="bg-red-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-red-700">
                <i class="fas fa-rotate-left mr-2"></i>Proses Retur
            </button>
            @endif
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
    function hitungRetur(el) {
        const qty = parseInt(el.value || '0', 10);
        const price = parseFloat(el.dataset.price);
        const detailId = el.dataset.id;
        const sub = qty * price;
        document.querySelector('.sub-' + detailId).textContent = 'Rp ' + sub.toLocaleString('id');

        let total = 0;
        document.querySelectorAll('.qty-retur').forEach(inp => {
            total += (parseInt(inp.value || '0', 10) * parseFloat(inp.dataset.price));
        });
        document.getElementById('totalRetur').textContent = 'Rp ' + total.toLocaleString('id');
    }
</script>
@endpush

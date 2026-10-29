<div class="tier-row grid grid-cols-12 gap-2 items-center" data-base="{{ $isBase ? '1' : '0' }}">
    <select name="satuan_tiers[{{ $i }}][satuan_id]" required
        class="col-span-12 md:col-span-4 tier-satuan border border-gray-300 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        <option value="">Satuan</option>
        @foreach($satuans as $satuan)
            <option value="{{ $satuan->id }}" {{ (string) $tier['satuan_id'] === (string) $satuan->id ? 'selected' : '' }}>
                {{ strtoupper($satuan->nama_satuan) }}
            </option>
        @endforeach
    </select>
    @if($isBase)
        <input type="hidden" name="satuan_tiers[{{ $i }}][konversi]" value="1">
        <div class="col-span-7 md:col-span-3">
            <span class="bg-blue-100 text-blue-700 text-xs font-medium px-2 py-1 rounded whitespace-nowrap">Dasar (x1)</span>
        </div>
    @else
        <div class="col-span-5 md:col-span-3 flex items-center gap-1">
            <span class="text-xs text-gray-500 whitespace-nowrap">=</span>
            <input type="number" name="satuan_tiers[{{ $i }}][konversi]" value="{{ $tier['konversi'] }}" required min="1" placeholder="1"
                class="w-full border border-gray-300 rounded-lg px-2 py-2 text-sm" title="Jumlah satuan dasar dalam 1 satuan ini">
        </div>
    @endif
    <div class="col-span-12 md:col-span-4 relative">
        <span class="absolute left-2 top-1/2 -translate-y-1/2 text-xs text-gray-400">Rp</span>
        <input type="text" inputmode="numeric"
            name="satuan_tiers[{{ $i }}][harga_jual]"
            value="{{ trim((string) ($tier['harga_jual'] ?? '')) === '' ? '' : number_format((int) round((float) $tier['harga_jual']), 0, ',', '.') }}"
            required placeholder="Harga jual"
            class="js-rupiah w-full border border-gray-300 rounded-lg pl-7 pr-2 py-2 text-sm">
    </div>
    @if($isBase)
        <div class="col-span-5 md:col-span-1"></div>
    @else
        <button type="button" onclick="hapusTier(this)" class="col-span-1 md:col-span-1 text-red-500 hover:text-red-700"><i class="fas fa-times"></i></button>
    @endif
</div>

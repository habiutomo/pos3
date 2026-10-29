@extends('layouts.app')
@section('title', 'Kasir')

@section('content')
<div class="flex flex-wrap justify-between items-center gap-3 mb-6">
    <h2 class="text-xl lg:text-2xl font-bold">Point of Sale</h2>
    <a href="{{ route('kasir.index') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">
        <i class="fas fa-history mr-2"></i>Riwayat Transaksi
    </a>
</div>

<div class="flex flex-col gap-6 lg:flex-row">
    <div class="flex-1 lg:max-w-[calc(100%-26rem)]">
        <div class="bg-white rounded-lg shadow p-4 mb-4">
            <div class="flex gap-2">
                <input type="text" id="searchProduk" placeholder="Scan/ ketik kode atau nama produk..."
                    class="flex-1 border-2 border-gray-300 rounded-lg px-4 py-3 text-lg focus:outline-none focus:border-blue-500"
                    autofocus>
                <button type="button" onclick="openScanner()" title="Scan Barcode dengan Kamera"
                    class="bg-blue-600 text-white px-5 rounded-lg hover:bg-blue-700 text-lg">
                    <i class="fas fa-barcode"></i>
                </button>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 mb-4">
            <div class="flex gap-2 flex-wrap">
                <button onclick="filterKategori('')" class="kategori-btn bg-blue-600 text-white px-3 py-1 rounded-full text-sm" data-kategori="">Semua</button>
                @foreach(\App\Models\Kategori::all() as $kat)
                    <button onclick="filterKategori('{{ $kat->id }}')" class="kategori-btn bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-sm" data-kategori="{{ $kat->id }}">
                        {{ $kat->nama_kategori }}
                    </button>
                @endforeach
            </div>
        </div>

        <div id="produkGrid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
            @foreach($produks as $produk)
                @php
                    $tiers = collect([[
                        'satuan_id' => $produk->satuan_id,
                        'nama' => $produk->satuan->nama_satuan,
                        'konversi' => 1,
                        'harga' => (float) $produk->harga_jual,
                    ]])->merge(
                        $produk->produkSatuans
                            ->reject(fn ($t) => (int) $t->satuan_id === (int) $produk->satuan_id)
                            ->map(fn ($t) => [
                                'satuan_id' => $t->satuan_id,
                                'nama' => $t->satuan->nama_satuan,
                                'konversi' => $t->konversi,
                                'harga' => (float) $t->harga_jual,
                            ])
                    )->values()->all();
                @endphp
                <div class="produk-card bg-white rounded-lg shadow p-3 cursor-pointer hover:shadow-md transition border-2 border-transparent hover:border-blue-400"
                    data-id="{{ $produk->id }}"
                    data-kode="{{ $produk->kode_produk }}"
                    data-barcode="{{ $produk->barcode }}"
                    data-nama="{{ $produk->nama_produk }}"
                    data-harga="{{ $produk->harga_jual }}"
                    data-stok="{{ $produk->stok }}"
                    data-kategori="{{ $produk->kategori_id }}"
                    data-tiers='{{ json_encode($tiers, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) }}'
                    onclick="addToCart(this)">
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-2 bg-gray-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-box text-2xl text-gray-400"></i>
                        </div>
                        <p class="font-medium text-sm truncate">{{ $produk->nama_produk }}</p>
                        <p class="text-xs text-gray-500">{{ $produk->kode_produk }}</p>
                        <p class="text-blue-600 font-bold text-sm">Rp {{ number_format($produk->harga_jual, 0, ',', '.') }} / {{ $produk->satuan->nama_satuan }}</p>
                        @if(count($tiers) > 1)
                            <p class="text-xs text-purple-600">+{{ count($tiers) - 1 }} satuan jual lain</p>
                        @endif
                        @if(\App\Models\Pengaturan::stokAktif())
                        <p class="text-xs {{ $produk->stok <= 5 ? 'text-red-500' : 'text-green-500' }}">Stok: {{ $produk->stok }} {{ $produk->satuan->nama_satuan }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Mobile cart toggle -->
    <button id="btnOpenCart" type="button" onclick="openCart()"
        class="lg:hidden fixed bottom-4 right-4 z-30 bg-blue-600 text-white px-5 py-3 rounded-full shadow-lg flex items-center gap-2">
        <i class="fas fa-shopping-cart"></i>
        <span>Keranjang</span>
        <span id="cartCountBadge" class="bg-white text-blue-700 text-xs font-bold px-2 py-0.5 rounded-full hidden">0</span>
    </button>

    <!-- Mobile cart bottom sheet -->
    <div id="cartSheet" class="lg:hidden fixed inset-0 z-40 hidden">
        <div id="cartSheetOverlay" onclick="closeCart()" class="absolute inset-0 bg-black bg-opacity-50"></div>
        <div class="absolute bottom-0 inset-x-0 bg-white rounded-t-2xl shadow-xl max-h-[85vh] flex flex-col">
            <div class="p-4 border-b bg-gray-50 rounded-t-2xl flex items-center justify-between">
                <h3 class="font-bold text-lg"><i class="fas fa-shopping-cart mr-2"></i>Keranjang</h3>
                <button type="button" onclick="closeCart()" class="text-gray-500 hover:text-gray-800 text-2xl leading-none">&times;</button>
            </div>
            <div class="p-4 overflow-y-auto flex-1" id="cartSheetBody">
                @include('kasir._cart-content')
            </div>
        </div>
    </div>

    <div class="hidden lg:block w-96">
        <div class="bg-white rounded-lg shadow sticky top-6">
            <div class="p-4 border-b bg-gray-50 rounded-t-lg">
                <h3 class="font-bold text-lg"><i class="fas fa-shopping-cart mr-2"></i>Keranjang</h3>
            </div>
            <div class="p-4">
                @include('kasir._cart-content')
            </div>
        </div>
    </div>
</div>

<form id="checkoutForm" method="POST" action="{{ route('kasir.checkout') }}" style="display:none">
    @csrf
    <input type="hidden" name="bayar" id="inputBayar">
</form>

<div id="scannerModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-4 w-full max-w-md mx-4">
        <div class="flex justify-between items-center mb-3">
            <h3 class="font-bold text-lg"><i class="fas fa-barcode mr-2"></i>Scan Barcode</h3>
            <button type="button" onclick="closeScanner()" class="text-gray-500 hover:text-gray-800 text-2xl leading-none">&times;</button>
        </div>
        <div id="reader" class="rounded-lg overflow-hidden"></div>
        <p class="text-sm text-gray-500 mt-2 text-center">Arahkan barcode produk ke kamera, atau gunakan alat scanner USB</p>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    let cart = [];
    const produkData = @json($produks);
    const stokAktif = @json(\App\Models\Pengaturan::stokAktif());

    function filterKategori(id) {
        document.querySelectorAll('.kategori-btn').forEach(btn => {
            btn.classList.remove('bg-blue-600', 'text-white');
            btn.classList.add('bg-gray-200', 'text-gray-700');
        });
        event.target.classList.remove('bg-gray-200', 'text-gray-700');
        event.target.classList.add('bg-blue-600', 'text-white');

        document.querySelectorAll('.produk-card').forEach(card => {
            if (!id || card.dataset.kategori == id) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }

    document.getElementById('searchProduk').addEventListener('input', function() {
        const search = this.value.toLowerCase();
        document.querySelectorAll('.produk-card').forEach(card => {
            const nama = card.dataset.nama.toLowerCase();
            const kode = card.dataset.kode.toLowerCase();
            const barcode = (card.dataset.barcode || '').toLowerCase();
            card.style.display = (nama.includes(search) || kode.includes(search) || (barcode && barcode.includes(search))) ? '' : 'none';
        });
    });

    document.getElementById('searchProduk').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const code = this.value.trim();
            if (!code) return;
            handleBarcode(code);
            this.value = '';
            document.querySelectorAll('.produk-card').forEach(card => card.style.display = '');
        }
    });

    function cariProdukByCode(code) {
        return document.querySelector(`.produk-card[data-barcode="${code}"]`) ||
               document.querySelector(`.produk-card[data-kode="${code}"]`);
    }

    function handleBarcode(code) {
        const card = cariProdukByCode(code);
        if (card) {
            addToCart(card);
        } else {
            alert('Produk dengan kode/barcode "' + code + '" tidak ditemukan!');
        }
    }

    let html5QrCode = null;

    const PESAN_TANPA_KAMERA = 'Kamera tidak ditemukan di perangkat ini.\n\nGunakan alat scan barcode USB: klik kotak pencarian produk di panel kiri, lalu scan barcode produk.';

    async function openScanner() {
        document.getElementById('scannerModal').classList.remove('hidden');
        document.getElementById('scannerModal').classList.add('flex');
        try {
            const cameras = await Html5Qrcode.getCameras();
            if (!cameras || cameras.length === 0) {
                alert(PESAN_TANPA_KAMERA);
                closeScanner();
                return;
            }
            html5QrCode = new Html5Qrcode('reader');
            await html5QrCode.start(
                { facingMode: 'environment' },
                { fps: 10, qrbox: { width: 250, height: 150 } },
                onScanSuccess
            );
        } catch (err) {
            const tanpaKamera = err && (err.name === 'NotFoundError' || String(err).includes('NotFound'));
            alert(tanpaKamera ? PESAN_TANPA_KAMERA : 'Tidak dapat mengakses kamera: ' + err);
            closeScanner();
        }
    }

    function closeScanner() {
        if (html5QrCode) {
            html5QrCode.stop().then(() => {
                html5QrCode.clear();
                html5QrCode = null;
            }).catch(() => { html5QrCode = null; });
        }
        document.getElementById('scannerModal').classList.add('hidden');
        document.getElementById('scannerModal').classList.remove('flex');
    }

    let lastScan = { text: '', time: 0 };

    function onScanSuccess(decodedText) {
        const now = Date.now();
        if (decodedText === lastScan.text && now - lastScan.time < 2000) return;
        lastScan = { text: decodedText, time: now };
        closeScanner();
        handleBarcode(decodedText.trim());
    }

    function addToCart(el) {
        const id = parseInt(el.dataset.id);
        const existing = cart.find(item => item.id === id);
        if (existing) {
            if (existing.jumlah >= maxJumlah(existing)) {
                alert('Stok tidak mencukupi!');
                return;
            }
            existing.jumlah++;
        } else {
            cart.push({
                id: id,
                kode: el.dataset.kode,
                nama: el.dataset.nama,
                stok: parseInt(el.dataset.stok),
                tiers: JSON.parse(el.dataset.tiers),
                satuanId: JSON.parse(el.dataset.tiers)[0].satuan_id,
                jumlah: 1
            });
        }
        renderCart();
    }

    function tierAktif(item) {
        return item.tiers.find(t => t.satuan_id === item.satuanId) || item.tiers[0];
    }

    function hargaSatuan(item) {
        return tierAktif(item).harga;
    }

    function maxJumlah(item) {
        if (!stokAktif) return Infinity;
        return Math.max(1, Math.floor(item.stok / tierAktif(item).konversi));
    }

    function gantiSatuan(id, satuanId) {
        const item = cart.find(i => i.id === id);
        if (!item) return;
        const sebelumnya = maxJumlah(item);
        item.satuanId = parseInt(satuanId);
        const maksBaru = maxJumlah(item);
        if (item.jumlah > maksBaru) {
            item.jumlah = maksBaru;
            if (maksBaru < sebelumnya || maksBaru === 0) alert('Stok tidak mencukupi untuk satuan ini!');
            if (maksBaru === 0) { removeFromCart(id); return; }
        }
        renderCart();
    }

    function removeFromCart(id) {
        cart = cart.filter(item => item.id !== id);
        renderCart();
    }

    function openCart() {
        document.getElementById('cartSheet').classList.remove('hidden');
        document.getElementById('cartSheet').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeCart() {
        document.getElementById('cartSheet').classList.add('hidden');
        document.getElementById('cartSheet').classList.remove('flex');
        document.body.style.overflow = '';
    }

    function updateJumlah(id, delta) {
        const item = cart.find(i => i.id === id);
        if (item) {
            item.jumlah += delta;
            if (item.jumlah <= 0) {
                removeFromCart(id);
                return;
            }
            if (item.jumlah > maxJumlah(item)) {
                item.jumlah = maxJumlah(item);
                alert('Stok tidak mencukupi!');
            }
        }
        renderCart();
    }

    function renderCart() {
        const containers = document.querySelectorAll('#cartItems');

        const htmlCart = cart.length === 0
            ? '<p class="text-center text-gray-400 py-8">Keranjang kosong</p>'
            : cart.map(item => {
                const tier = tierAktif(item);
                return `
                <div class="flex items-center justify-between bg-gray-50 rounded-lg p-2">
                    <div class="flex-1">
                        <p class="font-medium text-sm">${item.nama}</p>
                        <div class="flex items-center gap-1 mt-0.5">
                            <select onchange="gantiSatuan(${item.id}, this.value)"
                                class="text-xs border border-gray-300 rounded px-1 py-0.5 bg-white">
                                ${item.tiers.map(t => `
                                    <option value="${t.satuan_id}" ${t.satuan_id === item.satuanId ? 'selected' : ''}>
                                        ${t.nama}${t.konversi > 1 ? ` (${t.konversi})` : ''}
                                    </option>`).join('')}
                            </select>
                            <span class="text-xs ${tier.satuan_id !== item.tiers[0].satuan_id ? 'text-purple-600 font-medium' : 'text-gray-500'}">
                                Rp ${hargaSatuan(item).toLocaleString('id')}/${tier.nama}
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="updateJumlah(${item.id}, -1)" class="w-6 h-6 bg-red-100 text-red-600 rounded text-sm">-</button>
                        <span class="w-8 text-center text-sm font-medium">${item.jumlah}</span>
                        <button onclick="updateJumlah(${item.id}, 1)" class="w-6 h-6 bg-blue-100 text-blue-600 rounded text-sm">+</button>
                    </div>
                    <div class="text-right ml-2">
                        <p class="font-medium text-sm">Rp ${(hargaSatuan(item) * item.jumlah).toLocaleString('id')}</p>
                        <button onclick="removeFromCart(${item.id})" class="text-xs text-red-500">Hapus</button>
                    </div>
                </div>
            `;}).join('');

        containers.forEach(container => { container.innerHTML = htmlCart; });

        document.querySelectorAll('#btnCheckout').forEach(btn => {
            btn.disabled = cart.length === 0;
        });

        const subtotal = cart.reduce((sum, item) => sum + (hargaSatuan(item) * item.jumlah), 0);
        document.querySelectorAll('#subtotal').forEach(el => {
            el.textContent = 'Rp ' + subtotal.toLocaleString('id');
        });

        const totalQty = cart.reduce((sum, item) => sum + item.jumlah, 0);
        const badge = document.getElementById('cartCountBadge');
        if (totalQty > 0 && badge) {
            badge.textContent = totalQty;
            badge.classList.remove('hidden');
        } else if (badge) {
            badge.classList.add('hidden');
        }

        hitungKembalian();
    }

    function nilaiBayar() {
        const inputs = document.querySelectorAll('#bayar');
        for (const el of inputs) {
            const v = parseInt((el.value || '').replace(/\D/g, ''), 10) || 0;
            if (v > 0) return v;
        }
        return 0;
    }

    function hitungKembalian() {
        const subtotal = cart.reduce((sum, item) => sum + (hargaSatuan(item) * item.jumlah), 0);
        const bayar = nilaiBayar();
        const kembalian = bayar - subtotal;
        document.querySelectorAll('#kembalian').forEach(el => {
            el.textContent = 'Rp ' + kembalian.toLocaleString('id');
            el.className = kembalian >= 0 ? 'font-bold text-green-600' : 'font-bold text-red-600';
        });
    }

    function checkout() {
        if (cart.length === 0) {
            alert('Keranjang kosong!');
            return;
        }

        const subtotal = cart.reduce((sum, item) => sum + (hargaSatuan(item) * item.jumlah), 0);
        const bayar = nilaiBayar();

        if (bayar < subtotal) {
            alert('Jumlah bayar kurang!');
            return;
        }

        const form = document.getElementById('checkoutForm');
        const existingInputs = form.querySelectorAll('input[name]');
        existingInputs.forEach(input => {
            if (input.name.startsWith('items')) input.remove();
        });

        cart.forEach((item, index) => {
            const produkIdInput = document.createElement('input');
            produkIdInput.type = 'hidden';
            produkIdInput.name = `items[${index}][produk_id]`;
            produkIdInput.value = item.id;
            form.appendChild(produkIdInput);

            const satuanIdInput = document.createElement('input');
            satuanIdInput.type = 'hidden';
            satuanIdInput.name = `items[${index}][satuan_id]`;
            satuanIdInput.value = item.satuanId;
            form.appendChild(satuanIdInput);

            const jumlahInput = document.createElement('input');
            jumlahInput.type = 'hidden';
            jumlahInput.name = `items[${index}][jumlah]`;
            jumlahInput.value = item.jumlah;
            form.appendChild(jumlahInput);
        });

        document.getElementById('inputBayar').value = bayar;
        form.submit();
    }
</script>
@endpush

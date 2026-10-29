@extends('layouts.app')
@section('title', 'Struk Transaksi')

@section('content')
@php
    $pengaturan = \App\Models\Pengaturan::toko();
    $ukuran = $pengaturan['ukuran_kertas'];
    $panjang = $pengaturan['panjang_kertas'];
    $autoLebar = $ukuran === 'auto';
    $presisi = in_array($ukuran, ['35', '58p']);

    $lebarKarakter = match (true) {
        in_array($ukuran, ['32', '58']) => 32,
        $ukuran === '40' => 40,
        default => null,
    };
    $kolomMode = $lebarKarakter !== null;
    $lebar = match ($ukuran) {
        '32', '58' => '32ch',
        '40' => '40ch',
        '35' => '33mm',
        '58p' => '56mm',
        default => '72mm',
    };
    $lebarCetakMm = match ($ukuran) {
        '32', '58' => '58',
        '40' => '72',
        '58p' => '58',
        default => $ukuran,
    };
    $tinggiCetak = $panjang === 'auto' ? '297' : $panjang;

    if ($kolomMode) {
        $r_rp = fn ($n) => number_format((float) $n, 0, ',', '.');

        $r_pecahKata = function (string $teks) use ($lebarKarakter): array {
            $hasil = [];
            foreach (preg_split('/\s+/u', trim($teks)) ?: [] as $kata) {
                while (mb_strlen($kata) > $lebarKarakter) {
                    $hasil[] = mb_substr($kata, 0, $lebarKarakter);
                    $kata = mb_substr($kata, $lebarKarakter);
                }
                if ($kata !== '') {
                    $hasil[] = $kata;
                }
            }

            return $hasil;
        };

        $r_bungkus = function (string $teks) use ($lebarKarakter, $r_pecahKata): array {
            $baris = [];
            $aktif = '';
            foreach ($r_pecahKata($teks) as $kata) {
                if ($aktif === '') {
                    $aktif = $kata;
                } elseif (mb_strlen($aktif) + 1 + mb_strlen($kata) <= $lebarKarakter) {
                    $aktif .= ' ' . $kata;
                } else {
                    $baris[] = $aktif;
                    $aktif = $kata;
                }
            }
            if ($aktif !== '') {
                $baris[] = $aktif;
            }

            return $baris;
        };

        $r_tengah = function (string $teks) use ($lebarKarakter): string {
            $teks = rtrim($teks);
            if (mb_strlen($teks) >= $lebarKarakter) {
                return $teks;
            }
            $sisa = $lebarKarakter - mb_strlen($teks);

            return str_repeat(' ', intdiv($sisa, 2)) . $teks;
        };

        $r_bungkusTengah = fn (string $teks) => array_map($r_tengah, $r_bungkus($teks));

        $garisTipis = str_repeat('-', $lebarKarakter);
        $garisTebal = str_repeat('=', $lebarKarakter);

        $r_lines = $r_bungkusTengah(mb_strtoupper(trim($pengaturan['nama_toko'])));
        if ($pengaturan['alamat_toko']) {
            $r_lines = array_merge($r_lines, $r_bungkusTengah($pengaturan['alamat_toko']));
        }
        if ($pengaturan['telp_toko']) {
            $r_lines = array_merge($r_lines, $r_bungkusTengah('Telp: ' . $pengaturan['telp_toko']));
        }
        $r_lines[] = $garisTebal;
        foreach ($r_bungkusTengah('No : ' . $transaksi->no_transaksi) as $b) {
            $r_lines[] = $b;
        }
        foreach ($r_bungkusTengah('Tanggal : ' . $transaksi->created_at->format('d/m/Y H:i')) as $b) {
            $r_lines[] = $b;
        }
        foreach ($r_bungkusTengah('Kasir : ' . $transaksi->user->name) as $b) {
            $r_lines[] = $b;
        }
        $r_lines[] = $garisTipis;
        foreach ($transaksi->detailTransaksis as $detail) {
            foreach ($r_bungkusTengah($detail->produk->nama_produk) as $barisNama) {
                $r_lines[] = $barisNama;
            }
            foreach ($r_bungkusTengah(
                $detail->jumlah . ($detail->satuan_jual ? ' ' . $detail->satuan_jual : '') . ' x ' . $r_rp($detail->harga_satuan) . ' : ' . $r_rp($detail->subtotal)
            ) as $b) {
                $r_lines[] = $b;
            }
        }
        $r_lines[] = $garisTipis;
        foreach ($r_bungkusTengah('TOTAL : ' . $r_rp($transaksi->total_harga)) as $b) {
            $r_lines[] = $b;
        }
        foreach ($r_bungkusTengah('Tunai : ' . $r_rp($transaksi->bayar)) as $b) {
            $r_lines[] = $b;
        }
        foreach ($r_bungkusTengah('Kembalian : ' . $r_rp($transaksi->kembalian)) as $b) {
            $r_lines[] = $b;
        }
        $r_lines[] = $garisTebal;
        $r_lines = array_merge($r_lines, $r_bungkusTengah('Terima kasih atas kunjungan Anda!'));

        $r_teks = implode("\n", array_map(fn ($b) => htmlspecialchars(rtrim($b), ENT_QUOTES, 'UTF-8'), $r_lines));
    }
@endphp

<div class="struk-wrap mx-auto">
    @if($kolomMode)
    <div class="struk-paper bg-white rounded-lg shadow p-3">
        <pre class="struk-pre">{!! $r_teks !!}</pre>
    </div>
    @else
    <div class="struk-paper bg-white rounded-lg shadow p-4">
        <div class="text-center border-b pb-3 mb-3">
            <h2 class="r-title font-bold uppercase">{{ $pengaturan['nama_toko'] }}</h2>
            @if($pengaturan['alamat_toko'])
                <p class="r-text text-gray-500">{{ $pengaturan['alamat_toko'] }}</p>
            @endif
            @if($pengaturan['telp_toko'])
                <p class="r-text text-gray-500">Telp: {{ $pengaturan['telp_toko'] }}</p>
            @endif
        </div>

        <div class="mb-3">
            <div class="flex justify-between r-text">
                <span>No. Transaksi</span>
                <span class="font-mono">{{ $transaksi->no_transaksi }}</span>
            </div>
            <div class="flex justify-between r-text">
                <span>Tanggal</span>
                <span>{{ $transaksi->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="flex justify-between r-text">
                <span>Kasir</span>
                <span>{{ $transaksi->user->name }}</span>
            </div>
        </div>

        <div class="r-text mb-3 border-t border-b border-black py-1">
            @foreach($transaksi->detailTransaksis as $detail)
                <div class="py-0.5">
                    <div class="r-nama">{{ $detail->produk->nama_produk }}</div>
                    <div class="flex justify-between gap-2">
                        <span>{{ $detail->jumlah }}{{ $detail->satuan_jual ? ' ' . $detail->satuan_jual : '' }} x {{ number_format($detail->harga_satuan, 0, ',', '.') }}</span>
                        <span class="text-right whitespace-nowrap">{{ number_format($detail->subtotal, 0, ',', '.') }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="border-t border-dashed border-black pt-2 space-y-0.5">
            <div class="flex justify-between font-bold r-total">
                <span>TOTAL</span>
                <span>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between r-text">
                <span>Tunai</span>
                <span>Rp {{ number_format($transaksi->bayar, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between r-text">
                <span>Kembalian</span>
                <span>Rp {{ number_format($transaksi->kembalian, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="text-center border-t mt-3 pt-3">
            <p class="r-text text-gray-500">Terima kasih atas kunjungan Anda!</p>
        </div>
    </div>
    @endif

    <div class="no-print flex flex-wrap gap-3 mt-4">
        <a href="{{ route('kasir.index') }}" class="flex-1 min-w-[150px] bg-blue-600 text-white py-3 rounded-lg font-bold text-center hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i>Transaksi Baru
        </a>
        @if($kolomMode)
        <a href="{{ route('kasir.struk.bin', $transaksi->id) }}" class="flex-1 min-w-[150px] bg-emerald-600 text-white py-3 rounded-lg font-bold text-center hover:bg-emerald-700">
            <i class="fas fa-download mr-2"></i>ESC/POS (.bin)
        </a>
        @endif
        <button onclick="window.print()" class="flex-1 min-w-[150px] bg-gray-600 text-white py-3 rounded-lg font-bold text-center hover:bg-gray-700">
            <i class="fas fa-print mr-2"></i>Cetak Struk
        </button>
    </div>
    @if($kolomMode)
    <p class="no-print text-xs text-gray-500 mt-2">Cetak paling presisi: unduh file .bin lalu kirim langsung ke printer thermal tanpa lewat browser, contoh di Windows: <code>copy /b struk-{{ $transaksi->no_transaksi }}.bin \\localhost\NamaPrinter</code></p>
    @endif
</div>

<style>
    .struk-wrap { width: {{ $lebar }}; max-width: 100%; }
    @if($kolomMode)
    .struk-wrap {
        font-family: 'Courier New', ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
        font-size: 8mm;
    }
    .struk-pre { margin: 0; padding: 0; white-space: pre; overflow: hidden; line-height: 1.4; color: #000; }
    @else
    .struk-paper .r-text { font-size: {{ $autoLebar ? '4mm' : ($ukuran === '80' ? '15px' : '4mm') }}; line-height: 1.5; }
    .struk-paper .r-title { font-size: {{ $autoLebar ? '6mm' : ($ukuran === '80' ? '23px' : '6mm') }}; }
    .struk-paper .r-total { font-size: {{ $autoLebar ? '5mm' : ($ukuran === '80' ? '19px' : '5mm') }}; }
    .struk-paper { overflow-wrap: anywhere; word-break: break-word; }
    .struk-paper .flex { flex-wrap: wrap; }
    .struk-paper .flex > * { min-width: 0; }
    @endif

    @media print {
        @page {
            @if(($autoLebar || $panjang === 'auto') && ! $presisi)
                size: auto;
                margin: 2mm;
            @else
                size: {{ $lebarCetakMm }}mm {{ $tinggiCetak }}mm;
                margin: {{ $presisi ? '2mm 1mm' : '2mm 3mm' }};
            @endif
        }
        aside, .no-print { display: none !important; }
        html, body { margin: 0 !important; padding: 0 !important; background: white !important; }
        main { padding: 0 !important; }
        * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        @if($kolomMode)
        .struk-wrap { width: {{ $lebar }} !important; max-width: none !important; margin-left: auto !important; margin-right: auto !important; }
        .struk-pre { font-size: 4mm !important; line-height: 1.4 !important; }
        @else
        .struk-wrap { width: 100% !important; max-width: 100% !important; }
        @endif
        .struk-paper { box-shadow: none !important; border-radius: 0 !important; padding: 0 !important; width: 100% !important; }
    }
</style>
@endsection

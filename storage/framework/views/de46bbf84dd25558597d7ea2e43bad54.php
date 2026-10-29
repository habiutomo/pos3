<?php $__env->startSection('title', 'Struk Transaksi'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $pengaturan = \App\Models\Pengaturan::toko();
    $ukuran = $pengaturan['ukuran_kertas'];
    $panjang = $pengaturan['panjang_kertas'];
    $autoLebar = $ukuran === 'auto';
    $presisi = $ukuran === '58p';

    $lebarKarakter = match (true) {
        in_array($ukuran, ['32', '58']) => 32,
        $ukuran === '40' => 40,
        $ukuran === '48' => 48,
        default => null,
    };
    $kolomMode = $lebarKarakter !== null;
    $lebar = match ($ukuran) {
        '32', '58' => '32ch',
        '40' => '40ch',
        '48' => '48ch',
        '58p' => '56mm',
        default => '72mm',
    };
    $lebarCetakMm = match ($ukuran) {
        '32', '58' => '58',
        '40' => '72',
        '48' => '58',
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

        $lebarHeader = 28;
        $r_pecahKataHeader = function (string $teks) use ($lebarHeader): array {
            $hasil = [];
            foreach (preg_split('/\s+/u', trim($teks)) ?: [] as $kata) {
                while (mb_strlen($kata) > $lebarHeader) {
                    $hasil[] = mb_substr($kata, 0, $lebarHeader);
                    $kata = mb_substr($kata, $lebarHeader);
                }
                if ($kata !== '') {
                    $hasil[] = $kata;
                }
            }

            return $hasil;
        };

        $r_bungkusHeader = function (string $teks) use ($lebarHeader, $r_pecahKataHeader): array {
            $baris = [];
            $aktif = '';
            foreach ($r_pecahKataHeader($teks) as $kata) {
                if ($aktif === '') {
                    $aktif = $kata;
                } elseif (mb_strlen($aktif) + 1 + mb_strlen($kata) <= $lebarHeader) {
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

        $garisTipisHeader = str_repeat('-', $lebarHeader);
        $garisTebalHeader = str_repeat('=', $lebarHeader);

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

        $r_header = array_merge(
            array_fill(0, 5, ''),
            $r_bungkusHeader(mb_strtoupper(trim($pengaturan['nama_toko']))),
            $pengaturan['alamat_toko'] ? $r_bungkusHeader($pengaturan['alamat_toko']) : [],
            $pengaturan['telp_toko'] ? $r_bungkusHeader('Telp: ' . $pengaturan['telp_toko']) : [],
            [$garisTebalHeader],
            $r_bungkusHeader('No : ' . $transaksi->no_transaksi),
            $r_bungkusHeader('Tanggal : ' . $transaksi->created_at->format('d/m/Y H:i')),
            $r_bungkusHeader('Kasir : ' . $transaksi->user->name),
            [$garisTipisHeader]
        );

        $r_semua = $r_bungkus('Nama Produk  Qty  Harga  Total');

        foreach ($transaksi->detailTransaksis as $detail) {
            foreach ($r_bungkus($detail->produk->nama_produk) as $barisNama) {
                $r_semua[] = $barisNama;
            }
            foreach ($r_bungkus(
                $detail->jumlah . ($detail->satuan_jual ? ' ' . $detail->satuan_jual : '') . ' x ' . $r_rp($detail->harga_satuan) . ' : ' . $r_rp($detail->subtotal)
            ) as $b) {
                $r_semua[] = $b;
            }
        }

        $r_semua[] = $garisTipis;
        $r_semua = array_merge(
            $r_semua,
            $r_bungkus('TOTAL : ' . $r_rp($transaksi->total_harga)),
            $r_bungkus('Tunai : ' . $r_rp($transaksi->bayar)),
            $r_bungkus('Kembalian : ' . $r_rp($transaksi->kembalian)),
            [$garisTebal],
            $r_bungkus('Terima kasih atas kunjungan Anda!')
        );

        $r_teks = implode("\n", array_merge(
            array_map(fn ($b) => htmlspecialchars(str_repeat(' ', 5) . $r_tengah($b), ENT_QUOTES, 'UTF-8'), $r_header),
            array_map(fn ($b) => htmlspecialchars(str_repeat(' ', 5) . $r_tengah($b), ENT_QUOTES, 'UTF-8'), $r_semua)
        ));
    }
?>

<div class="struk-wrap mx-auto">
    <?php if($kolomMode): ?>
    <div class="struk-paper bg-white rounded-lg shadow p-3">
        <pre class="struk-pre"><?php echo $r_teks; ?></pre>
    </div>
    <?php else: ?>
    <div class="struk-paper bg-white rounded-lg shadow p-4">
        <div class="text-center border-b pb-3 mb-3">
            <h2 class="r-title font-bold uppercase"><?php echo e($pengaturan['nama_toko']); ?></h2>
            <?php if($pengaturan['alamat_toko']): ?>
                <p class="r-text text-gray-500"><?php echo e($pengaturan['alamat_toko']); ?></p>
            <?php endif; ?>
            <?php if($pengaturan['telp_toko']): ?>
                <p class="r-text text-gray-500">Telp: <?php echo e($pengaturan['telp_toko']); ?></p>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <div class="flex justify-between r-text">
                <span>No. Transaksi</span>
                <span><?php echo e($transaksi->no_transaksi); ?></span>
            </div>
            <div class="flex justify-between r-text">
                <span>Tanggal</span>
                <span><?php echo e($transaksi->created_at->format('d/m/Y H:i')); ?></span>
            </div>
            <div class="flex justify-between r-text">
                <span>Kasir</span>
                <span><?php echo e($transaksi->user->name); ?></span>
            </div>
        </div>

        <div class="r-text mb-3 border-t border-b border-black py-1">
            <?php $__currentLoopData = $transaksi->detailTransaksis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="py-0.5">
                    <div class="r-nama"><?php echo e($detail->produk->nama_produk); ?></div>
                    <div class="flex justify-between gap-2">
                        <span><?php echo e($detail->jumlah); ?><?php echo e($detail->satuan_jual ? ' ' . $detail->satuan_jual : ''); ?> x <?php echo e(number_format($detail->harga_satuan, 0, ',', '.')); ?></span>
                        <span class="text-right whitespace-nowrap"><?php echo e(number_format($detail->subtotal, 0, ',', '.')); ?></span>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="border-t border-dashed border-black pt-2 space-y-0.5">
            <div class="flex justify-between font-bold r-total">
                <span>TOTAL</span>
                <span>Rp <?php echo e(number_format($transaksi->total_harga, 0, ',', '.')); ?></span>
            </div>
            <div class="flex justify-between r-text">
                <span>Tunai</span>
                <span>Rp <?php echo e(number_format($transaksi->bayar, 0, ',', '.')); ?></span>
            </div>
            <div class="flex justify-between r-text">
                <span>Kembalian</span>
                <span>Rp <?php echo e(number_format($transaksi->kembalian, 0, ',', '.')); ?></span>
            </div>
        </div>

        <div class="text-center border-t mt-3 pt-3">
            <p class="r-text text-gray-500">Terima kasih atas kunjungan Anda!</p>
        </div>
    </div>
    <?php endif; ?>

    <div class="no-print flex flex-wrap gap-3 mt-4">
        <a href="<?php echo e(route('kasir.index')); ?>" class="flex-1 min-w-[150px] bg-blue-600 text-white py-3 rounded-lg font-bold text-center hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i>Transaksi Baru
        </a>
        <?php if($kolomMode): ?>
        <a href="<?php echo e(route('kasir.struk.bin', $transaksi->id)); ?>" class="flex-1 min-w-[150px] bg-emerald-600 text-white py-3 rounded-lg font-bold text-center hover:bg-emerald-700">
            <i class="fas fa-download mr-2"></i>ESC/POS (.bin)
        </a>
        <?php endif; ?>
        <button onclick="window.print()" class="flex-1 min-w-[150px] bg-gray-600 text-white py-3 rounded-lg font-bold text-center hover:bg-gray-700">
            <i class="fas fa-print mr-2"></i>Cetak Struk
        </button>
        <?php if($kolomMode): ?>
        <button onclick="cetakBluetooth(<?php echo e($transaksi->id); ?>)" data-bt-print
            class="flex-1 min-w-[150px] bg-purple-600 text-white py-3 rounded-lg font-bold text-center hover:bg-purple-700">
            <i class="fas fa-bluetooth mr-2"></i>Cetak Bluetooth
        </button>
        <?php endif; ?>
    </div>
    <?php if($kolomMode): ?>
    <p class="no-print text-xs text-gray-500 mt-2">Cetak paling presisi: unduh file .bin lalu kirim langsung ke printer thermal tanpa lewat browser, contoh di Windows: <code>copy /b struk-<?php echo e($transaksi->no_transaksi); ?>.bin \\localhost\NamaPrinter</code></p>
    <p class="no-print text-xs text-purple-500 mt-1"><i class="fas fa-bluetooth mr-1"></i>Untuk cetak Bluetooth: pastikan printer sudah dipairing di perangkat, lalu klik tombol "Cetak Bluetooth" di atas. Browser akan meminta izin Bluetooth saat pertama kali.</p>
    <?php endif; ?>
</div>

<style>
    .struk-wrap { width: <?php echo e($lebar); ?>; max-width: 100%; }
    <?php if($kolomMode): ?>
    .struk-wrap {
        font-family: Tahoma, 'Segoe UI', Helvetica, Arial, sans-serif;
        font-size: 8mm;
    }
    .struk-pre { margin: 0; padding: 0; white-space: pre-wrap; overflow-wrap: anywhere; line-height: 1.4; color: #000; text-align: left; }
    <?php else: ?>
    .struk-paper { font-family: Tahoma, 'Segoe UI', Helvetica, Arial, sans-serif; }
    .struk-paper .r-text { font-size: <?php echo e($autoLebar ? '4mm' : ($ukuran === '80' ? '15px' : '4mm')); ?>; line-height: 1.5; }
    .struk-paper .r-title { font-size: <?php echo e($autoLebar ? '6mm' : ($ukuran === '80' ? '23px' : '6mm')); ?>; }
    .struk-paper .r-total { font-size: <?php echo e($autoLebar ? '5mm' : ($ukuran === '80' ? '19px' : '5mm')); ?>; }
    .struk-paper { overflow-wrap: anywhere; word-break: break-word; }
    .struk-paper .flex { flex-wrap: wrap; }
    .struk-paper .flex > * { min-width: 0; }
    <?php endif; ?>
    .struk-paper *, .struk-pre * { font-family: Tahoma, 'Segoe UI', Helvetica, Arial, sans-serif !important; }

    @media print {
        @page {
            <?php if(($autoLebar || $panjang === 'auto') && ! $presisi): ?>
                size: auto;
                margin: 2mm;
            <?php else: ?>
                size: <?php echo e($lebarCetakMm); ?>mm <?php echo e($tinggiCetak); ?>mm;
                margin: <?php echo e($presisi ? '2mm 1mm' : '2mm 3mm'); ?>;
            <?php endif; ?>
        }
        aside, .no-print { display: none !important; }
        html, body { margin: 0 !important; padding: 0 !important; background: white !important; }
        main { padding: 0 !important; }
        * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        <?php if($kolomMode): ?>
        .struk-wrap { width: <?php echo e($lebar); ?> !important; max-width: none !important; margin-left: auto !important; margin-right: auto !important; }
        .struk-pre { font-size: 4mm !important; line-height: 1.4 !important; }
        <?php else: ?>
        .struk-wrap { width: <?php echo e($lebar); ?> !important; max-width: 100% !important; margin-left: auto !important; margin-right: auto !important; }
        <?php endif; ?>
        .struk-paper { box-shadow: none !important; border-radius: 0 !important; padding: 0 !important; width: 100% !important; }
    }
</style>

<?php if($kolomMode): ?>
<script src="/js/bluetooth-printer.js"></script>
<?php if(\App\Models\Pengaturan::get('auto_print_bluetooth', '0') === '1'): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        autoPrintBluetooth(<?php echo e($transaksi->id); ?>);
    });
</script>
<?php endif; ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\pos3.elnusaintegrated.cloud\resources\views\kasir\struk.blade.php ENDPATH**/ ?>
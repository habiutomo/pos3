<?php $__env->startSection('title', 'Retur Transaksi'); ?>

<?php $__env->startSection('content'); ?>
<div class="flex flex-wrap justify-between items-center gap-3 mb-6">
    <h2 class="text-xl lg:text-2xl font-bold">Retur: <?php echo e($transaksi->no_transaksi); ?></h2>
    <a href="<?php echo e(route('retur.create')); ?>" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
        <i class="fas fa-arrow-left mr-2"></i>Kembali
    </a>
</div>

<div class="bg-white rounded-lg shadow p-4 mb-4">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
        <div>
            <p class="text-gray-500">No. Transaksi</p>
            <p class="font-medium font-mono"><?php echo e($transaksi->no_transaksi); ?></p>
        </div>
        <div>
            <p class="text-gray-500">Tanggal</p>
            <p class="font-medium"><?php echo e($transaksi->created_at->format('d/m/Y H:i')); ?></p>
        </div>
        <div>
            <p class="text-gray-500">Kasir</p>
            <p class="font-medium"><?php echo e($transaksi->user->name); ?></p>
        </div>
        <div>
            <p class="text-gray-500">Total Transaksi</p>
            <p class="font-medium">Rp <?php echo e(number_format($transaksi->total_harga, 0, ',', '.')); ?></p>
        </div>
    </div>
</div>

<form id="returForm" method="POST" action="<?php echo e(route('retur.store', $transaksi->id)); ?>">
    <?php echo csrf_field(); ?>
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
                <?php $hasItem = false; ?>
                <?php $__currentLoopData = $transaksi->detailTransaksis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $sudahRetur = $detail->returs()->sum('jumlah');
                        $sisa = $detail->jumlah - $sudahRetur;
                        if ($sisa > 0) $hasItem = true;
                    ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <p class="font-medium"><?php echo e($detail->produk->nama_produk); ?></p>
                            <p class="text-xs text-gray-500"><?php echo e($detail->produk->kode_produk); ?></p>
                        </td>
                        <td class="px-6 py-4 text-right"><?php echo e($detail->jumlah); ?> <?php echo e($detail->satuan_jual); ?></td>
                        <td class="px-6 py-4 text-right"><?php echo e($sudahRetur); ?></td>
                        <td class="px-6 py-4 text-right font-medium"><?php echo e($sisa); ?></td>
                        <td class="px-6 py-4 text-right">Rp <?php echo e(number_format($detail->harga_satuan, 0, ',', '.')); ?></td>
                        <td class="px-6 py-4 text-center">
                            <?php if($sisa > 0): ?>
                                <input type="number" name="quantities[<?php echo e($detail->id); ?>]" min="0" max="<?php echo e($sisa); ?>" value="0"
                                    data-price="<?php echo e($detail->harga_satuan); ?>" data-id="<?php echo e($detail->id); ?>"
                                    oninput="hitungRetur(this)"
                                    class="qty-retur w-20 border border-gray-300 rounded-lg px-2 py-1 text-center focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <?php else: ?>
                                <span class="text-xs text-green-600">Semua sudah diretur</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-right font-medium sub-<?php echo e($detail->id); ?>">0</td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php if(!$hasItem): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">Semua item pada transaksi ini sudah diretur</td>
                    </tr>
                <?php endif; ?>
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
            <?php if($hasItem): ?>
            <button type="submit" id="btnProsesRetur" class="bg-red-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-red-700">
                <i class="fas fa-rotate-left mr-2"></i>Proses Retur
            </button>
            <?php endif; ?>
        </div>
    </div>
</form>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\pos3.elnusaintegrated.cloud\resources\views\retur\pilih.blade.php ENDPATH**/ ?>
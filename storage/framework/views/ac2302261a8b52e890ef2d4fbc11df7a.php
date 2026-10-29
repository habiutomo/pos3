<?php $__env->startSection('title', 'Detail Retur'); ?>

<?php $__env->startSection('content'); ?>
<div class="flex flex-wrap justify-between items-center gap-3 mb-6">
    <h2 class="text-xl lg:text-2xl font-bold">Detail Retur</h2>
    <a href="<?php echo e(route('retur.index')); ?>" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
        <i class="fas fa-arrow-left mr-2"></i>Riwayat Retur
    </a>
</div>

<div class="grid gap-4 lg:grid-cols-2">
    <div class="bg-white rounded-lg shadow p-4">
        <h3 class="font-bold text-lg mb-3"><i class="fas fa-receipt mr-2 text-gray-500"></i>Informasi</h3>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between border-b pb-1"><span class="text-gray-500">No. Retur</span><span class="font-mono text-red-600 font-medium"><?php echo e($retur->no_retur); ?></span></div>
            <div class="flex justify-between border-b pb-1"><span class="text-gray-500">Transaksi</span><span class="font-mono"><?php echo e($retur->transaksi->no_transaksi); ?></span></div>
            <div class="flex justify-between border-b pb-1"><span class="text-gray-500">Tanggal</span><span><?php echo e($retur->created_at->format('d/m/Y H:i')); ?></span></div>
            <div class="flex justify-between border-b pb-1"><span class="text-gray-500">Kasir</span><span><?php echo e($retur->user->name); ?></span></div>
            <div class="flex justify-between border-b pb-1"><span class="text-gray-500">Total Pengembalian</span><span class="font-bold text-red-600">Rp <?php echo e(number_format($retur->total_pengembalian, 0, ',', '.')); ?></span></div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full min-w-[480px]">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Harga</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php $__empty_1 = true; $__currentLoopData = $retur->detailReturs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium"><?php echo e($dr->produk->nama_produk); ?></td>
                        <td class="px-6 py-4 text-right"><?php echo e($dr->jumlah); ?><?php echo e($dr->satuan_jual ? ' ' . $dr->satuan_jual : ''); ?></td>
                        <td class="px-6 py-4 text-right">Rp <?php echo e(number_format($dr->harga_satuan, 0, ',', '.')); ?></td>
                        <td class="px-6 py-4 text-right font-medium">Rp <?php echo e(number_format($dr->subtotal, 0, ',', '.')); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="4" class="px-6 py-6 text-center text-gray-500">Tidak ada item</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if($retur->alasan): ?>
<div class="bg-white rounded-lg shadow p-4 mt-4">
    <p class="text-sm font-medium text-gray-600">Alasan Retur</p>
    <p class="mt-1"><?php echo e($retur->alasan); ?></p>
</div>
<?php endif; ?>

<div class="no-print flex flex-wrap gap-3 mt-4">
    <a href="<?php echo e(route('retur.create')); ?>" class="flex-1 min-w-[150px] bg-red-600 text-white py-3 rounded-lg font-bold text-center hover:bg-red-700">
        <i class="fas fa-rotate-left mr-2"></i>Retur Lain
    </a>
    <button onclick="window.print()" class="flex-1 min-w-[150px] bg-gray-600 text-white py-3 rounded-lg font-bold text-center hover:bg-gray-700">
        <i class="fas fa-print mr-2"></i>Cetak Laporan Retur
    </button>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\pos3.elnusaintegrated.cloud\resources\views\retur\show.blade.php ENDPATH**/ ?>
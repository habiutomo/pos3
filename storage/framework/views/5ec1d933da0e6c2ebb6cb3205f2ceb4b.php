<?php $__env->startSection('title', 'Retur Penjualan'); ?>

<?php $__env->startSection('content'); ?>
<div class="flex flex-wrap justify-between items-center gap-3 mb-6">
    <h2 class="text-xl lg:text-2xl font-bold">Retur Penjualan</h2>
    <a href="<?php echo e(route('retur.index')); ?>" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">
        <i class="fas fa-history mr-2"></i>Riwayat Retur
    </a>
</div>

<div class="bg-white rounded-lg shadow p-4 mb-4">
    <form method="GET" action="<?php echo e(route('retur.create')); ?>" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1">
            <label class="block text-sm font-medium text-gray-600 mb-1">Cari No. Transaksi</label>
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Ketik no. transaksi lalu tekan Cari..."
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700">
            <i class="fas fa-search mr-2"></i>Cari
        </button>
    </form>
</div>

<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="w-full min-w-[760px]">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Transaksi</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kasir</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php $__empty_1 = true; $__currentLoopData = $transaksis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-mono text-sm"><?php echo e($trx->no_transaksi); ?></td>
                    <td class="px-6 py-4 text-sm"><?php echo e($trx->created_at->format('d/m/Y H:i')); ?></td>
                    <td class="px-6 py-4"><?php echo e($trx->user->name); ?></td>
                    <td class="px-6 py-4 text-right font-medium">Rp <?php echo e(number_format($trx->total_harga, 0, ',', '.')); ?></td>
                    <td class="px-6 py-4 text-center">
                        <a href="<?php echo e(route('retur.pilih', $trx->id)); ?>" class="bg-blue-600 text-white px-3 py-1.5 rounded-lg hover:bg-blue-700 text-sm">
                            <i class="fas fa-rotate-left mr-1"></i>Retur
                        </a>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                        <?php if(request('search')): ?>
                            Tidak ada transaksi yang cocok dengan "<?php echo e(request('search')); ?>"
                        <?php else: ?>
                            Cari no. transaksi di atas untuk memulai retur
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\pos3.elnusaintegrated.cloud\resources\views\retur\create.blade.php ENDPATH**/ ?>
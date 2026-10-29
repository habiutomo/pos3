<?php $__env->startSection('title', 'Riwayat Retur'); ?>

<?php $__env->startSection('content'); ?>
<div class="flex flex-wrap justify-between items-center gap-3 mb-6">
    <h2 class="text-xl lg:text-2xl font-bold">Riwayat Retur</h2>
    <a href="<?php echo e(route('retur.create')); ?>" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
        <i class="fas fa-plus mr-2"></i>Buat Retur
    </a>
</div>

<div class="bg-white rounded-lg shadow p-4 mb-4">
    <form method="GET" action="<?php echo e(route('retur.index')); ?>" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1">
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari no. retur..."
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <input type="date" name="date" value="<?php echo e(request('date')); ?>"
                class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
            <i class="fas fa-search"></i>
        </button>
    </form>
</div>

<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="w-full min-w-[760px]">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Retur</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Transaksi</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kasir</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Retur</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php $__empty_1 = true; $__currentLoopData = $returs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $retur): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4"><?php echo e(($returs->currentPage() - 1) * $returs->perPage() + $loop->iteration); ?></td>
                    <td class="px-6 py-4 font-mono text-sm text-red-600"><?php echo e($retur->no_retur); ?></td>
                    <td class="px-6 py-4 font-mono text-sm"><?php echo e($retur->transaksi->no_transaksi); ?></td>
                    <td class="px-6 py-4 text-sm"><?php echo e($retur->created_at->format('d/m/Y H:i')); ?></td>
                    <td class="px-6 py-4"><?php echo e($retur->user->name); ?></td>
                    <td class="px-6 py-4 text-right font-medium text-red-600">Rp <?php echo e(number_format($retur->total_pengembalian, 0, ',', '.')); ?></td>
                    <td class="px-6 py-4 text-center">
                        <a href="<?php echo e(route('retur.show', $retur->id)); ?>" class="text-blue-600 hover:text-blue-800" title="Lihat Detail">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">Belum ada retur</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="mt-4">
    <?php echo e($returs->withQueryString()->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\pos3.elnusaintegrated.cloud\resources\views\retur\index.blade.php ENDPATH**/ ?>
<?php $__env->startSection('title', 'Detail Stock Opname'); ?>

<?php $__env->startSection('content'); ?>
<div class="flex flex-wrap justify-between items-center gap-3 mb-6">
    <h2 class="text-xl lg:text-2xl font-bold">Detail Stock Opname</h2>
    <a href="<?php echo e(route('stock-opname.index')); ?>" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
        <i class="fas fa-arrow-left mr-2"></i>Kembali
    </a>
</div>

<div class="bg-white rounded-lg shadow p-6 mb-4">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div>
            <p class="text-xs text-gray-500 uppercase">No. Opname</p>
            <p class="font-mono font-medium"><?php echo e($stockOpname->no_opname); ?></p>
        </div>
        <div>
            <p class="text-xs text-gray-500 uppercase">Tanggal</p>
            <p><?php echo e($stockOpname->created_at->format('d/m/Y H:i')); ?></p>
        </div>
        <div>
            <p class="text-xs text-gray-500 uppercase">Petugas</p>
            <p><?php echo e($stockOpname->user->name); ?></p>
        </div>
        <div>
            <p class="text-xs text-gray-500 uppercase">Catatan</p>
            <p><?php echo e($stockOpname->catatan ?: '-'); ?></p>
        </div>
    </div>
</div>

<?php
    $totalSelisih = $stockOpname->details->sum('selisih');
?>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-xs text-gray-500 uppercase">Jumlah Item Dicek</p>
        <p class="text-2xl font-bold"><?php echo e($stockOpname->details->count()); ?></p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-xs text-gray-500 uppercase">Item Selisih</p>
        <p class="text-2xl font-bold text-yellow-600"><?php echo e($stockOpname->details->where('selisih', '!=', 0)->count()); ?></p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-xs text-gray-500 uppercase">Total Selisih</p>
        <p class="text-2xl font-bold <?php echo e($totalSelisih > 0 ? 'text-green-600' : ($totalSelisih < 0 ? 'text-red-600' : 'text-gray-700')); ?>">
            <?php echo e($totalSelisih > 0 ? '+' . $totalSelisih : $totalSelisih); ?>

        </p>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="w-full min-w-[600px]">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Produk</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Stok Sistem</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Stok Fisik</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Selisih</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php $__currentLoopData = $stockOpname->details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3"><?php echo e($loop->iteration); ?></td>
                    <td class="px-4 py-3 font-mono text-sm"><?php echo e($detail->produk->kode_produk); ?></td>
                    <td class="px-4 py-3 font-medium"><?php echo e($detail->produk->nama_produk); ?></td>
                    <td class="px-4 py-3 text-right"><?php echo e($detail->stok_sistem); ?></td>
                    <td class="px-4 py-3 text-right"><?php echo e($detail->stok_fisik); ?></td>
                    <td class="px-4 py-3 text-right font-medium <?php echo e($detail->selisih > 0 ? 'text-green-600' : ($detail->selisih < 0 ? 'text-red-600' : 'text-gray-500')); ?>">
                        <?php echo e($detail->selisih > 0 ? '+' . $detail->selisih : $detail->selisih); ?>

                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\pos3.elnusaintegrated.cloud\resources\views\stock-opname\show.blade.php ENDPATH**/ ?>
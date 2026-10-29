<?php $__env->startSection('title', 'Satuan'); ?>

<?php $__env->startSection('content'); ?>
<div class="flex flex-wrap justify-between items-center gap-3 mb-6">
    <h2 class="text-xl lg:text-2xl font-bold">Satuan Produk</h2>
    <a href="<?php echo e(route('satuan.create')); ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
        <i class="fas fa-plus mr-2"></i>Tambah Satuan
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="w-full min-w-[500px]">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Satuan</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah Produk</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php $__empty_1 = true; $__currentLoopData = $satuans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $satuan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4"><?php echo e(($satuans->currentPage() - 1) * $satuans->perPage() + $loop->iteration); ?></td>
                    <td class="px-6 py-4 font-medium uppercase"><?php echo e($satuan->nama_satuan); ?></td>
                    <td class="px-6 py-4">
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded"><?php echo e($satuan->produks_count); ?> produk</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <a href="<?php echo e(route('satuan.edit', $satuan)); ?>" class="text-yellow-600 hover:text-yellow-800 mr-3">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="<?php echo e(route('satuan.destroy', $satuan)); ?>" method="POST" class="inline"
                            onsubmit="return confirm('Yakin hapus satuan ini?')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <?php if($satuan->produks_count > 0): ?>
                                <button type="submit" disabled title="Masih digunakan oleh produk"
                                    class="text-gray-300 cursor-not-allowed">
                                    <i class="fas fa-trash"></i>
                                </button>
                            <?php else: ?>
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i>
                                </button>
                            <?php endif; ?>
                        </form>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">Belum ada data satuan</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="mt-4">
    <?php echo e($satuans->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\pos3.elnusaintegrated.cloud\resources\views\satuan\index.blade.php ENDPATH**/ ?>
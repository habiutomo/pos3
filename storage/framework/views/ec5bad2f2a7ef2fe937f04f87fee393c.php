<?php $__env->startSection('title', 'Tambah Satuan'); ?>

<?php $__env->startSection('content'); ?>
<div class="flex flex-wrap justify-between items-center gap-3 mb-6">
    <h2 class="text-xl lg:text-2xl font-bold">Tambah Satuan</h2>
    <a href="<?php echo e(route('satuan.index')); ?>" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
        <i class="fas fa-arrow-left mr-2"></i>Kembali
    </a>
</div>

<div class="bg-white rounded-lg shadow p-6 max-w-lg">
    <form method="POST" action="<?php echo e(route('satuan.store')); ?>">
        <?php echo csrf_field(); ?>
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Satuan *</label>
            <input type="text" name="nama_satuan" value="<?php echo e(old('nama_satuan')); ?>" required maxlength="20"
                placeholder="contoh: pcs, dus, kg, liter..."
                autofocus
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <p class="text-xs text-gray-500 mt-1">Maksimal 20 karakter. Satuan akan tersedia sebagai pilihan saat input produk.</p>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
            <i class="fas fa-save mr-2"></i>Simpan
        </button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\pos3.elnusaintegrated.cloud\resources\views\satuan\create.blade.php ENDPATH**/ ?>
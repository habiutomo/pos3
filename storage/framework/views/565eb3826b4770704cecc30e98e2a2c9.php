<?php $__env->startSection('title', 'User'); ?>

<?php $__env->startSection('content'); ?>
<div class="flex flex-wrap justify-between items-center gap-3 mb-6">
    <h2 class="text-xl lg:text-2xl font-bold">Manajemen User</h2>
    <a href="<?php echo e(route('user.create')); ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
        <i class="fas fa-plus mr-2"></i>Tambah User
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="w-full min-w-[500px]">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4"><?php echo e(($users->currentPage() - 1) * $users->perPage() + $loop->iteration); ?></td>
                    <td class="px-6 py-4 font-medium">
                        <?php echo e($user->name); ?>

                        <?php if($user->id === auth()->id()): ?>
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded ml-1">Anda</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 text-gray-500"><?php echo e($user->email); ?></td>
                    <td class="px-6 py-4 text-center">
                        <a href="<?php echo e(route('user.edit', $user)); ?>" class="text-yellow-600 hover:text-yellow-800 mr-3">
                            <i class="fas fa-edit"></i>
                        </a>
                        <?php if (! ($user->id === auth()->id())): ?>
                            <form action="<?php echo e(route('user.destroy', $user)); ?>" method="POST" class="inline" onsubmit="return confirm('Yakin hapus user ini?')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">Belum ada data user</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="mt-4">
    <?php echo e($users->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\pos3.elnusaintegrated.cloud\resources\views\user\index.blade.php ENDPATH**/ ?>
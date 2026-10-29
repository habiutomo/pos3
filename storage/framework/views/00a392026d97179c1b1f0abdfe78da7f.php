<?php $__env->startSection('title', 'Produk'); ?>

<?php $__env->startSection('content'); ?>
<div class="flex flex-wrap justify-between items-center gap-3 mb-6">
    <h2 class="text-xl lg:text-2xl font-bold">Data Produk</h2>
    <div class="flex flex-wrap gap-2">
        <a href="<?php echo e(route('produk.import.form')); ?>" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
            <i class="fas fa-file-excel mr-2"></i>Import Excel
        </a>
        <a href="<?php echo e(route('produk.create')); ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i>Tambah Produk
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow p-4 mb-4">
    <form method="GET" action="<?php echo e(route('produk.index')); ?>" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1">
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari produk..."
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <select name="kategori_id" class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Kategori</option>
                <?php $__currentLoopData = $kategoris; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($kategori->id); ?>" <?php echo e(request('kategori_id') == $kategori->id ? 'selected' : ''); ?>>
                        <?php echo e($kategori->nama_kategori); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
            <i class="fas fa-search"></i>
        </button>
    </form>
</div>

<?php if($produks->isEmpty()): ?>
    <div class="bg-white rounded-lg shadow p-8 text-center text-gray-500">Belum ada data produk</div>
<?php else: ?>
    <!-- Mobile / tablet card layout -->
    <div class="space-y-4 md:hidden">
        <?php $__currentLoopData = $produks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $produk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-start justify-between gap-2 mb-2">
                    <div>
                        <p class="font-medium"><?php echo e($produk->nama_produk); ?></p>
                        <p class="text-xs text-gray-500 font-mono"><?php echo e($produk->kode_produk); ?></p>
                    </div>
                    <span class="text-xs bg-gray-100 text-gray-700 px-2 py-0.5 rounded whitespace-nowrap"><?php echo e($produk->kategori->nama_kategori); ?></span>
                </div>

                <?php $tiersLain = $produk->produkSatuans->reject(fn ($t) => (int) $t->satuan_id === (int) $produk->satuan_id); ?>
                <?php if($tiersLain->isNotEmpty()): ?>
                    <div class="flex flex-wrap gap-1 mb-2">
                        <?php $__currentLoopData = $tiersLain; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2 py-0.5 rounded">
                                <?php echo e(strtoupper($t->satuan->nama_satuan)); ?> = <?php echo e($t->konversi); ?> | Rp <?php echo e(number_format($t->harga_jual, 0, ',', '.')); ?>

                            </span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>

                <div class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm border-t border-gray-100 pt-3">
                    <?php if(\App\Models\Pengaturan::stokAktif()): ?>
                    <div>
                        <p class="text-xs text-gray-400">Stok</p>
                        <?php if($produk->stok == 0): ?>
                            <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-0.5 rounded">Habis</span>
                        <?php elseif($produk->stok <= 5): ?>
                            <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-0.5 rounded"><?php echo e($produk->stok); ?> <?php echo e($produk->satuan->nama_satuan); ?></span>
                        <?php else: ?>
                            <span class="text-green-600 font-medium"><?php echo e($produk->stok); ?> <?php echo e($produk->satuan->nama_satuan); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    <div>
                        <p class="text-xs text-gray-400">Harga Beli</p>
                        <p>Rp <?php echo e(number_format($produk->harga_beli, 0, ',', '.')); ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Harga Jual</p>
                        <p class="font-medium">Rp <?php echo e(number_format($produk->harga_jual, 0, ',', '.')); ?> <span class="text-xs text-gray-400">/ <?php echo e($produk->satuan->nama_satuan); ?></span></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Satuan Jual</p>
                        <p><?php echo e($produk->satuan->nama_satuan); ?></p>
                    </div>
                </div>

                <div class="flex items-center gap-3 mt-3 pt-3 border-t border-gray-100">
                    <a href="<?php echo e(route('produk.edit', $produk)); ?>" class="text-yellow-600 hover:text-yellow-800">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="<?php echo e(route('produk.destroy', $produk)); ?>" method="POST" onsubmit="return confirm('Yakin hapus produk ini?')">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="text-red-600 hover:text-red-800">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <!-- Desktop table layout -->
    <div class="hidden md:block bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full min-w-[900px]">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Produk</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                    <?php if(\App\Models\Pengaturan::stokAktif()): ?>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Stok</th>
                    <?php endif; ?>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Harga Beli</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Harga Jual</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Satuan Jual</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php $__currentLoopData = $produks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $produk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3"><?php echo e(($produks->currentPage() - 1) * $produks->perPage() + $loop->iteration); ?></td>
                        <td class="px-4 py-3 font-mono text-sm"><?php echo e($produk->kode_produk); ?></td>
                        <td class="px-4 py-3 font-medium"><?php echo e($produk->nama_produk); ?></td>
                        <td class="px-4 py-3"><?php echo e($produk->kategori->nama_kategori); ?></td>
                        <?php if(\App\Models\Pengaturan::stokAktif()): ?>
                        <td class="px-4 py-3 text-right">
                            <?php if($produk->stok == 0): ?>
                                <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-0.5 rounded">Habis</span>
                            <?php elseif($produk->stok <= 5): ?>
                                <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-0.5 rounded"><?php echo e($produk->stok); ?> <?php echo e($produk->satuan->nama_satuan); ?></span>
                            <?php else: ?>
                                <span class="text-green-600 font-medium"><?php echo e($produk->stok); ?> <?php echo e($produk->satuan->nama_satuan); ?></span>
                            <?php endif; ?>
                        </td>
                        <?php endif; ?>
                        <td class="px-4 py-3 text-right">Rp <?php echo e(number_format($produk->harga_beli, 0, ',', '.')); ?></td>
                        <td class="px-4 py-3 text-right font-medium">Rp <?php echo e(number_format($produk->harga_jual, 0, ',', '.')); ?> <span class="text-xs text-gray-400">/ <?php echo e($produk->satuan->nama_satuan); ?></span></td>
                        <td class="px-4 py-3 text-center">
                            <?php $tiersLain = $produk->produkSatuans->reject(fn ($t) => (int) $t->satuan_id === (int) $produk->satuan_id); ?>
                            <?php if($tiersLain->isNotEmpty()): ?>
                                <?php $__currentLoopData = $tiersLain; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2 py-0.5 rounded mr-1 mb-1 inline-block">
                                        <?php echo e(strtoupper($t->satuan->nama_satuan)); ?> = <?php echo e($t->konversi); ?> | Rp <?php echo e(number_format($t->harga_jual, 0, ',', '.')); ?>

                                    </span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                                <span class="text-gray-400">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="<?php echo e(route('produk.edit', $produk)); ?>" class="text-yellow-600 hover:text-yellow-800 mr-2">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="<?php echo e(route('produk.destroy', $produk)); ?>" method="POST" class="inline" onsubmit="return confirm('Yakin hapus produk ini?')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<div class="mt-4">
    <?php echo e($produks->withQueryString()->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\pos3.elnusaintegrated.cloud\resources\views\produk\index.blade.php ENDPATH**/ ?>
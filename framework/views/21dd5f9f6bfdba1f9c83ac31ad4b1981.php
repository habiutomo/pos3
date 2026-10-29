<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Kasir'); ?> - <?php echo e(config('app.name')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        #sidebar.open { transform: translateX(0); }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Mobile top bar -->
    <div class="lg:hidden fixed top-0 inset-x-0 z-40 bg-gray-900 text-white shadow-md">
        <div class="flex items-center justify-between px-4 h-14">
            <button type="button" onclick="document.getElementById('sidebar').classList.add('open'); document.getElementById('sidebarOverlay').classList.remove('hidden');" class="text-2xl leading-none hover:text-gray-300">
                <i class="fas fa-bars"></i>
            </button>
            <h1 class="text-lg font-bold"><i class="fas fa-store mr-2"></i><?php echo e(config('app.name')); ?></h1>
            <span class="w-6"></span>
        </div>
    </div>

    <!-- Sidebar overlay (mobile) -->
    <div id="sidebarOverlay" onclick="document.getElementById('sidebar').classList.remove('open'); this.classList.add('hidden');" class="hidden fixed inset-0 z-40 bg-black bg-opacity-50"></div>

    <div class="flex min-h-screen">
        <aside id="sidebar" class="fixed z-50 h-screen w-64 bg-gray-900 text-white flex flex-col transform -translate-x-full transition-transform duration-200 lg:translate-x-0 lg:static lg:h-auto lg:min-h-screen">
            <div class="p-4 border-b border-gray-700 flex items-center justify-between bg-gray-900">
                <h1 class="text-xl font-bold"><i class="fas fa-store mr-2"></i><?php echo e(config('app.name')); ?></h1>
                <button type="button" onclick="document.getElementById('sidebar').classList.remove('open'); document.getElementById('sidebarOverlay').classList.add('hidden');" class="lg:hidden text-2xl leading-none hover:text-gray-300">&times;</button>
            </div>
            <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                <a href="<?php echo e(route('dashboard')); ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-700 <?php echo e(request()->routeIs('dashboard') ? 'bg-gray-700' : ''); ?>">
                    <i class="fas fa-tachometer-alt w-6"></i> Dashboard
                </a>
                <a href="<?php echo e(route('kasir.index')); ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-700 <?php echo e(request()->routeIs('kasir.*') ? 'bg-gray-700' : ''); ?>">
                    <i class="fas fa-cash-register w-6"></i> Kasir
                </a>
                <a href="<?php echo e(route('kategori.index')); ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-700 <?php echo e(request()->routeIs('kategori.*') ? 'bg-gray-700' : ''); ?>">
                    <i class="fas fa-tags w-6"></i> Kategori
                </a>
                <a href="<?php echo e(route('satuan.index')); ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-700 <?php echo e(request()->routeIs('satuan.*') ? 'bg-gray-700' : ''); ?>">
                    <i class="fas fa-scale-balanced w-6"></i> Satuan
                </a>
                <a href="<?php echo e(route('produk.index')); ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-700 <?php echo e(request()->routeIs('produk.*') ? 'bg-gray-700' : ''); ?>">
                    <i class="fas fa-box w-6"></i> Produk
                </a>
                <?php if(\App\Models\Pengaturan::stokAktif()): ?>
                <a href="<?php echo e(route('stock-opname.index')); ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-700 <?php echo e(request()->routeIs('stock-opname.*') ? 'bg-gray-700' : ''); ?>">
                    <i class="fas fa-clipboard-check w-6"></i> Stock Opname
                </a>
                <?php endif; ?>
                <a href="<?php echo e(route('transaksi.index')); ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-700 <?php echo e(request()->routeIs('transaksi.*') ? 'bg-gray-700' : ''); ?>">
                    <i class="fas fa-receipt w-6"></i> Riwayat Transaksi
                </a>
                <a href="<?php echo e(route('rekap.index')); ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-700 <?php echo e(request()->routeIs('rekap.*') ? 'bg-gray-700' : ''); ?>">
                    <i class="fas fa-chart-column w-6"></i> Rekap Penjualan
                </a>
                <a href="<?php echo e(route('user.index')); ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-700 <?php echo e(request()->routeIs('user.*') ? 'bg-gray-700' : ''); ?>">
                    <i class="fas fa-users-gear w-6"></i> Manajemen User
                </a>
                <a href="<?php echo e(route('pengaturan.index')); ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-700 <?php echo e(request()->routeIs('pengaturan.*') ? 'bg-gray-700' : ''); ?>">
                    <i class="fas fa-gear w-6"></i> Pengaturan
                </a>
            </nav>
            <div class="p-4 border-t border-gray-700">
                <div class="flex items-center mb-3">
                    <i class="fas fa-user-circle w-6 mr-2"></i>
                    <span class="text-sm"><?php echo e(Auth::user()->name); ?></span>
                </div>
                <form method="POST" action="<?php echo e(route('logout')); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="flex items-center w-full px-3 py-2 rounded-lg hover:bg-red-600 text-red-400 hover:text-white">
                        <i class="fas fa-sign-out-alt w-6"></i> Logout
                    </button>
                </form>
            </div>
        </aside>

        <main class="flex-1 p-4 lg:p-6 pt-20 lg:pt-6">
            <?php if(session('success')): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <i class="fas fa-check-circle mr-2"></i><?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <i class="fas fa-exclamation-circle mr-2"></i><?php echo e(session('error')); ?>

                </div>
            <?php endif; ?>
            <?php if($errors->any()): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="list-disc list-inside">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>

    <?php echo $__env->yieldPushContent('scripts'); ?>
    <script>
        function formatRupiah(el) {
            const bersih = el.value.replace(/\D/g, '');
            el.value = bersih ? Number(bersih).toLocaleString('id-ID') : '';
        }
        document.addEventListener('input', function (e) {
            if (e.target.classList && e.target.classList.contains('js-rupiah')) formatRupiah(e.target);
        });
        document.addEventListener('submit', function (e) {
            e.target.querySelectorAll('.js-rupiah').forEach(function (el) {
                el.value = el.value.replace(/\D/g, '');
            });
        }, true);
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.js-rupiah').forEach(formatRupiah);
        });
    </script>
</body>
</html>
<?php /**PATH D:\kasir-app\resources\views/layouts/app.blade.php ENDPATH**/ ?>
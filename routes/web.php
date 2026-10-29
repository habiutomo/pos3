<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\RekapController;
use App\Http\Controllers\ReturController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes(['register' => false]);

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('kategori', KategoriController::class)->except(['show']);
    Route::resource('satuan', SatuanController::class)->except(['show']);

    Route::get('/produk/import', [ProdukController::class, 'importForm'])->name('produk.import.form');
    Route::post('/produk/import', [ProdukController::class, 'importStore'])->name('produk.import.store');
    Route::get('/produk/import/template', [ProdukController::class, 'importTemplate'])->name('produk.import.template');
    Route::resource('produk', ProdukController::class)->except(['show']);

    Route::get('/kasir', [TransaksiController::class, 'kasir'])->name('kasir.index');
    Route::get('/kasir/get-produk/{id}', [TransaksiController::class, 'getProduk'])->name('kasir.getProduk');
    Route::post('/kasir/checkout', [TransaksiController::class, 'checkout'])->name('kasir.checkout');
    Route::get('/kasir/struk/{id}', [TransaksiController::class, 'struk'])->name('kasir.struk');
    Route::get('/kasir/struk/{id}/bin', [TransaksiController::class, 'strukBin'])->name('kasir.struk.bin');

    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::delete('/transaksi/{transaksi}', [TransaksiController::class, 'destroy'])->name('transaksi.destroy');

    Route::get('/retur', [ReturController::class, 'index'])->name('retur.index');
    Route::get('/retur/create', [ReturController::class, 'create'])->name('retur.create');
    Route::get('/retur/transaksi/{transaksi}', [ReturController::class, 'pilihTransaksi'])->name('retur.pilih');
    Route::post('/retur/transaksi/{transaksi}', [ReturController::class, 'store'])->name('retur.store');
    Route::get('/retur/{retur}', [ReturController::class, 'show'])->name('retur.show');

    Route::resource('stock-opname', StockOpnameController::class)->except(['edit', 'update', 'destroy']);

    Route::get('/rekap', [RekapController::class, 'index'])->name('rekap.index');
    Route::get('/rekap/print', [RekapController::class, 'print'])->name('rekap.print');
    Route::get('/rekap/export', [RekapController::class, 'export'])->name('rekap.export');

    Route::resource('user', UserController::class)->except(['show']);

    Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
    Route::post('/pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');
});

Route::get('/home', [HomeController::class, 'index'])->name('home');

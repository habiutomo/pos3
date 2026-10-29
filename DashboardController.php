<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Pengaturan;
use App\Models\Produk;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $total_produk = Produk::count();
        $total_kategori = Kategori::count();
        $total_transaksi = Transaksi::count();
        $total_pendapatan = Transaksi::sum('total_harga');
        $stok_habis = Pengaturan::stokAktif() ? Produk::where('stok', 0)->count() : 0;
        $transaksi_hari_ini = Transaksi::whereDate('created_at', today())->count();
        $pendapatan_hari_ini = Transaksi::whereDate('created_at', today())->sum('total_harga');
        $produk_laris = DB::table('detail_transaksis')
            ->join('produks', 'detail_transaksis.produk_id', '=', 'produks.id')
            ->select('produks.nama_produk', DB::raw('SUM(detail_transaksis.jumlah) as total_terjual'))
            ->groupBy('produks.nama_produk')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get();

        $transaksi_terbaru = Transaksi::with('user')
            ->latest()
            ->limit(10)
            ->get();

        return view('dashboard', compact(
            'total_produk', 'total_kategori', 'total_transaksi',
            'total_pendapatan', 'stok_habis', 'transaksi_hari_ini',
            'pendapatan_hari_ini', 'produk_laris', 'transaksi_terbaru'
        ));
    }
}

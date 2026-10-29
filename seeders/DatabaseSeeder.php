<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@kasir.com',
            'password' => Hash::make('password'),
        ]);

        $kategoris = [
            ['nama_kategori' => 'Makanan', 'deskripsi' => 'Jenis makanan ringan dan berat'],
            ['nama_kategori' => 'Minuman', 'deskripsi' => 'Jenis minuman segar dan kemasan'],
            ['nama_kategori' => 'Sembako', 'deskripsi' => 'Kebutuhan pokok sehari-hari'],
            ['nama_kategori' => 'Rokok', 'deskripsi' => 'Berbagai merk rokok'],
            ['nama_kategori' => 'Kebutuhan Mandi', 'deskripsi' => 'Sabun, sampo, dll'],
        ];

        foreach ($kategoris as $kategori) {
            Kategori::create($kategori);
        }

        $satuans = ['pcs', 'dus', 'box', 'pack', 'renceng', 'kg', 'gram', 'liter', 'ml', 'meter', 'lembar', 'botol', 'kaleng', 'sachet', 'unit'];
        $satuanMap = [];
        foreach ($satuans as $nama) {
            $satuan = \App\Models\Satuan::firstOrCreate(['nama_satuan' => $nama]);
            $satuanMap[$nama] = $satuan->id;
        }

        $produks = [
            ['kategori_id' => 1, 'kode_produk' => 'MKN001', 'nama_produk' => 'Indomie Goreng', 'satuan_id' => $satuanMap['pcs'], 'stok' => 50, 'harga_beli' => 2500, 'harga_jual' => 3500],
            ['kategori_id' => 1, 'kode_produk' => 'MKN002', 'nama_produk' => 'Indomie Kuah Soto', 'satuan_id' => $satuanMap['pcs'], 'stok' => 40, 'harga_beli' => 2500, 'harga_jual' => 3500],
            ['kategori_id' => 1, 'kode_produk' => 'MKN003', 'nama_produk' => 'Nabati Richeese', 'satuan_id' => $satuanMap['pcs'], 'stok' => 30, 'harga_beli' => 1000, 'harga_jual' => 1500],
            ['kategori_id' => 1, 'kode_produk' => 'MKN004', 'nama_produk' => 'Chitato Lite', 'satuan_id' => $satuanMap['pcs'], 'stok' => 20, 'harga_beli' => 8000, 'harga_jual' => 11000],
            ['kategori_id' => 2, 'kode_produk' => 'MNM001', 'nama_produk' => 'Aqua 600ml', 'satuan_id' => $satuanMap['botol'], 'stok' => 60, 'harga_beli' => 3000, 'harga_jual' => 4000],
            ['kategori_id' => 2, 'kode_produk' => 'MNM002', 'nama_produk' => 'Coca Cola 330ml', 'satuan_id' => $satuanMap['kaleng'], 'stok' => 40, 'harga_beli' => 5000, 'harga_jual' => 7000],
            ['kategori_id' => 2, 'kode_produk' => 'MNM003', 'nama_produk' => 'Kopi ABC Sachet', 'satuan_id' => $satuanMap['sachet'], 'stok' => 100, 'harga_beli' => 1500, 'harga_jual' => 2000],
            ['kategori_id' => 2, 'kode_produk' => 'MNM004', 'nama_produk' => 'Teh Pucuk 350ml', 'satuan_id' => $satuanMap['botol'], 'stok' => 35, 'harga_beli' => 3000, 'harga_jual' => 4000],
            ['kategori_id' => 3, 'kode_produk' => 'SMB001', 'nama_produk' => 'Beras 1kg', 'satuan_id' => $satuanMap['kg'], 'stok' => 25, 'harga_beli' => 12000, 'harga_jual' => 14000],
            ['kategori_id' => 3, 'kode_produk' => 'SMB002', 'nama_produk' => 'Gula Pasir 1kg', 'satuan_id' => $satuanMap['kg'], 'stok' => 20, 'harga_beli' => 13000, 'harga_jual' => 15000],
            ['kategori_id' => 3, 'kode_produk' => 'SMB003', 'nama_produk' => 'Minyak Goreng 1L', 'satuan_id' => $satuanMap['liter'], 'stok' => 15, 'harga_beli' => 15000, 'harga_jual' => 17000],
            ['kategori_id' => 3, 'kode_produk' => 'SMB004', 'nama_produk' => 'Telur Ayam 1kg', 'satuan_id' => $satuanMap['kg'], 'stok' => 10, 'harga_beli' => 25000, 'harga_jual' => 28000],
            ['kategori_id' => 4, 'kode_produk' => 'ROK001', 'nama_produk' => 'Surya 12', 'satuan_id' => $satuanMap['pcs'], 'stok' => 50, 'harga_beli' => 18000, 'harga_jual' => 20000],
            ['kategori_id' => 4, 'kode_produk' => 'ROK002', 'nama_produk' => 'Djarum Super', 'satuan_id' => $satuanMap['pcs'], 'stok' => 40, 'harga_beli' => 20000, 'harga_jual' => 22000],
            ['kategori_id' => 5, 'kode_produk' => 'KBM001', 'nama_produk' => 'Lifebuoy Sabun', 'satuan_id' => $satuanMap['pcs'], 'stok' => 30, 'harga_beli' => 3500, 'harga_jual' => 5000],
            ['kategori_id' => 5, 'kode_produk' => 'KBM002', 'nama_produk' => 'Pantene Sampo Sachet', 'satuan_id' => $satuanMap['sachet'], 'stok' => 40, 'harga_beli' => 1000, 'harga_jual' => 1500],
            ['kategori_id' => 5, 'kode_produk' => 'KBM003', 'nama_produk' => 'Rexona Deo', 'satuan_id' => $satuanMap['pcs'], 'stok' => 20, 'harga_beli' => 12000, 'harga_jual' => 15000],
        ];

        foreach ($produks as $produk) {
            Produk::create($produk);
        }
    }
}

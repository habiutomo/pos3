<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Pengaturan;
use App\Models\Produk;
use App\Models\Satuan;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SimpanStrukHtmlTest extends TestCase
{
    use RefreshDatabase;

    public function test_simpan_varian_html_struk_32_kolom(): void
    {
        $dir = 'C:/Users/Administrator/AppData/Local/Temp/opencode';

        Pengaturan::set('nama_toko', 'Toko Sembako Berkah');
        Pengaturan::set('alamat_toko', 'Jl. Merdeka No. 123');
        Pengaturan::set('telp_toko', '0812-3456-7890');
        Pengaturan::set('ukuran_kertas', '32');
        Pengaturan::set('panjang_kertas', '150');

        $this->actingAs(User::factory()->create());
        $kategori = Kategori::create(['nama_kategori' => 'Kat']);
        $satuan = Satuan::where('nama_satuan', 'pcs')->firstOrFail();
        $produk = Produk::create([
            'kategori_id' => $kategori->id,
            'kode_produk' => 'KD-STRUK',
            'nama_produk' => 'Kopi Susu Kaleng',
            'stok' => 100,
            'harga_beli' => 5000,
            'harga_jual' => 8000,
            'satuan_id' => $satuan->id,
        ]);

        $this->post(route('kasir.checkout'), [
            'items' => [
                ['produk_id' => $produk->id, 'satuan_id' => $satuan->id, 'jumlah' => 3],
            ],
            'bayar' => 50000,
        ]);
        $transaksi = Transaksi::latest('id')->firstOrFail();

        file_put_contents("{$dir}/struk_32kolom.html", $this->get(route('kasir.struk', $transaksi->id))->getContent());

        $this->assertFileExists("{$dir}/struk_32kolom.html");
    }
}

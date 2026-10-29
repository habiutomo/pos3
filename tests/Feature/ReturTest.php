<?php

namespace Tests\Feature;

use App\Models\DetailTransaksi;
use App\Models\Kategori;
use App\Models\Pengaturan;
use App\Models\Produk;
use App\Models\Retur;
use App\Models\Satuan;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReturTest extends TestCase
{
    use RefreshDatabase;

    private function login(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        return $user;
    }

    private function buatProduk(int $stok): Produk
    {
        $kategori = Kategori::create(['nama_kategori' => 'Retur Kat']);
        $satuan = Satuan::where('nama_satuan', 'pcs')->firstOrFail();

        return Produk::create([
            'kategori_id' => $kategori->id,
            'kode_produk' => 'RET-' . uniqid(),
            'nama_produk' => 'Produk Retur Uji',
            'stok' => $stok,
            'harga_beli' => 1000,
            'harga_jual' => 2000,
            'satuan_id' => $satuan->id,
        ]);
    }

    private function buatTransaksi(array $items): Transaksi
    {
        return Transaksi::create([
            'no_transaksi' => 'TRX-TEST-' . uniqid(),
            'user_id' => auth()->id() ?: $this->login()->id,
            'total_harga' => collect($items)->sum(fn ($i) => $i['harga'] * $i['jumlah']),
            'bayar' => collect($items)->sum(fn ($i) => $i['harga'] * $i['jumlah']),
            'kembalian' => 0,
        ]);
    }

    public function test_halaman_create_retur_tampil(): void
    {
        $this->login();

        $response = $this->get(route('retur.create'));
        $response->assertStatus(200);
        $response->assertSee('Retur Penjualan');
    }

    public function test_halaman_pilih_transaksi_menampilkan_item(): void
    {
        $this->login();
        $produk = $this->buatProduk(10);
        $trx = $this->buatTransaksi([]);
        DetailTransaksi::create([
            'transaksi_id' => $trx->id,
            'produk_id' => $produk->id,
            'jumlah' => 3,
            'satuan_jual' => 'pcs',
            'harga_satuan' => 2000,
            'subtotal' => 6000,
        ]);

        $response = $this->get(route('retur.pilih', $trx->id));
        $response->assertStatus(200);
        $response->assertSee($produk->nama_produk);
    }

    public function test_retur_mengembalikan_stok_dan_membuat_catatan(): void
    {
        $this->login();
        Pengaturan::set('fitur_stok', '1');
        $produk = $this->buatProduk(10);
        $trx = $this->buatTransaksi([]);
        $detail = DetailTransaksi::create([
            'transaksi_id' => $trx->id,
            'produk_id' => $produk->id,
            'jumlah' => 3,
            'satuan_jual' => 'pcs',
            'harga_satuan' => 2000,
            'subtotal' => 6000,
        ]);

        $response = $this->post(route('retur.store', $trx->id), [
            'quantities' => [$detail->id => 3],
            'alasan' => 'Barang rusak',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertSame(13, $produk->fresh()->stok);
        $this->assertDatabaseHas('returs', ['transaksi_id' => $trx->id, 'total_pengembalian' => 6000]);
        $this->assertDatabaseHas('detail_returs', [
            'detail_transaksi_id' => $detail->id,
            'jumlah' => 3,
            'subtotal' => 6000,
        ]);
    }

    public function test_retur_parsial_hanya_mengembalikan_sebagian(): void
    {
        $this->login();
        Pengaturan::set('fitur_stok', '1');
        $produk = $this->buatProduk(10);
        $trx = $this->buatTransaksi([]);
        $detail = DetailTransaksi::create([
            'transaksi_id' => $trx->id,
            'produk_id' => $produk->id,
            'jumlah' => 5,
            'satuan_jual' => 'pcs',
            'harga_satuan' => 2000,
            'subtotal' => 10000,
        ]);

        $this->post(route('retur.store', $trx->id), [
            'quantities' => [$detail->id => 2],
        ])->assertSessionHas('success');

        $this->assertSame(12, $produk->fresh()->stok);
    }

    public function test_retur_ditolak_jika_melebihi_jumlah_terjual(): void
    {
        $this->login();
        Pengaturan::set('fitur_stok', '1');
        $produk = $this->buatProduk(10);
        $trx = $this->buatTransaksi([]);
        $detail = DetailTransaksi::create([
            'transaksi_id' => $trx->id,
            'produk_id' => $produk->id,
            'jumlah' => 3,
            'satuan_jual' => 'pcs',
            'harga_satuan' => 2000,
            'subtotal' => 6000,
        ]);

        $response = $this->post(route('retur.store', $trx->id), [
            'quantities' => [$detail->id => 5],
        ]);

        $response->assertSessionHas('error');
        $this->assertSame(10, $produk->fresh()->stok);
        $this->assertDatabaseMissing('returs', ['transaksi_id' => $trx->id]);
    }

    public function test_retur_ditolak_jika_tidak_memilih_item(): void
    {
        $this->login();
        $produk = $this->buatProduk(10);
        $trx = $this->buatTransaksi([]);
        $detail = DetailTransaksi::create([
            'transaksi_id' => $trx->id,
            'produk_id' => $produk->id,
            'jumlah' => 3,
            'satuan_jual' => 'pcs',
            'harga_satuan' => 2000,
            'subtotal' => 6000,
        ]);

        $response = $this->post(route('retur.store', $trx->id), [
            'quantities' => [$detail->id => 0],
        ]);

        $response->assertSessionHas('error');
        $this->assertSame(10, $produk->fresh()->stok);
    }

    public function test_riwayat_dan_detail_retur_tampil(): void
    {
        $this->login();
        $produk = $this->buatProduk(10);
        $trx = $this->buatTransaksi([]);
        $detail = DetailTransaksi::create([
            'transaksi_id' => $trx->id,
            'produk_id' => $produk->id,
            'jumlah' => 2,
            'satuan_jual' => 'pcs',
            'harga_satuan' => 2000,
            'subtotal' => 4000,
        ]);

        $this->post(route('retur.store', $trx->id), ['quantities' => [$detail->id => 2]]);

        $retur = Retur::first();

        $this->get(route('retur.index'))->assertStatus(200)->assertSee($retur->no_retur);
        $this->get(route('retur.show', $retur->id))
            ->assertStatus(200)
            ->assertSee($retur->no_retur)
            ->assertSee($produk->nama_produk);
    }
}

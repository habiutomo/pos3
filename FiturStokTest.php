<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Pengaturan;
use App\Models\Produk;
use App\Models\Satuan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FiturStokTest extends TestCase
{
    use RefreshDatabase;

    private function buatProduk(int $stok): Produk
    {
        $kategori = Kategori::create(['nama_kategori' => 'Test Kat']);
        $satuan = Satuan::where('nama_satuan', 'pcs')->firstOrFail();

        return Produk::create([
            'kategori_id' => $kategori->id,
            'kode_produk' => 'KD-' . uniqid(),
            'nama_produk' => 'Produk Uji',
            'stok' => $stok,
            'harga_beli' => 1000,
            'harga_jual' => 2000,
            'satuan_id' => $satuan->id,
        ]);
    }

    private function login(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        return $user;
    }

    public function test_fitur_stok_aktif_secara_default(): void
    {
        $this->assertTrue(Pengaturan::stokAktif());
    }

    public function test_ketika_nonaktif_kasir_menampilkan_produk_stok_nol(): void
    {
        $this->login();
        Pengaturan::set('fitur_stok', '0');
        $produk = $this->buatProduk(0);

        $response = $this->get(route('kasir.index'));
        $response->assertStatus(200);
        $response->assertSee($produk->kode_produk);
    }

    public function test_ketika_aktif_kasir_sembunyikan_produk_stok_nol(): void
    {
        $this->login();
        Pengaturan::set('fitur_stok', '1');
        $produk = $this->buatProduk(0);

        $response = $this->get(route('kasir.index'));
        $response->assertStatus(200);
        $response->assertDontSee($produk->kode_produk);
    }

    public function test_ketika_nonaktif_checkout_tidak_cek_dan_tidak_kurangi_stok(): void
    {
        $user = $this->login();
        Pengaturan::set('fitur_stok', '0');
        $produk = $this->buatProduk(5);

        $response = $this->post(route('kasir.checkout'), [
            'items' => [
                ['produk_id' => $produk->id, 'satuan_id' => $produk->satuan_id, 'jumlah' => 999],
            ],
            'bayar' => 999 * 2000,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('transaksis', ['user_id' => $user->id]);
        $this->assertSame(5, $produk->fresh()->stok);
    }

    public function test_ketika_aktif_checkout_diblokir_jika_stok_kurang(): void
    {
        $this->login();
        Pengaturan::set('fitur_stok', '1');
        $produk = $this->buatProduk(5);

        $response = $this->post(route('kasir.checkout'), [
            'items' => [
                ['produk_id' => $produk->id, 'satuan_id' => $produk->satuan_id, 'jumlah' => 10],
            ],
            'bayar' => 10 * 2000,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertSame(5, $produk->fresh()->stok);
    }

    public function test_ketika_aktif_checkout_mengurangi_stok(): void
    {
        $this->login();
        Pengaturan::set('fitur_stok', '1');
        $produk = $this->buatProduk(5);

        $response = $this->post(route('kasir.checkout'), [
            'items' => [
                ['produk_id' => $produk->id, 'satuan_id' => $produk->satuan_id, 'jumlah' => 2],
            ],
            'bayar' => 2 * 2000,
        ]);

        $response->assertRedirect();
        $this->assertSame(3, $produk->fresh()->stok);
    }

    public function test_ketika_nonaktif_stock_opname_dialihkan_ke_dashboard(): void
    {
        $this->login();
        Pengaturan::set('fitur_stok', '0');

        foreach ([['GET', route('stock-opname.index')], ['GET', route('stock-opname.create')]] as [$method, $uri]) {
            $response = $this->{$method === 'GET' ? 'get' : 'post'}($uri);
            $response->assertRedirect(route('dashboard'));
            $response->assertSessionHas('error');
        }
    }

    public function test_ketika_nonaktif_validasi_stok_produk_opsional(): void
    {
        $this->login();
        Pengaturan::set('fitur_stok', '0');
        $kategori = Kategori::create(['nama_kategori' => 'Kat']);
        $satuan = Satuan::where('nama_satuan', 'pcs')->firstOrFail();

        $response = $this->post(route('produk.store'), [
            'kategori_id' => $kategori->id,
            'kode_produk' => 'KODE-TANPA-STOK',
            'nama_produk' => 'Produk Tanpa Input Stok',
            'harga_beli' => 500,
            'satuan_tiers' => [
                ['satuan_id' => $satuan->id, 'konversi' => null, 'harga_jual' => 1500],
            ],
        ]);

        $response->assertRedirect(route('produk.index'));
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('produks', [
            'kode_produk' => 'KODE-TANPA-STOK',
            'stok' => 0,
        ]);
    }
}

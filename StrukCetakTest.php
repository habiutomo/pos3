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

class StrukCetakTest extends TestCase
{
    use RefreshDatabase;

    private function buatTransaksi(): Transaksi
    {
        $this->actingAs(User::factory()->create());
        $kategori = Kategori::create(['nama_kategori' => 'Kat']);
        $satuan = Satuan::where('nama_satuan', 'pcs')->firstOrFail();
        $produk = Produk::create([
            'kategori_id' => $kategori->id,
            'kode_produk' => 'KD-STRUK',
            'nama_produk' => 'Produk Struk',
            'stok' => 10,
            'harga_beli' => 1000,
            'harga_jual' => 2000,
            'satuan_id' => $satuan->id,
        ]);

        $this->post(route('kasir.checkout'), [
            'items' => [
                ['produk_id' => $produk->id, 'satuan_id' => $satuan->id, 'jumlah' => 2],
            ],
            'bayar' => 10000,
        ]);

        return Transaksi::latest('id')->firstOrFail();
    }

    public function test_struk_default_memakai_lebar_otomatis(): void
    {
        $transaksi = $this->buatTransaksi();

        $response = $this->get(route('kasir.struk', $transaksi->id));
        $response->assertStatus(200);
        $response->assertSee('size: auto', false);
        $response->assertSee('4mm', false);
        $response->assertSee('struk-wrap', false);
        $response->assertSee('2 pcs x 2.000');
        $response->assertSee('4.000');
    }

    public function test_struk_ukuran_tetap_tetap_berfungsi(): void
    {
        Pengaturan::set('ukuran_kertas', '58');
        Pengaturan::set('panjang_kertas', '150');
        $transaksi = $this->buatTransaksi();

        $response = $this->get(route('kasir.struk', $transaksi->id));
        $response->assertStatus(200);
        $response->assertSee('size: 58mm 150mm', false);
        $response->assertSee('32ch', false);
    }

    public function test_struk_kertas_35mm_presisi_penuh(): void
    {
        Pengaturan::set('ukuran_kertas', '35');
        Pengaturan::set('panjang_kertas', 'auto');
        $transaksi = $this->buatTransaksi();

        $response = $this->get(route('kasir.struk', $transaksi->id));
        $response->assertStatus(200);
        $response->assertSee('size: 35mm 297mm', false);
        $response->assertSee('margin: 2mm 1mm', false);
        $response->assertSee('width: 33mm', false);
        $response->assertSee('4mm', false);
        $response->assertSee('overflow-wrap: anywhere', false);
        $response->assertSee('flex-wrap: wrap', false);
    }

    public function test_struk_kertas_58mm_presisi_penuh(): void
    {
        Pengaturan::set('ukuran_kertas', '58p');
        Pengaturan::set('panjang_kertas', '150');
        $transaksi = $this->buatTransaksi();

        $response = $this->get(route('kasir.struk', $transaksi->id));
        $response->assertStatus(200);
        $response->assertSee('size: 58mm 150mm', false);
        $response->assertSee('margin: 2mm 1mm', false);
        $response->assertSee('width: 56mm', false);
        $response->assertSee('4mm', false);
    }

    public function test_struk_pos_58_optimal_penuh(): void
    {
        Pengaturan::set('ukuran_kertas', '58');
        Pengaturan::set('panjang_kertas', '150');
        $transaksi = $this->buatTransaksi();

        $response = $this->get(route('kasir.struk', $transaksi->id));
        $response->assertStatus(200);
        $response->assertSee('monospace', false);
        $response->assertSee('font-size: 4mm', false);
        $response->assertSee('width: 32ch !important', false);
        $response->assertSee('margin: 2mm 3mm', false);
        $response->assertSee('margin-left: auto !important', false);
        $response->assertSee('color: #000', false);
    }

    public function test_struk_mode_32_kolom_memakai_monospace_dan_lebar_32ch(): void
    {
        Pengaturan::set('ukuran_kertas', '32');
        Pengaturan::set('panjang_kertas', '150');
        $transaksi = $this->buatTransaksi();

        $response = $this->get(route('kasir.struk', $transaksi->id));
        $response->assertStatus(200);
        $response->assertSee('width: 32ch', false);
        $response->assertSee('monospace', false);
        $response->assertSee('size: 58mm 150mm', false);
    }

    public function test_struk_mode_40_kolom_memakai_monospace_dan_lebar_40ch(): void
    {
        Pengaturan::set('ukuran_kertas', '40');
        Pengaturan::set('panjang_kertas', '150');
        $transaksi = $this->buatTransaksi();

        $response = $this->get(route('kasir.struk', $transaksi->id));
        $response->assertStatus(200);
        $response->assertSee('width: 40ch', false);
        $response->assertSee('monospace', false);
        $response->assertSee('size: 72mm 150mm', false);
    }

    public function test_struk_mode_kolom_memakai_teks_terformat_penuh(): void
    {
        Pengaturan::set('ukuran_kertas', '58');
        Pengaturan::set('panjang_kertas', '150');
        $transaksi = $this->buatTransaksi();

        $response = $this->get(route('kasir.struk', $transaksi->id));
        $response->assertStatus(200);
        $response->assertSee('<pre class="struk-pre">', false);
        $response->assertSee(str_repeat('=', 32), false);
        $response->assertSee(str_repeat('-', 32), false);
        $response->assertSee('TOTAL', false);
        $response->assertSee('Kembalian', false);
        $response->assertSee(str_repeat(' ', intdiv(32 - strlen('Produk Struk'), 2)).'Produk Struk', false);

        $this->assertMatchesRegularExpression('/<pre class="struk-pre">(.*?)<\/pre>/s', $response->getContent());
        preg_match('/<pre class="struk-pre">(.*?)<\/pre>/s', $response->getContent(), $cocok);
        foreach (explode("\n", html_entity_decode($cocok[1], ENT_QUOTES, 'UTF-8')) as $baris) {
            $this->assertLessThanOrEqual(32, mb_strlen(rtrim($baris)), 'Baris melebihi 32 karakter: '.$baris);
        }
    }

    public function test_halaman_pengaturan_menampilkan_opsi_auto(): void
    {
        $this->actingAs(User::factory()->create());

        $response = $this->get(route('pengaturan.index'));
        $response->assertStatus(200);
        $response->assertSee('value="auto"', false);
        $response->assertSee('value="35"', false);
        $response->assertSee('value="58p"', false);
        $response->assertSee('value="32"', false);
        $response->assertSee('value="40"', false);
        $response->assertSee('Direkomendasikan');
    }
}

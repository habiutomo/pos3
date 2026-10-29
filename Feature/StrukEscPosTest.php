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

class StrukEscPosTest extends TestCase
{
    use RefreshDatabase;

    private function buatTransaksi(): Transaksi
    {
        $this->actingAs(User::factory()->create());
        $kategori = Kategori::create(['nama_kategori' => 'Kat']);
        $satuan = Satuan::where('nama_satuan', 'pcs')->firstOrFail();
        $produk = Produk::create([
            'kategori_id' => $kategori->id,
            'kode_produk' => 'KD-BIN',
            'nama_produk' => 'Produk Bin',
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

    public function test_mengunduh_struk_escpos_huruf_besar_32_kolom(): void
    {
        Pengaturan::set('ukuran_kertas', '58');
        $transaksi = $this->buatTransaksi();

        $response = $this->get(route('kasir.struk.bin', $transaksi->id));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/octet-stream');

        $bin = $response->getContent();
        $this->assertStringStartsWith("\x1b@", $bin);
        $this->assertStringContainsString("\x1ba\x01\x1d!\x11", $bin);
        $this->assertStringContainsString($this->tengah('Tunai : 10.000', 16), $bin);
        $this->assertStringContainsString($this->tengah('Produk Bin', 16), $bin);
        $this->assertStringContainsString(str_repeat('=', 16) . "\n", $bin);
        $this->assertDoesNotMatchRegularExpression('/\x1ba\x00/', $bin);
        $this->assertStringEndsWith("\x1d!\x00\x1dV\x42\x00", $bin);

        foreach (explode("\n", $bin) as $baris) {
            $bersih = preg_replace('/[^\x20-\x7E]/', '', $baris);
            if ($bersih === '' || $bersih === null) {
                continue;
            }
            $isi = substr_count($baris, "\n") >= 0 ? rtrim($bersih) : $bersih;
            $this->assertLessThanOrEqual(16, strlen($isi), 'Baris melebihi kapasitas huruf besar: ' . $isi);
        }
    }

    public function test_escpos_mengikuti_ukuran_40_kolom(): void
    {
        Pengaturan::set('ukuran_kertas', '40');
        $transaksi = $this->buatTransaksi();

        $response = $this->get(route('kasir.struk.bin', $transaksi->id));
        $response->assertStatus(200);

        $bin = $response->getContent();
        $this->assertStringContainsString(str_repeat('=', 20) . "\n", $bin);
        $this->assertStringContainsString($this->tengah('Tunai : 10.000', 20), $bin);
        $this->assertStringContainsString($this->tengah('TOTAL : 4.000', 20), $bin);
    }

    public function test_escpos_mengikuti_ukuran_48_kolom(): void
    {
        Pengaturan::set('ukuran_kertas', '48');
        $transaksi = $this->buatTransaksi();

        $response = $this->get(route('kasir.struk.bin', $transaksi->id));
        $response->assertStatus(200);

        $bin = $response->getContent();
        $this->assertStringContainsString(str_repeat('=', 24) . "\n", $bin);
        $this->assertStringContainsString($this->tengah('Tunai : 10.000', 24), $bin);
        $this->assertStringContainsString($this->tengah('TOTAL : 4.000', 24), $bin);
    }

    private function tengah(string $teks, int $lebar): string
    {
        return str_repeat(' ', intdiv($lebar - strlen($teks), 2)) . $teks;
    }
}

<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Produk;
use App\Models\User;
use App\Services\Excel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ProductImportTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        Kategori::create(['nama_kategori' => 'Umum']);
    }

    public function test_halaman_import_tampil_dan_template_bisa_dibaca_kembali(): void
    {
        $this->actingAs($this->user)
            ->get(route('produk.import.form'))
            ->assertOk()
            ->assertSee('Import Produk dari Excel');

        $respon = $this->get(route('produk.import.template'));

        $respon->assertOk();
        $this->assertStringContainsString('spreadsheetml', (string) $respon->headers->get('Content-Type'));
        $this->assertSame("PK\x03\x04", substr($respon->getContent(), 0, 4));

        $tmp = tempnam(sys_get_temp_dir(), 'tpl').'.xlsx';
        file_put_contents($tmp, $respon->getContent());

        $rows = Excel::read($tmp);

        $this->assertSame([
            'kode_produk', 'nama_produk', 'kategori', 'satuan', 'barcode', 'stok', 'harga_beli', 'harga_jual',
        ], $rows[0]);
        $this->assertCount(3, $rows);
    }

    public function test_import_csv_membuat_produk_dan_kategori_satuan_baru(): void
    {
        $csv = "kode_produk;nama_produk;kategori;satuan;barcode;stok;harga_beli;harga_jual\n"
            ."PRD001;Keripik Pedas;Snack;pcs;;10;\"2.500\";3500\n";

        $this->actingAs($this->user)
            ->post(route('produk.import.store'), [
                'file_excel' => UploadedFile::fake()->createWithContent('produk.csv', $csv),
            ])
            ->assertRedirect()
            ->assertSessionHas('import_result.sukses', 1);

        $produk = Produk::where('kode_produk', 'PRD001')->first();

        $this->assertNotNull($produk);
        $this->assertSame('Keripik Pedas', $produk->nama_produk);
        $this->assertSame(2500.0, (float) $produk->harga_beli);
        $this->assertSame(3500.0, (float) $produk->harga_jual);
        $this->assertSame(10, $produk->stok);
        $this->assertSame('Snack', $produk->kategori->nama_kategori);
        $this->assertSame('pcs', $produk->satuan->nama_satuan);
    }

    public function test_import_xlsx_membuat_produk(): void
    {
        $isi = Excel::buildXlsx([
            ['kode_produk', 'nama_produk', 'kategori', 'satuan', 'barcode', 'stok', 'harga_beli', 'harga_jual'],
            ['PRD002', 'Teh Kotak', 'Minuman', 'dus', '', 5, '38.000', '45.000'],
            ['PRD003', 'Gula Pasir', 'Sembako', 'kg', '1234567890123', 20, 'Rp14.000', 'Rp16.500'],
        ]);

        $this->actingAs($this->user)
            ->post(route('produk.import.store'), [
                'file_excel' => UploadedFile::fake()->createWithContent('produk.xlsx', $isi),
            ])
            ->assertRedirect()
            ->assertSessionHas('import_result.sukses', 2);

        $this->assertDatabaseHas('produks', ['kode_produk' => 'PRD002']);
        $this->assertDatabaseHas('produks', ['kode_produk' => 'PRD003']);

        $gula = Produk::where('kode_produk', 'PRD003')->first();
        $this->assertSame(14000.0, (float) $gula->harga_beli);
        $this->assertSame('1234567890123', $gula->barcode);
    }

    public function test_baris_tidak_valid_dilaporkan_dan_baris_valid_tetap_masuk(): void
    {
        $isi = Excel::buildXlsx([
            ['kode_produk', 'nama_produk', 'kategori', 'satuan', 'barcode', 'stok', 'harga_beli', 'harga_jual'],
            ['PRD010', 'Produk Baik', 'Umum', 'pcs', '', 1, 1000, 2000],
            ['', 'Tanpa Kode', 'Umum', 'pcs', '', 1, 1000, 2000],
            ['PRD011', 'Harga Rusak', 'Umum', 'pcs', '', 1, 'abc', 2000],
            ['PRD012', 'Duplikat Di File', 'Umum', 'pcs', '', 1, 1000, 2000],
            ['PRD012', 'Duplikat Lagi', 'Umum', 'pcs', '', 1, 1000, 2000],
        ]);

        $respon = $this->actingAs($this->user)
            ->post(route('produk.import.store'), [
                'file_excel' => UploadedFile::fake()->createWithContent('produk.xlsx', $isi),
            ]);

        $respon->assertRedirect()->assertSessionHas('import_result.gagal', 3);

        $hasil = $respon->getSession()->get('import_result');

        $this->assertSame(2, $hasil['sukses']);
        $this->assertTrue(collect($hasil['errors'])->contains(fn ($e) => str_contains($e, 'Baris 3')));
        $this->assertTrue(collect($hasil['errors'])->contains(fn ($e) => str_contains($e, 'Baris 4')));
        $this->assertTrue(collect($hasil['errors'])->contains(fn ($e) => str_contains($e, 'Baris 6')));

        $this->assertDatabaseHas('produks', ['kode_produk' => 'PRD010']);
        $this->assertDatabaseMissing('produks', ['kode_produk' => 'PRD011']);
        $this->assertSame(1, Produk::where('kode_produk', 'PRD012')->count());
        $this->assertSame('Duplikat Di File', Produk::where('kode_produk', 'PRD012')->value('nama_produk'));
    }

    public function test_kode_sudah_ada_dilewati_kecuali_mode_perbarui(): void
    {
        Produk::create([
            'kategori_id' => 1,
            'satuan_id' => 1,
            'kode_produk' => 'PRD020',
            'nama_produk' => 'Nama Lama',
            'stok' => 1,
            'harga_beli' => 1000,
            'harga_jual' => 1500,
        ]);

        $baris = [
            ['kode_produk', 'nama_produk', 'kategori', 'satuan', 'barcode', 'stok', 'harga_beli', 'harga_jual'],
            ['PRD020', 'Nama Baru', 'Umum', 'pcs', '', 9, 1200, 2500],
        ];

        $upload = fn () => UploadedFile::fake()->createWithContent('p.xlsx', Excel::buildXlsx($baris));

        $this->actingAs($this->user)
            ->post(route('produk.import.store'), ['file_excel' => $upload()])
            ->assertRedirect()
            ->assertSessionHas('import_result.sukses', 0)
            ->assertSessionHas('import_result.dilewati', 1);

        $this->assertSame('Nama Lama', Produk::where('kode_produk', 'PRD020')->value('nama_produk'));

        $this->post(route('produk.import.store'), ['file_excel' => $upload(), 'perbarui_yang_ada' => '1'])
            ->assertRedirect()
            ->assertSessionHas('import_result.sukses', 1);

        $produk = Produk::where('kode_produk', 'PRD020')->first();
        $this->assertSame('Nama Baru', $produk->nama_produk);
        $this->assertSame(9, $produk->stok);
        $this->assertSame(2500.0, (float) $produk->harga_jual);
    }

    public function test_header_tidak_lengkap_ditolak(): void
    {
        $isi = Excel::buildXlsx([
            ['kode', 'nama barang'],
            ['PRD099', 'Produk Tanpa Harga'],
        ]);

        $respon = $this->actingAs($this->user)
            ->post(route('produk.import.store'), [
                'file_excel' => UploadedFile::fake()->createWithContent('rusak.xlsx', $isi),
            ]);

        $respon->assertRedirect()
            ->assertSessionHas('import_result.total', 0);

        $hasil = $respon->getSession()->get('import_result');

        $this->assertTrue(str_contains($hasil['errors'][0], 'Kolom wajib'));
        $this->assertDatabaseMissing('produks', ['kode_produk' => 'PRD099']);
    }

    public function test_ekstensi_selain_xlsx_csv_ditolak(): void
    {
        $this->actingAs($this->user)
            ->post(route('produk.import.store'), [
                'file_excel' => UploadedFile::fake()->createWithContent('produk.xls', 'binary'),
            ])
            ->assertRedirect()
            ->assertSessionHas('error');
    }
}

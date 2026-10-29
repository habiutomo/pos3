<?php

namespace App\Services;

use App\Models\Kategori;
use App\Models\Pengaturan;
use App\Models\Produk;
use App\Models\Satuan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductImportService
{
    private const ALIAS = [
        'kode_produk' => ['kode produk', 'kode', 'kode barang'],
        'nama_produk' => ['nama produk', 'nama', 'nama barang'],
        'kategori' => ['kategori', 'nama kategori', 'kategori produk'],
        'satuan' => ['satuan', 'nama satuan'],
        'barcode' => ['barcode', 'bar code'],
        'stok' => ['stok', 'stock', 'jumlah stok'],
        'harga_beli' => ['harga beli', 'harga pembelian', 'hb'],
        'harga_jual' => ['harga jual', 'hj'],
    ];

    private const KOLOM_WAJIB = ['kode_produk', 'nama_produk', 'kategori', 'satuan', 'harga_beli', 'harga_jual'];

    public static function headersTemplate(): array
    {
        return array_keys(self::ALIAS);
    }

    public static function daftarKolom(): array
    {
        return [
            ['kolom' => 'kode_produk', 'wajib' => true, 'ket' => 'Kode unik tiap produk, maksimal 50 karakter. Contoh: PRD001'],
            ['kolom' => 'nama_produk', 'wajib' => true, 'ket' => 'Nama produk, maksimal 255 karakter.'],
            ['kolom' => 'kategori', 'wajib' => true, 'ket' => 'Nama kategori. Dibuat otomatis jika belum ada.'],
            ['kolom' => 'satuan', 'wajib' => true, 'ket' => 'Satuan utama (pcs, dus, box, dll). Dibuat otomatis jika belum ada.'],
            ['kolom' => 'barcode', 'wajib' => false, 'ket' => 'Kode barcode, maksimal 50 karakter dan harus unik.'],
            ['kolom' => 'stok', 'wajib' => false, 'ket' => 'Angka >= 0. Jika dikosongkan dianggap 0.'],
            ['kolom' => 'harga_beli', 'wajib' => true, 'ket' => 'Angka >= 0. Boleh ditulis 2500 / 2.500 / Rp2.500.'],
            ['kolom' => 'harga_jual', 'wajib' => true, 'ket' => 'Harga jual per satuan utama. Format sama dengan harga beli.'],
        ];
    }

    public function import(array $rows, bool $perbaruiYangAda = false): array
    {
        $hasil = ['total' => 0, 'sukses' => 0, 'gagal' => 0, 'dilewati' => 0, 'errors' => [], 'catatan' => []];

        if (empty($rows)) {
            $hasil['errors'][] = 'File tidak berisi data apa pun.';

            return $hasil;
        }

        $colMap = $this->petakanHeader($rows[0]);

        if ($colMap === null) {
            $hasil['errors'][] = 'Header kolom tidak dikenali. Gunakan template: '
                .implode(', ', self::headersTemplate());

            return $hasil;
        }

        $kurang = array_diff(self::KOLOM_WAJIB, $colMap);

        if (! empty($kurang)) {
            $hasil['errors'][] = 'Kolom wajib tidak ditemukan: '.implode(', ', $kurang)
                .'. Pastikan baris pertama berisi nama kolom.';

            return $hasil;
        }

        [$items, $errors] = $this->validasiBaris($rows, $colMap);

        if (! empty($items)) {
            $this->siapkanReferensi($items);
            [$hasil['sukses'], $hasil['dilewati'], $catatan] = $this->simpan($items, $perbaruiYangAda);
            $hasil['catatan'] = $catatan;
        }

        $hasil['total'] = count($rows) - 1;
        $hasil['gagal'] = count($errors);
        $hasil['errors'] = array_slice($errors, 0, 100);

        if (count($errors) > 100) {
            $hasil['errors'][] = '... dan '.(count($errors) - 100).' kesalahan lainnya.';
        }

        return $hasil;
    }

    private function petakanHeader(array $header): ?array
    {
        $map = [];

        foreach ($header as $idx => $raw) {
            $normal = self::normalisasi((string) $raw);

            if ($normal === '') {
                continue;
            }

            foreach (self::ALIAS as $field => $alias) {
                if ($normal === $field || in_array($normal, $alias, true)) {
                    $map[$idx] = $field;
                    break;
                }
            }
        }

        return empty($map) ? null : $map;
    }

    private static function normalisasi(string $text): string
    {
        return trim(mb_strtolower(preg_replace('/[\s_]+/u', ' ', trim($text)) ?? ''));
    }

    private function validasiBaris(array $rows, array $colMap): array
    {
        $items = [];
        $errors = [];
        $stokAktif = Pengaturan::stokAktif();
        $kodeDilihat = [];
        $barcodeDilihat = [];

        foreach (array_slice($rows, 1) as $i => $row) {
            $no = $i + 2;
            $data = [];

            foreach ($colMap as $idx => $field) {
                $data[$field] = isset($row[$idx]) ? trim((string) $row[$idx]) : '';
            }

            if (implode('', array_intersect_key($data, array_flip(self::KOLOM_WAJIB))) === '') {
                continue;
            }

            $rowErrors = [];
            $kodeLower = mb_strtolower($data['kode_produk']);

            if ($data['kode_produk'] === '') {
                $rowErrors[] = 'kode produk wajib diisi';
            } elseif (mb_strlen($data['kode_produk']) > 50) {
                $rowErrors[] = 'kode produk maksimal 50 karakter';
            } elseif (isset($kodeDilihat[$kodeLower])) {
                $rowErrors[] = "kode produk \"{$data['kode_produk']}\" duplikat di file (baris {$kodeDilihat[$kodeLower]})";
            } else {
                $kodeDilihat[$kodeLower] = $no;
            }

            if ($data['nama_produk'] === '') {
                $rowErrors[] = 'nama produk wajib diisi';
            } elseif (mb_strlen($data['nama_produk']) > 255) {
                $rowErrors[] = 'nama produk maksimal 255 karakter';
            }

            if ($data['kategori'] === '') {
                $rowErrors[] = 'kategori wajib diisi';
            } elseif (mb_strlen($data['kategori']) > 255) {
                $rowErrors[] = 'nama kategori maksimal 255 karakter';
            }

            if ($data['satuan'] === '') {
                $rowErrors[] = 'satuan wajib diisi';
            }

            $barcodeLower = null;

            if ($data['barcode'] !== '') {
                if (mb_strlen($data['barcode']) > 50) {
                    $rowErrors[] = 'barcode maksimal 50 karakter';
                } else {
                    $barcodeLower = mb_strtolower($data['barcode']);

                    if (isset($barcodeDilihat[$barcodeLower])) {
                        $rowErrors[] = "barcode \"{$data['barcode']}\" duplikat di file (baris {$barcodeDilihat[$barcodeLower]})";
                    } else {
                        $barcodeDilihat[$barcodeLower] = $no;
                    }
                }
            }

            if ($stokAktif && $data['stok'] !== '') {
                if (! preg_match('/^\d+$/', $data['stok'])) {
                    $rowErrors[] = 'stok harus berupa angka bulat >= 0';
                }
            }

            foreach (['harga_beli', 'harga_jual'] as $kolHarga) {
                if ($data[$kolHarga] === '') {
                    $rowErrors[] = str_replace('_', ' ', $kolHarga).' wajib diisi';

                    continue;
                }

                $nilai = self::parseNominal($data[$kolHarga]);

                if ($nilai === null || $nilai < 0) {
                    $rowErrors[] = str_replace('_', ' ', $kolHarga)." \"{$data[$kolHarga]}\" bukan angka yang valid";
                }
            }

            if (! empty($rowErrors)) {
                $errors[] = "Baris {$no}: ".implode(', ', $rowErrors);

                continue;
            }

            $items[] = [
                'baris' => $no,
                'kode_produk' => $data['kode_produk'],
                'nama_produk' => $data['nama_produk'],
                'kategori' => $data['kategori'],
                'satuan' => Str::limit(mb_strtolower($data['satuan']), 20, ''),
                'barcode' => $data['barcode'] !== '' ? $data['barcode'] : null,
                'stok' => $stokAktif ? max(0, (int) ($data['stok'] !== '' ? $data['stok'] : 0)) : 0,
                'harga_beli' => (string) self::parseNominal($data['harga_beli']),
                'harga_jual' => (string) self::parseNominal($data['harga_jual']),
            ];
        }

        return [$items, $errors];
    }

    private function siapkanReferensi(array &$items): void
    {
        $kategoris = Kategori::all()->keyBy(fn ($k) => mb_strtolower(trim($k->nama_kategori)));
        $satuans = Satuan::all()->keyBy(fn ($s) => mb_strtolower(trim($s->nama_satuan)));

        foreach ($items as &$item) {
            $katKey = mb_strtolower(trim($item['kategori']));

            if (! isset($kategoris[$katKey])) {
                $baru = Kategori::create(['nama_kategori' => trim($item['kategori'])]);
                $kategoris[$katKey] = $baru;
            }

            $item['kategori_id'] = $kategoris[$katKey]->id;

            if (! isset($satuans[$item['satuan']])) {
                $baru = Satuan::create(['nama_satuan' => $item['satuan']]);
                $satuans[$item['satuan']] = $baru;
            }

            $item['satuan_id'] = $satuans[$item['satuan']]->id;
        }

        unset($item);
    }

    private function simpan(array $items, bool $perbaruiYangAda): array
    {
        $existing = Produk::whereIn('kode_produk', collect($items)->pluck('kode_produk')->unique()->all())
            ->get()
            ->keyBy(fn ($p) => mb_strtolower($p->kode_produk));

        $barcodes = collect($items)->pluck('barcode')->filter()->unique()->all();

        $existingBarcode = empty($barcodes) ? collect() : Produk::whereIn('barcode', $barcodes)
            ->get()
            ->keyBy(fn ($p) => mb_strtolower((string) $p->barcode));

        $ops = [];

        foreach ($items as $item) {
            $ada = $existing->get(mb_strtolower($item['kode_produk']));
            $bentrokBarcode = $item['barcode'] !== null && $existingBarcode->has(mb_strtolower($item['barcode']))
                && (! $ada || (int) $existingBarcode->get(mb_strtolower($item['barcode']))->id !== (int) $ada->id);

            if ($bentrokBarcode) {
                $ops[] = ['mode' => 'skip', 'baris' => $item['baris'], 'alasan' => 'barcode sudah dipakai produk lain'];

                continue;
            }

            if ($ada && ! $perbaruiYangAda) {
                $ops[] = ['mode' => 'skip', 'baris' => $item['baris'], 'alasan' => "kode \"{$item['kode_produk']}\" sudah terdaftar"];

                continue;
            }

            $payload = [
                'kategori_id' => $item['kategori_id'],
                'satuan_id' => $item['satuan_id'],
                'kode_produk' => $item['kode_produk'],
                'barcode' => $item['barcode'],
                'nama_produk' => $item['nama_produk'],
                'stok' => $item['stok'],
                'harga_beli' => $item['harga_beli'],
                'harga_jual' => $item['harga_jual'],
            ];

            $ops[] = $ada ? ['mode' => 'update', 'produk' => $ada, 'data' => $payload] : ['mode' => 'create', 'data' => $payload];
        }

        DB::transaction(function () use ($ops) {
            foreach ($ops as $op) {
                if ($op['mode'] === 'create') {
                    Produk::create($op['data']);
                } elseif ($op['mode'] === 'update') {
                    $op['produk']->update($op['data']);
                }
            }
        });

        $catatan = array_map(
            fn ($op) => "Baris {$op['baris']}: dilewati ({$op['alasan']}).",
            array_filter($ops, fn ($op) => $op['mode'] === 'skip')
        );

        return [count($ops) - count($catatan), count($catatan), array_values($catatan)];
    }

    public static function parseNominal(?string $raw): ?float
    {
        if ($raw === null) {
            return null;
        }

        $s = trim($raw);

        if ($s === '') {
            return null;
        }

        $s = preg_replace('/rp(?![a-z])/i', '', $s) ?? '';
        $s = str_replace([' ', "\u{00A0}"], '', $s);

        if ($s === '' || ! preg_match('/^[\d.,]+$/', $s)) {
            return null;
        }

        $punyaKoma = str_contains($s, ',');
        $punyaTitik = str_contains($s, '.');

        if ($punyaKoma && $punyaTitik) {
            $s = str_replace('.', '', $s);
            $s = str_replace(',', '.', $s);
        } elseif ($punyaKoma) {
            $s = preg_match('/^\d{1,3}(,\d{3})+$/', $s) ? str_replace(',', '', $s) : str_replace(',', '.', $s);
        } elseif ($punyaTitik) {
            if (preg_match('/^\d{1,3}(\.\d{3})+$/', $s)) {
                $s = str_replace('.', '', $s);
            }
        }

        return is_numeric($s) ? (float) $s : null;
    }
}

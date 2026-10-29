<?php

namespace App\Services;

use App\Models\Pengaturan;
use App\Models\Transaksi;

class StrukEscPos
{
    protected int $kolomPrinter;

    public function __construct()
    {
        $this->kolomPrinter = match (Pengaturan::get('ukuran_kertas', 'auto')) {
            '40' => 40,
            '48' => 48,
            default => 32,
        };
    }

    public function buat(Transaksi $transaksi): string
    {
        $lebar = intdiv($this->kolomPrinter, 2);
        $besar = "\x1d!\x11";
        $toko = Pengaturan::toko();
        $rp = fn ($n) => number_format((float) $n, 0, ',', '.');

        $pecahKata = function (string $teks) use ($lebar): array {
            $hasil = [];
            foreach (preg_split('/\s+/', trim($teks)) ?: [] as $kata) {
                while (strlen($kata) > $lebar) {
                    $hasil[] = substr($kata, 0, $lebar);
                    $kata = substr($kata, $lebar);
                }
                if ($kata !== '') {
                    $hasil[] = $kata;
                }
            }

            return $hasil;
        };

        $bungkus = function (string $teks) use ($lebar, $pecahKata): array {
            $baris = [];
            $aktif = '';
            foreach ($pecahKata($teks) as $kata) {
                if ($aktif === '') {
                    $aktif = $kata;
                } elseif (strlen($aktif) + 1 + strlen($kata) <= $lebar) {
                    $aktif .= ' '.$kata;
                } else {
                    $baris[] = $aktif;
                    $aktif = $kata;
                }
            }
            if ($aktif !== '') {
                $baris[] = $aktif;
            }

            return $baris;
        };

        $tengah = function (string $teks) use ($lebar): string {
            $teks = rtrim($teks);
            if (strlen($teks) >= $lebar) {
                return $teks;
            }
            $sisa = $lebar - strlen($teks);

            return str_repeat(' ', intdiv($sisa, 2)).$teks;
        };

        $bungkusTengah = fn (string $teks) => array_map($tengah, $bungkus($teks));

        $garisTipis = str_repeat('-', $lebar);
        $garisTebal = str_repeat('=', $lebar);

        $out = "\x1b@";
        $out .= "\x1ba\x01".$besar;

        foreach ($bungkusTengah($this->teks(rtrim(mb_strtoupper(trim($toko['nama_toko']))))) as $b) {
            $out .= $b."\n";
        }
        if ($toko['alamat_toko']) {
            foreach ($bungkusTengah($this->teks($toko['alamat_toko'])) as $b) {
                $out .= $b."\n";
            }
        }
        if ($toko['telp_toko']) {
            foreach ($bungkusTengah($this->teks('Telp: '.$toko['telp_toko'])) as $b) {
                $out .= $b."\n";
            }
        }

        $out .= $garisTebal."\n";
        foreach ($bungkusTengah('No : '.$this->teks($transaksi->no_transaksi)) as $b) {
            $out .= $b."\n";
        }
        foreach ($bungkusTengah('Tanggal : '.$transaksi->created_at->format('d/m/Y H:i')) as $b) {
            $out .= $b."\n";
        }
        foreach ($bungkusTengah('Kasir : '.$this->teks($transaksi->user->name)) as $b) {
            $out .= $b."\n";
        }
        $out .= $garisTipis."\n";

        foreach ($transaksi->detailTransaksis as $detail) {
            foreach ($bungkusTengah($this->teks($detail->produk->nama_produk)) as $b) {
                $out .= $b."\n";
            }
            foreach ($bungkusTengah(
                $detail->jumlah.($detail->satuan_jual ? ' '.$this->teks($detail->satuan_jual) : '').' x '.$rp($detail->harga_satuan).' : '.$rp($detail->subtotal)
            ) as $b) {
                $out .= $b."\n";
            }
        }

        $out .= $garisTipis."\n";
        $out .= $tengah('TOTAL : '.$rp($transaksi->total_harga))."\n";
        $out .= $tengah('Tunai : '.$rp($transaksi->bayar))."\n";
        $out .= $tengah('Kembalian : '.$rp($transaksi->kembalian))."\n";
        $out .= $garisTebal."\n";

        foreach ($bungkusTengah($this->teks('Terima kasih atas kunjungan Anda!')) as $b) {
            $out .= $b."\n";
        }

        $out .= "\n\n\n\n";
        $out .= "\x1d!\x00\x1dV\x42\x00";

        return $out;
    }

    private function teks(string $nilai): string
    {
        $hasil = false;
        if (function_exists('iconv')) {
            $hasil = @iconv('UTF-8', 'CP437//TRANSLIT', $nilai);
            if ($hasil === false) {
                $hasil = @iconv('UTF-8', 'ASCII//TRANSLIT', $nilai);
            }
        }
        if (! is_string($hasil)) {
            $hasil = $nilai;
        }

        return preg_replace('/[^\x20-\x7E]/', '?', $hasil);
    }
}

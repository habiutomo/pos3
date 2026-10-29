<?php

namespace App\Http\Controllers;

use App\Models\DetailTransaksi;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RekapController extends Controller
{
    private function periode(Request $request): array
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        if (strtotime($startDate) > strtotime($endDate)) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        return [$startDate, $endDate];
    }

    private function dataRekap(string $startDate, string $endDate): array
    {
        $trxQuery = Transaksi::whereBetween(DB::raw('date(created_at)'), [$startDate, $endDate]);

        $totalOmzet = (clone $trxQuery)->sum('total_harga');
        $jumlahTransaksi = (clone $trxQuery)->count();
        $jumlahHari = max(1, (strtotime($endDate) - strtotime($startDate)) / 86400 + 1);

        $labaKotor = DetailTransaksi::join('transaksis', 'transaksis.id', '=', 'detail_transaksis.transaksi_id')
            ->join('produks', 'produks.id', '=', 'detail_transaksis.produk_id')
            ->whereBetween(DB::raw('date(transaksis.created_at)'), [$startDate, $endDate])
            ->selectRaw('COALESCE(SUM((detail_transaksis.harga_satuan - produks.harga_beli) * detail_transaksis.jumlah), 0) as laba')
            ->value('laba');

        $perHari = Transaksi::query()
            ->selectRaw("DATE(created_at) as tanggal, COUNT(*) as jumlah_transaksi, COALESCE(SUM(total_harga), 0) as omzet")
            ->whereBetween(DB::raw('date(created_at)'), [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('tanggal')
            ->get();

        $perKasir = Transaksi::query()
            ->join('users', 'users.id', '=', 'transaksis.user_id')
            ->selectRaw('users.name, COUNT(*) as jumlah_transaksi, COALESCE(SUM(transaksis.total_harga), 0) as omzet')
            ->whereBetween(DB::raw('date(transaksis.created_at)'), [$startDate, $endDate])
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('omzet')
            ->get();

        $produkTerjual = DetailTransaksi::query()
            ->join('transaksis', 'transaksis.id', '=', 'detail_transaksis.transaksi_id')
            ->join('produks', 'produks.id', '=', 'detail_transaksis.produk_id')
            ->whereBetween(DB::raw('date(transaksis.created_at)'), [$startDate, $endDate])
            ->selectRaw('produks.kode_produk, produks.nama_produk, SUM(detail_transaksis.jumlah) as total_terjual, COALESCE(SUM(detail_transaksis.subtotal), 0) as omzet, COALESCE(SUM((detail_transaksis.harga_satuan - produks.harga_beli) * detail_transaksis.jumlah), 0) as laba')
            ->groupBy('produks.id', 'produks.kode_produk', 'produks.nama_produk')
            ->orderByDesc('total_terjual')
            ->limit(10)
            ->get();

        return [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalOmzet' => $totalOmzet,
            'jumlahTransaksi' => $jumlahTransaksi,
            'rataHarian' => $totalOmzet / $jumlahHari,
            'labaKotor' => $labaKotor,
            'perHari' => $perHari,
            'perKasir' => $perKasir,
            'produkTerjual' => $produkTerjual,
        ];
    }

    public function index(Request $request)
    {
        [$startDate, $endDate] = $this->periode($request);
        $data = $this->dataRekap($startDate, $endDate);

        return view('rekap.index', $data);
    }

    public function print(Request $request)
    {
        [$startDate, $endDate] = $this->periode($request);
        $data = $this->dataRekap($startDate, $endDate);

        return view('rekap.print', $data);
    }

    public function export(Request $request)
    {
        [$startDate, $endDate] = $this->periode($request);
        $data = $this->dataRekap($startDate, $endDate);

        $filename = "rekap-{$startDate}_sd_{$endDate}.csv";

        return response()->streamDownload(function () use ($data) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($out, ['REKAP PENJUALAN']);
            fputcsv($out, ['Periode', $data['startDate'] . ' s/d ' . $data['endDate']]);
            fputcsv($out, []);
            fputcsv($out, ['Ringkasan']);
            fputcsv($out, ['Total Omzet', $data['totalOmzet']]);
            fputcsv($out, ['Laba Kotor', $data['labaKotor']]);
            fputcsv($out, ['Jumlah Transaksi', $data['jumlahTransaksi']]);
            fputcsv($out, ['Rata-rata Harian', round($data['rataHarian'], 2)]);
            fputcsv($out, []);

            fputcsv($out, ['Rekap Per Hari']);
            fputcsv($out, ['Tanggal', 'Jumlah Transaksi', 'Omzet']);
            foreach ($data['perHari'] as $h) {
                fputcsv($out, [$h->tanggal, $h->jumlah_transaksi, $h->omzet]);
            }
            fputcsv($out, []);

            fputcsv($out, ['Rekap Per Kasir']);
            fputcsv($out, ['Kasir', 'Jumlah Transaksi', 'Omzet']);
            foreach ($data['perKasir'] as $k) {
                fputcsv($out, [$k->name, $k->jumlah_transaksi, $k->omzet]);
            }
            fputcsv($out, []);

            fputcsv($out, ['10 Produk Terlaris']);
            fputcsv($out, ['Kode', 'Nama Produk', 'Total Terjual', 'Omzet', 'Laba']);
            foreach ($data['produkTerjual'] as $p) {
                fputcsv($out, [$p->kode_produk, $p->nama_produk, $p->total_terjual, $p->omzet, $p->laba]);
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\DetailTransaksi;
use App\Models\Pengaturan;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Services\StrukEscPos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaksi::with('user');

        if ($request->filled('search')) {
            $query->where('no_transaksi', 'like', "%{$request->search}%");
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $transaksis = $query->latest()->paginate(15);

        return view('kasir.index', compact('transaksis'));
    }

    public function kasir()
    {
        $query = Produk::with(['kategori', 'satuan', 'produkSatuans.satuan']);

        if (Pengaturan::stokAktif()) {
            $query->where('stok', '>', 0);
        }

        $produks = $query->get();

        return view('kasir.kasir', compact('produks'));
    }

    public function getProduk($id)
    {
        $produk = Produk::with(['kategori', 'satuan', 'produkSatuans.satuan'])->find($id);
        if (! $produk) {
            return response()->json(['error' => 'Produk tidak ditemukan'], 404);
        }

        return response()->json($produk);
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.produk_id' => 'required|exists:produks,id',
            'items.*.satuan_id' => 'required|exists:satuans,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'bayar' => 'required|numeric|min:0',
        ]);

        $totalHarga = 0;
        $items = [];

        foreach ($request->items as $item) {
            $produk = Produk::with(['satuan', 'produkSatuans'])->findOrFail($item['produk_id']);

            if ((int) $item['satuan_id'] === (int) $produk->satuan_id) {
                $konversi = 1;
                $hargaSatuan = $produk->harga_jual;
                $namaSatuan = $produk->satuan->nama_satuan;
            } else {
                $tier = $produk->produkSatuans->firstWhere('satuan_id', (int) $item['satuan_id']);
                if (! $tier) {
                    return back()->withInput()->with('error', "Satuan jual tidak valid untuk {$produk->nama_produk}");
                }
                $konversi = $tier->konversi;
                $hargaSatuan = $tier->harga_jual;
                $namaSatuan = $tier->satuan->nama_satuan;
            }

            $jumlahDasar = $item['jumlah'] * $konversi;

            if (Pengaturan::stokAktif() && $produk->stok < $jumlahDasar) {
                return back()->withInput()->with('error', "Stok {$produk->nama_produk} tidak mencukupi");
            }

            $subtotal = $hargaSatuan * $item['jumlah'];
            $totalHarga += $subtotal;

            $items[] = [
                'produk_id' => $produk->id,
                'jumlah' => $item['jumlah'],
                'jumlah_dasar' => $jumlahDasar,
                'satuan_jual' => $namaSatuan,
                'harga_satuan' => $hargaSatuan,
                'subtotal' => $subtotal,
            ];
        }

        if ($request->bayar < $totalHarga) {
            return back()->withInput()->with('error', 'Jumlah bayar kurang dari total harga');
        }

        $kembalian = $request->bayar - $totalHarga;
        $no_transaksi = 'TRX-'.date('Ymd').'-'.str_pad(Transaksi::count() + 1, 5, '0', STR_PAD_LEFT);

        DB::beginTransaction();

        try {
            $transaksi = Transaksi::create([
                'no_transaksi' => $no_transaksi,
                'user_id' => Auth::id(),
                'total_harga' => $totalHarga,
                'bayar' => $request->bayar,
                'kembalian' => $kembalian,
            ]);

            foreach ($items as $item) {
                DetailTransaksi::create([
                    'transaksi_id' => $transaksi->id,
                    'produk_id' => $item['produk_id'],
                    'jumlah' => $item['jumlah'],
                    'satuan_jual' => $item['satuan_jual'],
                    'harga_satuan' => $item['harga_satuan'],
                    'subtotal' => $item['subtotal'],
                ]);

                if (Pengaturan::stokAktif()) {
                    $produk = Produk::find($item['produk_id']);
                    $produk->decrement('stok', $item['jumlah_dasar']);
                }
            }

            DB::commit();

            return redirect()->route('kasir.struk', $transaksi->id)->with('success', 'Transaksi berhasil!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    public function struk($id)
    {
        $transaksi = Transaksi::with(['detailTransaksis.produk', 'user'])->findOrFail($id);

        return view('kasir.struk', compact('transaksi'));
    }

    public function strukBin($id)
    {
        $transaksi = Transaksi::with(['detailTransaksis.produk', 'user'])->findOrFail($id);
        $data = app(StrukEscPos::class)->buat($transaksi);

        return response($data, 200, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="struk-'.$transaksi->no_transaksi.'.bin"',
        ]);
    }

    public function destroy(Transaksi $transaksi)
    {
        DB::beginTransaction();

        try {
            if (Pengaturan::stokAktif()) {
                foreach ($transaksi->detailTransaksis as $detail) {
                    $detail->produk->increment('stok', $detail->jumlah);
                }
            }

            $transaksi->delete();

            DB::commit();

            return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal menghapus transaksi');
        }
    }
}

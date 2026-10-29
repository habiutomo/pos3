<?php

namespace App\Http\Controllers;

use App\Models\DetailTransaksi;
use App\Models\Pengaturan;
use App\Models\Retur;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReturController extends Controller
{
    public function index(Request $request)
    {
        $query = Retur::with(['transaksi', 'user']);

        if ($request->filled('search')) {
            $query->where('no_retur', 'like', "%{$request->search}%");
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $returs = $query->latest()->paginate(15);

        return view('retur.index', compact('returs'));
    }

    public function create(Request $request)
    {
        $transaksis = collect();

        if ($request->filled('search')) {
            $transaksis = Transaksi::with(['user', 'returs'])
                ->where('no_transaksi', 'like', "%{$request->search}%")
                ->latest()
                ->take(20)
                ->get();
        }

        return view('retur.create', compact('transaksis'));
    }

    public function pilihTransaksi(Transaksi $transaksi)
    {
        $transaksi->load(['detailTransaksis.produk.satuan', 'detailTransaksis.produk.produkSatuans.satuan', 'returs.detailReturs']);

        return view('retur.pilih', compact('transaksi'));
    }

    public function store(Request $request, Transaksi $transaksi)
    {
        $request->validate([
            'quantities' => 'required|array',
            'quantities.*' => 'nullable|integer|min:0',
            'alasan' => 'nullable|string|max:500',
        ]);

        $transaksi->load(['detailTransaksis.produk.satuan', 'detailTransaksis.produk.produkSatuans.satuan', 'returs.detailReturs']);

        $noRetur = 'RET-'.date('Ymd').'-'.str_pad(Retur::count() + 1, 5, '0', STR_PAD_LEFT);

        DB::beginTransaction();

        try {
            $retur = Retur::create([
                'no_retur' => $noRetur,
                'transaksi_id' => $transaksi->id,
                'user_id' => Auth::id(),
                'total_pengembalian' => 0,
                'alasan' => $request->alasan,
            ]);

            $total = 0;
            $returnedAny = false;

            foreach ($transaksi->detailTransaksis as $detail) {
                $qty = (int) ($request->quantities[$detail->id] ?? 0);

                if ($qty <= 0) {
                    continue;
                }

                $sudahDiretur = $detail->returs()
                    ->where('retur_id', '!=', $retur->id)
                    ->sum('jumlah');

                $sisa = $detail->jumlah - $sudahDiretur;

                if ($qty > $sisa) {
                    throw new \Exception("Jumlah retur melebihi sisa untuk {$detail->produk->nama_produk} (sisa {$sisa})");
                }

                $konversi = $this->konversiSatuan($detail->produk, $detail->satuan_jual);
                $subtotal = $detail->harga_satuan * $qty;
                $total += $subtotal;

                $retur->detailReturs()->create([
                    'detail_transaksi_id' => $detail->id,
                    'produk_id' => $detail->produk_id,
                    'jumlah' => $qty,
                    'jumlah_dasar' => $qty * $konversi,
                    'satuan_jual' => $detail->satuan_jual,
                    'harga_satuan' => $detail->harga_satuan,
                    'subtotal' => $subtotal,
                ]);

                if (Pengaturan::stokAktif()) {
                    $detail->produk->increment('stok', $qty * $konversi);
                }

                $returnedAny = true;
            }

            if (! $returnedAny) {
                DB::rollBack();

                return back()->withInput()->with('error', 'Tidak ada item yang dipilih untuk diretur');
            }

            $retur->update(['total_pengembalian' => $total]);

            DB::commit();

            return redirect()->route('retur.show', $retur->id)->with('success', 'Retur berhasil diproses');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    public function show(Retur $retur)
    {
        $retur->load(['transaksi.user', 'user', 'detailReturs.produk']);

        return view('retur.show', compact('retur'));
    }

    private function konversiSatuan($produk, ?string $namaSatuan): int
    {
        if (! $namaSatuan || $produk->satuan && $namaSatuan === $produk->satuan->nama_satuan) {
            return 1;
        }

        $tier = $produk->produkSatuans->first(fn ($t) => $t->satuan && $t->satuan->nama_satuan === $namaSatuan);

        return $tier ? $tier->konversi : 1;
    }
}

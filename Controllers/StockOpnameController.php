<?php

namespace App\Http\Controllers;

use App\Models\Pengaturan;
use App\Models\Produk;
use App\Models\StockOpname;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockOpnameController extends Controller
{
    private function cekFiturStok(): ?RedirectResponse
    {
        if (! Pengaturan::stokAktif()) {
            return redirect()->route('dashboard')->with('error', 'Fitur stok sedang dinonaktifkan. Aktifkan terlebih dahulu di menu Pengaturan.');
        }

        return null;
    }

    public function index(Request $request)
    {
        if ($redirect = $this->cekFiturStok()) {
            return $redirect;
        }
        $query = StockOpname::with('user')->withCount('details');

        if ($request->filled('search')) {
            $query->where('no_opname', 'like', "%{$request->search}%");
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $stockOpnames = $query->latest()->paginate(15);

        return view('stock-opname.index', compact('stockOpnames'));
    }

    public function create()
    {
        if ($redirect = $this->cekFiturStok()) {
            return $redirect;
        }

        $produks = Produk::with('kategori')->orderBy('nama_produk')->get();
        return view('stock-opname.create', compact('produks'));
    }

    public function store(Request $request)
    {
        if ($redirect = $this->cekFiturStok()) {
            return $redirect;
        }
        $request->validate([
            'stok_fisik' => 'required|array|min:1',
            'stok_fisik.*' => 'required|integer|min:0',
            'catatan' => 'nullable|string|max:1000',
        ]);

        $noOpname = 'SO-' . date('Ymd') . '-' . str_pad(StockOpname::count() + 1, 5, '0', STR_PAD_LEFT);

        DB::beginTransaction();

        try {
            $stockOpname = StockOpname::create([
                'no_opname' => $noOpname,
                'user_id' => Auth::id(),
                'catatan' => $request->catatan,
            ]);

            foreach ($request->stok_fisik as $produkId => $stokFisik) {
                $produk = Produk::findOrFail($produkId);

                StockOpnameDetail::create([
                    'stock_opname_id' => $stockOpname->id,
                    'produk_id' => $produk->id,
                    'stok_sistem' => $produk->stok,
                    'stok_fisik' => $stokFisik,
                    'selisih' => $stokFisik - $produk->stok,
                ]);

                if ($produk->stok != $stokFisik) {
                    $produk->update(['stok' => $stokFisik]);
                }
            }

            DB::commit();

            return redirect()->route('stock-opname.show', $stockOpname->id)->with('success', "Stock opname {$noOpname} berhasil disimpan dan stok telah disesuaikan");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(StockOpname $stockOpname)
    {
        if ($redirect = $this->cekFiturStok()) {
            return $redirect;
        }

        $stockOpname->load(['details.produk.kategori', 'user']);
        return view('stock-opname.show', compact('stockOpname'));
    }
}

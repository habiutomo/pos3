<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Pengaturan;
use App\Models\Produk;
use App\Models\Satuan;
use App\Services\Excel;
use App\Services\ProductImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProdukController extends Controller
{
    private function validasiProduk(Request $request, ?Produk $produk = null): array
    {
        $uniqueKode = 'unique:produks,kode_produk'.($produk ? ','.$produk->id : '');
        $uniqueBarcode = 'unique:produks,barcode'.($produk ? ','.$produk->id : '');

        $request->validate([
            'kategori_id' => 'required|exists:kategoris,id',
            'kode_produk' => "required|string|max:50|{$uniqueKode}",
            'barcode' => "nullable|string|max:50|{$uniqueBarcode}",
            'nama_produk' => 'required|string|max:255',
            'stok' => Pengaturan::stokAktif() ? 'required|integer|min:0' : 'nullable|integer|min:0',
            'harga_beli' => 'required|numeric|min:0',
            'satuan_tiers' => 'required|array|min:1',
            'satuan_tiers.0.satuan_id' => 'required|exists:satuans,id',
            'satuan_tiers.0.harga_jual' => 'required|numeric|min:0',
            'satuan_tiers.*.satuan_id' => 'required|exists:satuans,id|distinct',
            'satuan_tiers.*.konversi' => 'nullable|integer|min:1',
            'satuan_tiers.*.harga_jual' => 'required|numeric|min:0',
        ]);

        $data = $request->only('kategori_id', 'kode_produk', 'barcode', 'nama_produk', 'stok', 'harga_beli');

        if (($data['stok'] ?? null) === null) {
            unset($data['stok']);
        }

        $data['satuan_id'] = (int) $request->input('satuan_tiers.0.satuan_id');
        $data['harga_jual'] = $request->input('satuan_tiers.0.harga_jual');

        return $data;
    }

    private function simpanSatuanTiers(Request $request, Produk $produk): void
    {
        $tiers = collect($request->input('satuan_tiers', []))
            ->skip(1)
            ->filter(fn ($t) => ! empty($t['satuan_id']))
            ->reject(fn ($t) => (int) $t['satuan_id'] === (int) $produk->satuan_id)
            ->mapWithKeys(fn ($t) => [
                (int) $t['satuan_id'] => [
                    'konversi' => max(1, (int) ($t['konversi'] ?? 1)),
                    'harga_jual' => $t['harga_jual'],
                ],
            ]);

        if ($tiers->isNotEmpty()) {
            $produk->produkSatuans()->whereNotIn('satuan_id', $tiers->keys())->delete();
            foreach ($tiers as $satuanId => $attr) {
                $produk->produkSatuans()->updateOrCreate(['satuan_id' => $satuanId], $attr);
            }
        } else {
            $produk->produkSatuans()->delete();
        }
    }

    public function index(Request $request)
    {
        $query = Produk::with(['kategori', 'satuan', 'produkSatuans.satuan']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_produk', 'like', "%{$search}%")
                    ->orWhere('kode_produk', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        $produks = $query->latest()->paginate(15);
        $kategoris = Kategori::all();

        return view('produk.index', compact('produks', 'kategoris'));
    }

    public function create()
    {
        $kategoris = Kategori::all();
        $satuans = Satuan::orderBy('nama_satuan')->get();

        return view('produk.create', compact('kategoris', 'satuans'));
    }

    public function store(Request $request)
    {
        $data = $this->validasiProduk($request);

        $produk = DB::transaction(function () use ($request, $data) {
            $produk = Produk::create($data);
            $this->simpanSatuanTiers($request, $produk);

            return $produk;
        });

        return redirect()->route('produk.index')->with('success', 'Produk berhasil ditambahkan');
    }

    public function edit(Produk $produk)
    {
        $kategoris = Kategori::all();
        $satuans = Satuan::orderBy('nama_satuan')->get();

        return view('produk.edit', compact('produk', 'kategoris', 'satuans'));
    }

    public function update(Request $request, Produk $produk)
    {
        $data = $this->validasiProduk($request, $produk);

        DB::transaction(function () use ($request, $produk, $data) {
            $produk->update($data);
            $this->simpanSatuanTiers($request, $produk);
        });

        return redirect()->route('produk.index')->with('success', 'Produk berhasil diupdate');
    }

    public function destroy(Produk $produk)
    {
        $produk->delete();

        return redirect()->route('produk.index')->with('success', 'Produk berhasil dihapus');
    }

    public function importForm()
    {
        return view('produk.import', ['kolom' => ProductImportService::daftarKolom()]);
    }

    public function importStore(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|file|max:5120',
            'perbarui_yang_ada' => 'nullable|boolean',
        ]);

        $file = $request->file('file_excel');
        $ext = strtolower($file->getClientOriginalExtension() ?: '');

        if (! in_array($ext, ['xlsx', 'csv'])) {
            return back()->with('error', 'Format file harus .xlsx atau .csv. Gunakan template yang tersedia.');
        }

        try {
            $rows = Excel::read($file->getRealPath(), $ext);
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal membaca file: '.$e->getMessage());
        }

        if (count($rows) < 2) {
            return back()->with('error', 'File tidak berisi data produk (hanya header atau kosong).');
        }

        $hasil = app(ProductImportService::class)->import($rows, $request->boolean('perbarui_yang_ada'));

        if ($hasil['sukses'] > 0 && $hasil['gagal'] === 0) {
            session()->flash('success', "{$hasil['sukses']} produk berhasil diimpor");
        }

        return back()->with('import_result', $hasil);
    }

    public function importTemplate()
    {
        $header = ProductImportService::headersTemplate();

        $contoh = [
            ['PRD001', 'Indomie Goreng', 'Makanan', 'pcs', '', 50, 2800, 3500],
            ['PRD002', 'Aqua Botol 600ml', 'Minuman', 'dus', '8998866200011', 10, 38000, 45000],
        ];

        $isi = Excel::buildXlsx([$header, ...$contoh]);

        return response()->make($isi, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="template-import-produk.xlsx"',
            'Content-Length' => (string) strlen($isi),
        ]);
    }
}

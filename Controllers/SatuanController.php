<?php

namespace App\Http\Controllers;

use App\Models\Satuan;
use Illuminate\Http\Request;

class SatuanController extends Controller
{
    public function index()
    {
        $satuans = Satuan::withCount('produks')->orderBy('nama_satuan')->paginate(10);
        return view('satuan.index', compact('satuans'));
    }

    public function create()
    {
        return view('satuan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_satuan' => 'required|string|max:20|unique:satuans,nama_satuan',
        ]);

        Satuan::create(['nama_satuan' => strtolower(trim($request->nama_satuan))]);

        return redirect()->route('satuan.index')->with('success', 'Satuan berhasil ditambahkan');
    }

    public function edit(Satuan $satuan)
    {
        return view('satuan.edit', compact('satuan'));
    }

    public function update(Request $request, Satuan $satuan)
    {
        $request->validate([
            'nama_satuan' => 'required|string|max:20|unique:satuans,nama_satuan,' . $satuan->id,
        ]);

        $satuan->update(['nama_satuan' => strtolower(trim($request->nama_satuan))]);

        return redirect()->route('satuan.index')->with('success', 'Satuan berhasil diupdate');
    }

    public function destroy(Satuan $satuan)
    {
        if ($satuan->produks()->exists()) {
            return redirect()->route('satuan.index')->with('error', 'Satuan "' . $satuan->nama_satuan . '" tidak dapat dihapus karena masih digunakan oleh produk');
        }

        $satuan->delete();
        return redirect()->route('satuan.index')->with('success', 'Satuan berhasil dihapus');
    }
}

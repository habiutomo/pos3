<?php

namespace App\Http\Controllers;

use App\Models\Pengaturan;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    public function index()
    {
        $pengaturan = Pengaturan::toko();

        return view('pengaturan.index', compact('pengaturan'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'ukuran_kertas' => 'required|in:auto,58p,32,40,48,58,80',
            'panjang_kertas' => 'required|in:auto,100,150,200,250,297',
            'nama_toko' => 'required|string|max:100',
            'alamat_toko' => 'nullable|string|max:200',
            'telp_toko' => 'nullable|string|max:30',
        ]);

        foreach (['ukuran_kertas', 'panjang_kertas', 'nama_toko', 'alamat_toko', 'telp_toko'] as $key) {
            Pengaturan::set($key, $request->input($key));
        }

        Pengaturan::set('fitur_stok', $request->boolean('fitur_stok') ? '1' : '0');
        Pengaturan::set('auto_print_bluetooth', $request->boolean('auto_print_bluetooth') ? '1' : '0');

        return back()->with('success', 'Pengaturan berhasil disimpan');
    }
}

@extends('layouts.app')
@section('title', 'Pengaturan')

@section('content')
<div class="max-w-2xl mx-auto">
    <h2 class="text-2xl font-bold mb-6"><i class="fas fa-gear mr-2"></i>Pengaturan</h2>

    <form method="POST" action="{{ route('pengaturan.update') }}">
        @csrf

        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="font-bold text-lg mb-1">Ukuran Kertas Struk</h3>
            <p class="text-sm text-gray-500 mb-4">Mode Auto mengikuti lebar kertas dari printer secara otomatis. Pilih ukuran tetap hanya bila printer menuntut setting manual.</p>
            @error('ukuran_kertas')<p class="text-red-600 text-sm mb-2">{{ $message }}</p>@enderror

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <label class="relative cursor-pointer">
                    <input type="radio" name="ukuran_kertas" value="auto" class="peer sr-only"
                        {{ old('ukuran_kertas', $pengaturan['ukuran_kertas']) == 'auto' ? 'checked' : '' }}>
                    <div class="border-2 border-gray-200 rounded-lg p-4 peer-checked:border-emerald-600 peer-checked:bg-emerald-50 transition">
                        <div class="flex items-center gap-2 mb-3">
                            <i class="fas fa-wand-magic-sparkles text-emerald-600 text-xl"></i>
                            <div>
                                <p class="font-bold">Auto</p>
                                <p class="text-xs text-gray-500">Ikuti printer</p>
                            </div>
                        </div>
                        <span class="block w-max mx-auto text-[10px] font-semibold uppercase tracking-wide bg-emerald-100 text-emerald-700 rounded px-1.5 py-0.5">Direkomendasikan</span>
                        <p class="text-xs text-center mt-3 font-medium {{ old('ukuran_kertas', $pengaturan['ukuran_kertas']) == 'auto' ? 'text-emerald-700' : 'text-gray-500' }}">Lebar menyesuaikan otomatis</p>
                    </div>
                </label>

                <label class="relative cursor-pointer">
                    <input type="radio" name="ukuran_kertas" value="58p" class="peer sr-only"
                        {{ old('ukuran_kertas', $pengaturan['ukuran_kertas']) == '58p' ? 'checked' : '' }}>
                    <div class="border-2 border-gray-200 rounded-lg p-4 peer-checked:border-cyan-600 peer-checked:bg-cyan-50 transition">
                        <div class="flex items-center gap-2 mb-3">
                            <i class="fas fa-receipt text-cyan-600 text-xl"></i>
                            <div>
                                <p class="font-bold">58 mm Presisi</p>
                                <p class="text-xs text-gray-500">Custom 5,8 cm</p>
                            </div>
                        </div>
                        <div class="bg-white border border-gray-300 rounded mx-auto p-2" style="width: 58px; height: 90px;">
                            <div class="space-y-1">
                                <div class="h-1 bg-gray-400 rounded w-4/5 mx-auto"></div>
                                <div class="h-0.5 bg-gray-200 rounded"></div>
                                <div class="h-0.5 bg-gray-200 rounded"></div>
                                <div class="h-0.5 bg-gray-200 rounded"></div>
                                <div class="h-1 bg-gray-400 rounded w-full"></div>
                                <div class="h-0.5 bg-gray-200 rounded"></div>
                                <div class="h-0.5 bg-gray-200 rounded"></div>
                                <div class="h-1 bg-gray-400 rounded w-3/5 mx-auto"></div>
                            </div>
                        </div>
                        <p class="text-xs text-center mt-3 font-medium {{ old('ukuran_kertas', $pengaturan['ukuran_kertas']) == '58p' ? 'text-cyan-700' : 'text-gray-500' }}">Full area cetak 56 mm (presisi 5,8 cm)</p>
                    </div>
                </label>

                <label class="relative cursor-pointer">
                    <input type="radio" name="ukuran_kertas" value="32" class="peer sr-only"
                        {{ old('ukuran_kertas', $pengaturan['ukuran_kertas']) == '32' ? 'checked' : '' }}>
                    <div class="border-2 border-gray-200 rounded-lg p-4 peer-checked:border-orange-600 peer-checked:bg-orange-50 transition">
                        <div class="flex items-center gap-2 mb-3">
                            <i class="fas fa-table-columns text-orange-600 text-xl"></i>
                            <div>
                                <p class="font-bold">32 Kolom</p>
                                <p class="text-xs text-gray-500">Printer mini</p>
                            </div>
                        </div>
                        <div class="bg-white border border-gray-300 rounded mx-auto p-1.5 font-mono" style="width: 58px; height: 90px;">
                            <div class="space-y-0.5 text-[6px] leading-[7px] text-gray-500 break-all">################################<br>##&nbsp;&nbsp;&nbsp;&nbsp;STRUK&nbsp;32&nbsp;KOLOM###<br>################################</div>
                        </div>
                        <p class="text-xs text-center mt-3 font-medium {{ old('ukuran_kertas', $pengaturan['ukuran_kertas']) == '32' ? 'text-orange-700' : 'text-gray-500' }}">Lebar cetak ± 32 karakter</p>
                    </div>
                </label>

                <label class="relative cursor-pointer">
                    <input type="radio" name="ukuran_kertas" value="40" class="peer sr-only"
                        {{ old('ukuran_kertas', $pengaturan['ukuran_kertas']) == '40' ? 'checked' : '' }}>
                    <div class="border-2 border-gray-200 rounded-lg p-4 peer-checked:border-teal-600 peer-checked:bg-teal-50 transition">
                        <div class="flex items-center gap-2 mb-3">
                            <i class="fas fa-table-columns text-teal-600 text-xl"></i>
                            <div>
                                <p class="font-bold">40 Kolom</p>
                                <p class="text-xs text-gray-500">Custom</p>
                            </div>
                        </div>
                        <div class="bg-white border border-gray-300 rounded mx-auto p-1.5 font-mono" style="width: 72px; height: 90px;">
                            <div class="space-y-0.5 text-[6px] leading-[7px] text-gray-500 break-all">########################################<br>##&nbsp;&nbsp;&nbsp;STRUK&nbsp;40&nbsp;KOLOM#######<br>########################################</div>
                        </div>
                        <p class="text-xs text-center mt-3 font-medium {{ old('ukuran_kertas', $pengaturan['ukuran_kertas']) == '40' ? 'text-teal-700' : 'text-gray-500' }}">Lebar cetak ± 40 karakter</p>
                    </div>
                </label>

                <label class="relative cursor-pointer">
                    <input type="radio" name="ukuran_kertas" value="48" class="peer sr-only"
                        {{ old('ukuran_kertas', $pengaturan['ukuran_kertas']) == '48' ? 'checked' : '' }}>
                    <div class="border-2 border-gray-200 rounded-lg p-4 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 transition">
                        <div class="flex items-center gap-2 mb-3">
                            <i class="fas fa-table-columns text-indigo-600 text-xl"></i>
                            <div>
                                <p class="font-bold">48 Kolom</p>
                                <p class="text-xs text-gray-500">Custom POS-58</p>
                            </div>
                        </div>
                        <div class="bg-white border border-gray-300 rounded mx-auto p-1.5 font-mono" style="width: 58px; height: 90px;">
                            <div class="space-y-0.5 text-[6px] leading-[7px] text-gray-500 break-all">################################################<br>##&nbsp;&nbsp;&nbsp;STRUK&nbsp;48&nbsp;KOLOM##########<br>################################################</div>
                        </div>
                        <p class="text-xs text-center mt-3 font-medium {{ old('ukuran_kertas', $pengaturan['ukuran_kertas']) == '48' ? 'text-indigo-700' : 'text-gray-500' }}">Lebar cetak ± 48 karakter (kertas 58 mm)</p>
                    </div>
                </label>

                <label class="relative cursor-pointer">
                    <input type="radio" name="ukuran_kertas" value="58" class="peer sr-only"
                        {{ old('ukuran_kertas', $pengaturan['ukuran_kertas']) == '58' ? 'checked' : '' }}>
                    <div class="border-2 border-gray-200 rounded-lg p-4 peer-checked:border-blue-600 peer-checked:bg-blue-50 transition">
                        <div class="flex items-center gap-2 mb-3">
                            <i class="fas fa-receipt text-blue-600 text-xl"></i>
                            <div>
                                <p class="font-bold">58 mm</p>
                                <p class="text-xs text-gray-500">Printer POS-58</p>
                            </div>
                        </div>
                        <div class="bg-white border border-gray-300 rounded mx-auto p-2" style="width: 58px; height: 90px;">
                            <div class="space-y-1">
                                <div class="h-1 bg-gray-400 rounded w-4/5 mx-auto"></div>
                                <div class="h-0.5 bg-gray-200 rounded"></div>
                                <div class="h-0.5 bg-gray-200 rounded"></div>
                                <div class="h-0.5 bg-gray-200 rounded"></div>
                                <div class="h-0.5 bg-gray-200 rounded"></div>
                                <div class="h-1 bg-gray-400 rounded w-full"></div>
                                <div class="h-0.5 bg-gray-200 rounded"></div>
                                <div class="h-0.5 bg-gray-200 rounded"></div>
                                <div class="h-0.5 bg-gray-200 rounded"></div>
                                <div class="h-1 bg-gray-400 rounded w-3/5 mx-auto"></div>
                            </div>
                        </div>
                        <p class="text-xs text-center mt-3 font-medium {{ old('ukuran_kertas', $pengaturan['ukuran_kertas']) == '58' ? 'text-blue-700' : 'text-gray-500' }}">Full area cetak 48 mm (32 karakter)</p>
                    </div>
                </label>

                <label class="relative cursor-pointer">
                    <input type="radio" name="ukuran_kertas" value="80" class="peer sr-only"
                        {{ old('ukuran_kertas', $pengaturan['ukuran_kertas']) == '80' ? 'checked' : '' }}>
                    <div class="border-2 border-gray-200 rounded-lg p-4 peer-checked:border-blue-600 peer-checked:bg-blue-50 transition">
                        <div class="flex items-center gap-2 mb-3">
                            <i class="fas fa-receipt text-purple-600 text-xl"></i>
                            <div>
                                <p class="font-bold">80 mm</p>
                                <p class="text-xs text-gray-500">Printer POS-80</p>
                            </div>
                        </div>
                        <div class="bg-white border border-gray-300 rounded mx-auto p-2" style="width: 80px; height: 90px;">
                            <div class="space-y-1">
                                <div class="h-1 bg-gray-400 rounded w-4/5 mx-auto"></div>
                                <div class="h-0.5 bg-gray-200 rounded"></div>
                                <div class="h-0.5 bg-gray-200 rounded"></div>
                                <div class="h-0.5 bg-gray-200 rounded"></div>
                                <div class="h-0.5 bg-gray-200 rounded"></div>
                                <div class="h-1 bg-gray-400 rounded w-full"></div>
                                <div class="h-0.5 bg-gray-200 rounded"></div>
                                <div class="h-0.5 bg-gray-200 rounded"></div>
                                <div class="h-0.5 bg-gray-200 rounded"></div>
                                <div class="h-1 bg-gray-400 rounded w-3/5 mx-auto"></div>
                            </div>
                        </div>
                        <p class="text-xs text-center mt-3 font-medium {{ old('ukuran_kertas', $pengaturan['ukuran_kertas']) == '80' ? 'text-purple-700' : 'text-gray-500' }}">Lebar cetak ± 72 mm</p>
                    </div>
                </label>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Panjang Kertas</label>
                <select name="panjang_kertas"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach(['auto' => 'Auto (mengikuti panjang struk)', '100' => '100 mm', '150' => '150 mm', '200' => '200 mm', '250' => '250 mm', '297' => '297 mm'] as $val => $label)
                        <option value="{{ $val }}" {{ old('panjang_kertas', $pengaturan['panjang_kertas']) == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Hanya berlaku saat Ukuran Kertas dipilih tetap (58 mm Presisi, 32/40/48 kolom, 58, atau 80 mm). Bila panjang Auto, dipakai roll 297 mm dan lebar tetap presisi mengikuti ukuran kertas.</p>
                @error('panjang_kertas')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="font-bold text-lg mb-1"><i class="fas fa-bluetooth mr-2 text-purple-600"></i>Printer Thermal Bluetooth</h3>
            <p class="text-sm text-gray-500 mb-4">Cetak struk langsung ke printer thermal melalui koneksi Bluetooth dari browser.</p>

            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-4 text-sm text-purple-800">
                <p class="font-semibold mb-1"><i class="fas fa-circle-info mr-1"></i>Cara Menggunakan:</p>
                <ol class="list-decimal list-inside space-y-1">
                    <li>Pairing printer thermal Bluetooth ke perangkat ini di pengaturan Bluetooth sistem.</li>
                    <li>Buka halaman struk transaksi, lalu klik tombol <span class="font-semibold">"Cetak Bluetooth"</span>.</li>
                    <li>Browser akan menampilkan daftar perangkat Bluetooth — pilih printer thermal.</li>
                    <li>Struk akan langsung tercetak.</li>
                </ol>
                <p class="text-xs mt-2 text-purple-600">Catatan: Fitur ini butuh Chrome/Edge/Opera dan akses wajib melalui HTTPS atau localhost. Proses pairing kembali dilakukan otomatis pada transaksi berikutnya.</p>
            </div>

            <label class="flex items-center justify-between gap-4 cursor-pointer border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                <div>
                    <p class="font-medium text-gray-800">Cetak otomatis via Bluetooth</p>
                    <p class="text-sm text-gray-500">Setelah checkout, struk otomatis dikirim ke printer Bluetooth tanpa perlu klik tombol lagi.</p>
                </div>
                <input type="checkbox" name="auto_print_bluetooth" value="1" {{ old('auto_print_bluetooth', $pengaturan['auto_print_bluetooth']) == '1' ? 'checked' : '' }}
                    class="sr-only peer">
                <span class="relative inline-flex w-11 h-6 shrink-0 rounded-full bg-gray-300 peer-checked:bg-purple-600 transition
                    after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:w-5 after:h-5 after:rounded-full after:bg-white after:transition peer-checked:after:translate-x-5"></span>
            </label>
            <p class="text-xs text-gray-500 mt-2">Hanya berlaku untuk mode kolom (32/40/58). Pastikan printer sudah pernah dipilih lewat tombol "Cetak Bluetooth" minimal satu kali agar perangkat diingat.</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="font-bold text-lg mb-1">Informasi Toko</h3>
            <p class="text-sm text-gray-500 mb-4">Ditampilkan di bagian atas struk.</p>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Toko *</label>
                <input type="text" name="nama_toko" value="{{ old('nama_toko', $pengaturan['nama_toko']) }}" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('nama_toko')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                <textarea name="alamat_toko" rows="2"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('alamat_toko', $pengaturan['alamat_toko']) }}</textarea>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                <input type="text" name="telp_toko" value="{{ old('telp_toko', $pengaturan['telp_toko']) }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="font-bold text-lg mb-1">Fitur Stok</h3>
            <p class="text-sm text-gray-500 mb-4">Aktifkan jika ingin melacak stok produk. Nonaktifkan jika toko tidak menggunakan sistem stok.</p>

            <label class="flex items-center justify-between gap-4 cursor-pointer border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                <div>
                    <p class="font-medium text-gray-800">Manajemen Stok</p>
                    <p class="text-sm text-gray-500">Saat nonaktif: semua produk tampil di kasir tanpa batas jumlah, stok tidak berkurang saat transaksi, dan menu Stock Opname disembunyikan.</p>
                </div>
                <input type="checkbox" name="fitur_stok" value="1" {{ old('fitur_stok', \App\Models\Pengaturan::stokAktif()) ? 'checked' : '' }}
                    class="sr-only peer">
                <span class="relative inline-flex w-11 h-6 shrink-0 rounded-full bg-gray-300 peer-checked:bg-blue-600 transition
                    after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:w-5 after:h-5 after:rounded-full after:bg-white after:transition peer-checked:after:translate-x-5"></span>
            </label>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-lg hover:bg-blue-700 font-medium">
            <i class="fas fa-save mr-2"></i>Simpan Pengaturan
        </button>
    </form>
</div>
@endsection

@extends('layouts.app')
@section('title', 'Import Produk')

@section('content')
<div class="flex flex-wrap justify-between items-center gap-3 mb-6">
    <h2 class="text-xl lg:text-2xl font-bold">Import Produk dari Excel</h2>
    <a href="{{ route('produk.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
        <i class="fas fa-arrow-left mr-2"></i>Kembali
    </a>
</div>

@if(session('import_result'))
    @php $hasil = session('import_result'); @endphp
    <div class="bg-white rounded-lg shadow p-4 mb-6 border-l-4 {{ $hasil['gagal'] > 0 ? 'border-yellow-500' : 'border-green-500' }}">
        <h3 class="font-semibold mb-3"><i class="fas fa-clipboard-check mr-2"></i>Hasil Import</h3>
        <div class="flex flex-wrap gap-3 mb-3">
            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">Total Baris: {{ $hasil['total'] }}</span>
            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">Berhasil: {{ $hasil['sukses'] }}</span>
            <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-medium">Gagal: {{ $hasil['gagal'] }}</span>
            <span class="bg-gray-200 text-gray-800 px-3 py-1 rounded-full text-sm font-medium">Dilewati: {{ $hasil['dilewati'] }}</span>
        </div>
        @if($hasil['sukses'] > 0)
            <p class="text-green-700 text-sm mb-2"><i class="fas fa-check-circle mr-1"></i>{{ $hasil['sukses'] }} produk berhasil disimpan ke database.</p>
        @endif
        @if(count($hasil['catatan']))
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-2">
                <ul class="list-disc list-inside text-sm text-blue-700 space-y-1">
                    @foreach($hasil['catatan'] as $catatan)
                        <li>{{ $catatan }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if(count($hasil['errors']))
            <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                <p class="font-semibold text-red-700 text-sm mb-1">Baris yang gagal diimpor:</p>
                <ul class="list-disc list-inside text-sm text-red-700 space-y-1 max-h-64 overflow-y-auto">
                    @foreach($hasil['errors'] as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@endif

<div class="grid md:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="font-semibold text-lg mb-4"><i class="fas fa-circle-info text-blue-600 mr-2"></i>Cara Import</h3>
        <ol class="space-y-3 text-gray-700">
            <li class="flex items-start">
                <span class="bg-blue-600 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold mr-3 mt-0.5">1</span>
                <span>Download template Excel di samping.</span>
            </li>
            <li class="flex items-start">
                <span class="bg-blue-600 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold mr-3 mt-0.5">2</span>
                <span>Isi data produk pada baris baru (jangan mengubah baris header).</span>
            </li>
            <li class="flex items-start">
                <span class="bg-blue-600 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold mr-3 mt-0.5">3</span>
                <span>Upload file dan klik Proses Import.</span>
            </li>
        </ol>
        <a href="{{ route('produk.import.template') }}"
            class="mt-6 inline-flex items-center justify-center w-full bg-green-600 text-white px-4 py-3 rounded-lg hover:bg-green-700 font-medium">
            <i class="fas fa-file-excel mr-2"></i>Download Template Excel
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="font-semibold text-lg mb-4"><i class="fas fa-upload text-blue-600 mr-2"></i>Upload File</h3>
        <form action="{{ route('produk.import.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <label class="block text-sm font-medium text-gray-700 mb-1">File Excel / CSV</label>
            <input type="file" name="file_excel" accept=".xlsx,.csv" required
                class="w-full border border-gray-300 rounded-lg px-3 py-2 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <p class="text-xs text-gray-500 mt-1">Format yang didukung: .xlsx dan .csv (maksimal 5 MB)</p>

            <label class="flex items-center mt-4 cursor-pointer">
                <input type="checkbox" name="perbarui_yang_ada" value="1"
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <span class="ml-2 text-sm text-gray-700">Perbarui produk jika kode sudah terdaftar</span>
            </label>
            <p class="text-xs text-gray-500 mt-1 ml-6">Jika tidak dicentang, baris dengan kode produk yang sama akan dilewati.</p>

            <button type="submit"
                class="mt-5 w-full bg-blue-600 text-white px-4 py-3 rounded-lg hover:bg-blue-700 font-medium">
                <i class="fas fa-file-import mr-2"></i>Proses Import
            </button>
        </form>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-4 py-3 border-b border-gray-200">
        <h3 class="font-semibold"><i class="fas fa-table-list mr-2 text-gray-500"></i>Format Kolom Template</h3>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kolom</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach($kolom as $k)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono">{{ $k['kolom'] }}</td>
                    <td class="px-4 py-3 text-center">
                        @if($k['wajib'])
                            <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-0.5 rounded">Wajib</span>
                        @else
                            <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2 py-0.5 rounded">Opsional</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-700">{{ $k['ket'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

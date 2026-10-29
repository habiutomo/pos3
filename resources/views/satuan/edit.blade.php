@extends('layouts.app')
@section('title', 'Edit Satuan')

@section('content')
<div class="flex flex-wrap justify-between items-center gap-3 mb-6">
    <h2 class="text-xl lg:text-2xl font-bold">Edit Satuan</h2>
    <a href="{{ route('satuan.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
        <i class="fas fa-arrow-left mr-2"></i>Kembali
    </a>
</div>

<div class="bg-white rounded-lg shadow p-6 max-w-lg">
    <form method="POST" action="{{ route('satuan.update', $satuan) }}">
        @csrf
        @method('PUT')
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Satuan *</label>
            <input type="text" name="nama_satuan" value="{{ old('nama_satuan', $satuan->nama_satuan) }}" required maxlength="20"
                autofocus
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
            <i class="fas fa-save mr-2"></i>Update
        </button>
    </form>
</div>
@endsection

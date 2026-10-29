@extends('layouts.app')
@section('title', 'Edit User')

@section('content')
<div class="flex flex-wrap justify-between items-center gap-3 mb-6">
    <h2 class="text-xl lg:text-2xl font-bold">Edit User</h2>
    <a href="{{ route('user.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
        <i class="fas fa-arrow-left mr-2"></i>Kembali
    </a>
</div>

<div class="bg-white rounded-lg shadow p-6 max-w-lg">
    <form method="POST" action="{{ route('user.update', $user) }}">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama *</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input type="password" name="password"
                placeholder="Kosongkan jika tidak diubah" minlength="6"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
            <i class="fas fa-save mr-2"></i>Update
        </button>
    </form>
</div>
@endsection

@extends('layouts.app')
@section('title', 'User')

@section('content')
<div class="flex flex-wrap justify-between items-center gap-3 mb-6">
    <h2 class="text-xl lg:text-2xl font-bold">Manajemen User</h2>
    <a href="{{ route('user.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
        <i class="fas fa-plus mr-2"></i>Tambah User
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="w-full min-w-[500px]">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</td>
                    <td class="px-6 py-4 font-medium">
                        {{ $user->name }}
                        @if($user->id === auth()->id())
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded ml-1">Anda</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-gray-500">{{ $user->email }}</td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('user.edit', $user) }}" class="text-yellow-600 hover:text-yellow-800 mr-3">
                            <i class="fas fa-edit"></i>
                        </a>
                        @unless($user->id === auth()->id())
                            <form action="{{ route('user.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus user ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        @endunless
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">Belum ada data user</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $users->links() }}
</div>
@endsection

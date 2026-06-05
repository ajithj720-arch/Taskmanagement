@extends('layouts.app')
@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('content')
<div class="mt-2 max-w-lg">
    <div class="card-bg rounded-2xl border border-white/5 p-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold text-lg">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <p class="text-white font-semibold">{{ $user->name }}</p>
                <p class="text-xs text-slate-400">{{ $user->email }}</p>
            </div>
        </div>

        <form id="edit-user-form" method="POST" action="{{ route('users.update', $user) }}">
            @csrf @method('PUT')
            @include('users._form', ['user' => $user])

            <div class="mt-6 flex items-center gap-3">
                <button type="submit" form="edit-user-form"
                        class="px-6 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold rounded-lg transition">
                    Save Changes
                </button>
                <a href="{{ route('users.index') }}"
                   class="text-sm text-slate-400 hover:text-white transition">Cancel</a>
                @can('delete', $user)
                <button type="submit" form="delete-user-form"
                        onclick="return confirm('Delete {{ $user->name }}?')"
                        class="ml-auto text-sm text-red-400 hover:text-red-300 transition">
                    Delete User
                </button>
                @endcan
            </div>
        </form>

        @can('delete', $user)
        <form id="delete-user-form" method="POST" action="{{ route('users.destroy', $user) }}" class="hidden">
            @csrf @method('DELETE')
        </form>
        @endcan
    </div>
</div>
@endsection

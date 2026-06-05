@extends('layouts.app')
@section('title', 'Users')
@section('page-title', 'User Management')

@section('header-actions')
    <a href="{{ route('users.create') }}"
       class="flex items-center gap-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
        + Add User
    </a>
@endsection

@section('content')
<div class="mt-2">
    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        @php
            $total  = $users->total();
            $admins = $users->getCollection()->where('role','admin')->count();
            $regular = $users->getCollection()->where('role','user')->count();
        @endphp
        @foreach([['Total Users',$total,'indigo'],['Admins',$admins,'red'],['Regular Users',$regular,'blue']] as [$label,$count,$color])
        <div class="card-bg rounded-xl p-4 border border-white/5">
            <p class="text-xs text-slate-400 mb-1">{{ $label }}</p>
            <p class="text-2xl font-bold {{ $color==='indigo'?'text-indigo-400':($color==='red'?'text-red-400':'text-blue-400') }}">{{ $count }}</p>
        </div>
        @endforeach
    </div>

    {{-- Table --}}
    <div class="card-bg rounded-2xl border border-white/5 overflow-hidden">
        <table class="min-w-full">
            <thead>
                <tr class="border-b border-white/5">
                    <th class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wide px-6 py-3">User</th>
                    <th class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wide px-4 py-3">Role</th>
                    <th class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wide px-4 py-3">Tasks</th>
                    <th class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wide px-4 py-3">Joined</th>
                    <th class="text-right text-xs font-semibold text-slate-400 uppercase tracking-wide px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @foreach($users as $user)
                <tr class="hover:bg-white/5 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-white">{{ $user->name }}</p>
                                <p class="text-xs text-slate-400">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                            {{ $user->role === 'admin' ? 'badge-high' : 'badge-in-progress' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="px-4 py-4">
                        <span class="text-sm text-slate-300">{{ $user->tasks_count ?? 0 }}</span>
                    </td>
                    <td class="px-4 py-4">
                        <span class="text-sm text-slate-400">{{ $user->created_at->format('M d, Y') }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('users.edit', $user) }}"
                               class="px-3 py-1.5 text-xs font-medium text-slate-300 border border-slate-600 rounded-lg hover:bg-white/10 transition">
                                Edit
                            </a>
                            @can('delete', $user)
                            <form method="POST" action="{{ route('users.destroy', $user) }}"
                                  onsubmit="return confirm('Delete {{ $user->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="px-3 py-1.5 text-xs font-medium text-red-400 border border-red-800 rounded-lg hover:bg-red-500/10 transition">
                                    Delete
                                </button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($users->hasPages())
        <div class="px-6 py-4 border-t border-white/5">{{ $users->links() }}</div>
        @endif
    </div>
</div>
@endsection

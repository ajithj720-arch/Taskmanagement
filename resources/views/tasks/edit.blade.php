@extends('layouts.app')
@section('title', 'Edit Task')
@section('page-title', 'Edit Task')

@section('filters')
<div class="flex flex-wrap items-center gap-3 mb-1">
    <div class="relative">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input type="text" placeholder="Search Filter Task" class="input-dark pl-9 pr-4 py-2 rounded-lg text-sm w-48">
    </div>
    <select class="input-dark px-3 py-2 rounded-lg text-sm"><option>Status</option></select>
    <select class="input-dark px-3 py-2 rounded-lg text-sm"><option>All Members</option></select>
    <select class="input-dark px-3 py-2 rounded-lg text-sm"><option>Priority</option></select>
</div>
<p class="text-xs text-slate-400">Filter User Task</p>
@endsection

@section('content')
<div class="mt-2 max-w-lg">
    <div class="card-bg rounded-2xl border border-white/5 p-6">
        <div class="flex items-start justify-between mb-2">
            <h2 class="text-lg font-semibold text-white leading-snug">{{ $task->title }}</h2>
            <span class="text-slate-500 text-lg cursor-pointer leading-none">•••</span>
        </div>

        <form id="edit-task-form" method="POST" action="{{ route('tasks.update', $task) }}">
            @csrf @method('PUT')
            @include('tasks._form', ['task' => $task, 'users' => $users])

            <div class="mt-6 flex items-center gap-3">
                <button type="submit" form="edit-task-form"
                        class="px-6 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold rounded-lg transition">
                    Save Changes
                </button>
                <a href="{{ route('tasks.show', $task) }}" class="text-sm text-slate-400 hover:text-white transition">Cancel</a>
                @can('delete', $task)
                <button type="submit" form="delete-task-form"
                        onclick="return confirm('Delete this task permanently?')"
                        class="ml-auto text-sm text-red-400 hover:text-red-300 transition">
                    Delete
                </button>
                @endcan
            </div>
        </form>

        {{-- Delete form outside edit form --}}
        @can('delete', $task)
        <form id="delete-task-form" method="POST" action="{{ route('tasks.destroy', $task) }}" class="hidden">
            @csrf @method('DELETE')
        </form>
        @endcan
    </div>
</div>
@endsection

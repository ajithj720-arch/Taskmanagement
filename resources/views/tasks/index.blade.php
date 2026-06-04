@extends('layouts.app')
@section('title', 'Tasks')
@section('page-title', 'Task List')

@section('filters')
<form method="GET" action="{{ route('tasks.index') }}">
    <div class="flex flex-wrap items-center gap-3 mb-1">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search Filter Task"
                   class="input-dark pl-9 pr-4 py-2 rounded-lg text-sm w-48">
        </div>
        <select name="status" onchange="this.form.submit()" class="input-dark px-3 py-2 rounded-lg text-sm">
            <option value="">Status</option>
            @foreach(['pending'=>'Pending','in_progress'=>'In Progress','completed'=>'Completed'] as $v=>$l)
                <option value="{{ $v }}" @selected(($filters['status']??'')===$v)>{{ $l }}</option>
            @endforeach
        </select>
        <select name="assigned_to" onchange="this.form.submit()" class="input-dark px-3 py-2 rounded-lg text-sm">
            <option value="">All Members</option>
            @foreach($users as $u)
                <option value="{{ $u->id }}" @selected(($filters['assigned_to']??'')==$u->id)>{{ $u->name }}</option>
            @endforeach
        </select>
        <select name="priority" onchange="this.form.submit()" class="input-dark px-3 py-2 rounded-lg text-sm">
            <option value="">Priority</option>
            @foreach(['low'=>'Low','medium'=>'Medium','high'=>'High'] as $v=>$l)
                <option value="{{ $v }}" @selected(($filters['priority']??'')===$v)>{{ $l }}</option>
            @endforeach
        </select>
        @if(array_filter($filters))
        <a href="{{ route('tasks.index') }}" class="text-xs text-slate-400 hover:text-white underline">Clear</a>
        @endif
    </div>
    <p class="text-xs text-slate-400">Filter User Task</p>
</form>
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
    @forelse($tasks as $task)
    <div class="card-bg rounded-xl border border-white/5 p-5 hover:border-blue-500/30 transition">
        <div class="flex items-center justify-between mb-3">
            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                @if($task->status->value==='completed') badge-completed
                @elseif($task->status->value==='in_progress') badge-in-progress
                @else badge-pending @endif">
                {{ $task->status->label() }}
            </span>
            <span class="text-slate-500 text-lg cursor-pointer leading-none select-none">•••</span>
        </div>

        <h3 class="text-white font-semibold text-base mb-2 leading-snug">{{ $task->title }}</h3>

        <div class="flex items-center gap-2 mb-3">
            <span class="text-xs text-slate-400">Status</span>
            <span class="px-2 py-0.5 rounded text-xs font-bold
                @if($task->priority->value==='high') badge-high
                @elseif($task->priority->value==='medium') badge-medium
                @else badge-low @endif">
                Priority {{ ucfirst($task->priority->value) }}
            </span>
        </div>

        @if($task->description)
        <p class="text-xs text-slate-400 mb-3 line-clamp-2">{{ $task->description }}</p>
        @endif

        <div class="text-xs text-slate-400 space-y-1 mb-4">
            <p>Assigned to: <span class="text-slate-300">{{ $task->assignee?->name ?? 'Unassigned' }}</span></p>
            @if($task->due_date)
            <p>Due {{ $task->due_date->format('Y-m-d') }}</p>
            @endif
            <p class="capitalize">{{ ucfirst($task->priority->value) }}</p>
        </div>

        <div class="flex items-center justify-end gap-2 pt-2 border-t border-white/5">
            @can('update', $task)
            <a href="{{ route('tasks.edit', $task) }}"
               class="px-3 py-1.5 text-xs font-medium text-slate-300 border border-slate-600 rounded-lg hover:bg-white/10 transition">
                Edit
            </a>
            @endcan
            <a href="{{ route('tasks.show', $task) }}"
               class="px-3 py-1.5 text-xs font-medium bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition">
                View
            </a>
        </div>
    </div>
    @empty
    <div class="col-span-2 card-bg rounded-xl border border-white/5 p-16 text-center text-slate-400">
        <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
        </svg>
        <p>No tasks found. <a href="{{ route('tasks.create') }}" class="text-blue-400 underline">Create one</a></p>
    </div>
    @endforelse
</div>

@if($tasks->hasPages())
<div class="mt-6">{{ $tasks->links() }}</div>
@endif
@endsection

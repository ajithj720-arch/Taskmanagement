@extends('layouts.app')
@section('title', $task->title)
@section('page-title', 'Task Detail + AI Summary')

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

@section('sidebar-extra')
{{-- Refresh AI Summary button in sidebar --}}
<div class="px-4 pb-4">
    <a href="{{ route('tasks.show', $task) }}?refresh_ai=1"
       class="flex items-center justify-center gap-2 w-full py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold rounded-lg transition">
        Refresh AI Summary
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
    </a>
</div>
@endsection

@section('content')
<div class="mt-2 max-w-lg">
    <div class="card-bg rounded-2xl border border-white/5 p-6">
        <div class="flex items-start justify-between mb-4">
            <h2 class="text-xl font-bold text-white leading-snug">{{ $task->title }}</h2>
            <span class="text-slate-500 text-lg cursor-pointer leading-none">•••</span>
        </div>

        {{-- Status + Priority badges --}}
        <div class="flex items-center gap-3 mb-5">
            <div class="flex items-center gap-1.5">
                <span class="text-xs text-slate-400">Status</span>
                <span class="px-2 py-0.5 rounded text-xs font-semibold
                    @if($task->status->value==='completed') badge-completed
                    @elseif($task->status->value==='in_progress') badge-in-progress
                    @else badge-pending @endif">
                    {{ $task->status->value === 'in_progress' ? '3 Progress' : $task->status->label() }}
                </span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="text-xs text-slate-400">Priority</span>
                <span class="px-2 py-0.5 rounded text-xs font-semibold
                    @if($task->priority->value==='high') badge-high
                    @elseif($task->priority->value==='medium') badge-medium
                    @else badge-low @endif">
                    {{ ucfirst($task->priority->value) }}
                </span>
            </div>
        </div>

        {{-- Description --}}
        <div class="mb-4">
            <p class="text-xs font-semibold text-slate-300 uppercase tracking-wide mb-2">Description</p>
            <p class="text-xs text-slate-400">
                Assigned to: <span class="text-slate-300">{{ $task->assignee?->name ?? 'Unassigned' }}</span>
            </p>
            @if($task->due_date)
            <div class="flex items-center gap-2 mt-1.5">
                <input type="text" value="Due Date: {{ $task->due_date->format('Y-m-d') }}"
                       readonly class="input-dark w-full px-3 py-2 rounded-lg text-xs">
            </div>
            @endif
            @if($task->description)
            <p class="text-xs text-slate-400 mt-3 leading-relaxed">{{ $task->description }}</p>
            @endif
        </div>

        {{-- AI Summary --}}
        <div class="ai-box rounded-xl p-4 mb-4">
            <p class="text-xs font-semibold text-slate-300 mb-2">AI-Generated Summary</p>
            @if($task->ai_summary)
                <p class="text-xs text-slate-400 leading-relaxed">{{ $task->ai_summary }}</p>
            @else
                <p class="text-xs text-slate-500 italic">Processing in background queue...</p>
            @endif
        </div>

        {{-- AI priority result --}}
        @if($task->ai_summary)
        <div class="mb-5 text-xs text-slate-400">
            <span class="font-semibold text-slate-300">AI Summary:</span>
            {{ Str::limit($task->ai_summary, 80) }}
            Priority: <span class="font-semibold text-white capitalize">{{ $task->ai_priority?->value ?? $task->priority->value }}</span>
        </div>
        @endif

        {{-- Status update + actions --}}
        <div class="flex items-center gap-3 pt-4 border-t border-white/5">
            @can('update', $task)
            <a href="{{ route('tasks.edit', $task) }}"
               class="px-5 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold rounded-lg transition">
                Edit Task
            </a>
            @endcan
            @can('updateStatus', $task)
            <form id="status-form" method="POST" action="{{ route('tasks.status', $task) }}" class="flex items-center gap-2">
                @csrf @method('PATCH')
                <select name="status" class="input-dark px-3 py-2 rounded-lg text-sm">
                    @foreach(['pending'=>'Pending','in_progress'=>'In Progress','completed'=>'Completed'] as $v=>$l)
                        <option value="{{ $v }}" @selected($task->status->value === $v)>{{ $l }}</option>
                    @endforeach
                </select>
                <button type="submit" form="status-form"
                        class="px-4 py-2 bg-slate-600 hover:bg-slate-500 text-white text-xs font-medium rounded-lg transition">
                    Update
                </button>
            </form>
            @endcan
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('tasks.index') }}" class="text-sm text-slate-400 hover:text-white transition">← All Tasks</a>
    </div>
</div>
@endsection

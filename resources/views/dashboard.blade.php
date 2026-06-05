@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('filters')
<div class="flex flex-wrap items-center gap-3 mb-1">
    <div class="relative">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input type="text" placeholder="Search Filter Task"
               class="input-dark pl-9 pr-4 py-2 rounded-lg text-sm w-48">
    </div>
    <select class="input-dark px-3 py-2 rounded-lg text-sm"><option>Status</option></select>
    <select class="input-dark px-3 py-2 rounded-lg text-sm"><option>All Members</option></select>
    <select class="input-dark px-3 py-2 rounded-lg text-sm"><option>Priority</option></select>
</div>
<p class="text-xs text-slate-400">Filter User Task</p>
@endsection

@section('content')

{{-- Stats row — Total, Pending, In Progress, Completed, High Priority --}}
<div class="grid grid-cols-2 sm:grid-cols-5 gap-4 mb-6 mt-2">
    @foreach([
        ['Total Tasks',    $stats['total'],        'stat-bar-blue'],
        ['Pending',        $stats['pending'],       'stat-bar-gray'],
        ['In Progress',    $stats['in_progress'],   'stat-bar-yellow'],
        ['Completed',      $stats['completed'],     'stat-bar-green'],
        ['High Priority',  $stats['high_priority'], 'stat-bar-red'],
    ] as [$label, $val, $bar])
    <div class="card-bg rounded-xl p-4 border border-white/5">
        <p class="text-xs text-slate-400 mb-1">{{ $label }}</p>
        <p class="text-2xl font-bold text-white">{{ $val }}</p>
        <div class="mt-2 {{ $bar }}"></div>
    </div>
    @endforeach
</div>

{{-- Chart.js — Task Status Overview --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
    <div class="card-bg rounded-xl border border-white/5 p-5">
        <p class="text-sm font-semibold text-white mb-4">Task Status Overview</p>
        <div class="flex justify-center">
            <canvas id="statusDonut" width="160" height="160"></canvas>
        </div>
        <div class="mt-4 space-y-2">
            @foreach([['Pending','#64748b',$stats['pending']],['In Progress','#f59e0b',$stats['in_progress']],['Completed','#10b981',$stats['completed']]] as [$lbl,$col,$cnt])
            <div class="flex items-center justify-between text-xs">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background:{{ $col }}"></span>
                    <span class="text-slate-400">{{ $lbl }}</span>
                </div>
                <span class="text-white font-semibold">{{ $cnt }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <div class="card-bg rounded-xl border border-white/5 p-5 lg:col-span-2">
        <p class="text-sm font-semibold text-white mb-4">Monthly Task Completion</p>
        <canvas id="completionBar" height="120"></canvas>
    </div>
</div>

{{-- Recent tasks as cards --}}
<p class="text-sm font-semibold text-white mb-3">Recent Tasks</p>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @forelse($recentTasks as $task)
    <div class="card-bg rounded-xl border border-white/5 p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                @if($task->status->value==='completed') badge-completed
                @elseif($task->status->value==='in_progress') badge-in-progress
                @else badge-pending @endif">
                {{ $task->status->label() }}
            </span>
            <span class="text-slate-500 text-lg leading-none cursor-pointer">•••</span>
        </div>
        <h3 class="text-white font-semibold text-base mb-2">{{ $task->title }}</h3>
        <div class="flex items-center gap-2 mb-2">
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
            @if($task->due_date)<p>Due: <span class="text-slate-300">{{ $task->due_date->format('Y-m-d') }}</span></p>@endif
        </div>
        <div class="flex items-center justify-end gap-2">
            @can('update', $task)
            <a href="{{ route('tasks.edit', $task) }}"
               class="px-3 py-1.5 text-xs font-medium text-slate-300 border border-slate-600 rounded-lg hover:bg-white/10 transition">Edit</a>
            @endcan
            <a href="{{ route('tasks.show', $task) }}"
               class="px-3 py-1.5 text-xs font-medium bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition">View</a>
        </div>
    </div>
    @empty
    <div class="col-span-2 card-bg rounded-xl border border-white/5 p-12 text-center text-slate-400">
        <p>No tasks yet. <a href="{{ route('tasks.create') }}" class="text-blue-400 underline">Create one</a></p>
    </div>
    @endforelse
</div>

@endsection

@push('scripts')
<script>
// Donut Chart — Task Status
new Chart(document.getElementById('statusDonut'), {
    type: 'doughnut',
    data: {
        labels: ['Pending', 'In Progress', 'Completed'],
        datasets: [{
            data: [{{ $stats['pending'] }}, {{ $stats['in_progress'] }}, {{ $stats['completed'] }}],
            backgroundColor: ['#64748b', '#f59e0b', '#10b981'],
            borderWidth: 0,
            hoverOffset: 6
        }]
    },
    options: {
        cutout: '70%',
        responsive: false,
        plugins: { legend: { display: false } }
    }
});

// Bar Chart — Monthly Completion (real data)
new Chart(document.getElementById('completionBar'), {
    type: 'bar',
    data: {
        labels: {!! json_encode(collect($monthlyCompleted)->pluck('label')) !!},
        datasets: [{
            label: 'Completed Tasks',
            data: {!! json_encode(collect($monthlyCompleted)->pluck('count')) !!},
            backgroundColor: '#3b82f6',
            borderRadius: 4
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { ticks: { color: '#94a3b8', font: { size: 11 } }, grid: { display: false } },
            y: { ticks: { color: '#94a3b8', font: { size: 11 }, stepSize: 1 }, grid: { color: '#374060' }, beginAtZero: true }
        }
    }
});
</script>
@endpush

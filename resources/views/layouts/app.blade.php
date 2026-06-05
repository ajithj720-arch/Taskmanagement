<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Task Manager') }} - @yield('title', 'Dashboard')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    @if(app()->environment('production'))
        <link rel="stylesheet" href="{{ asset('build/assets/app-CrNqOrdm.css') }}">
        <script type="module" src="{{ asset('build/assets/app-CpOtDUtT.js') }}"></script>
    @else
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --bg-body:    #1a2035;
            --bg-card:    #242f4b;
            --border-dim: #374060;
            --blue-main:  #4a7cdc;
        }
        body             { background-color: var(--bg-body); font-family: 'Inter', sans-serif; }
        .card-bg         { background-color: var(--bg-card); }
        .sidebar-bg      { background-color: var(--bg-card); }
        .main-content    { max-width: calc(100% - 260px); }
        .input-dark      { background-color: var(--bg-body); border: 1px solid var(--border-dim); color: #cbd5e1; }
        .input-dark::placeholder { color: #64748b; }
        .input-dark:focus { outline: none; border-color: var(--blue-main); }
        .nav-active      { background-color: var(--blue-main); color: #fff; }
        .badge-high      { background-color: #ef4444; color: #fff; }
        .badge-medium    { background-color: #f59e0b; color: #fff; }
        .badge-low       { background-color: #10b981; color: #fff; }
        .badge-in-progress { background-color: #3b82f6; color: #fff; }
        .badge-pending   { background-color: #64748b; color: #fff; }
        .badge-completed { background-color: #10b981; color: #fff; }
        .ai-box          { background-color: var(--bg-body); }
        .guest-card      { background-color: var(--bg-card); border: 1px solid rgba(255,255,255,0.07); }
        .stat-bar-blue   { background-color: #3b82f6; height:4px; width:60%; border-radius:9999px; }
        .stat-bar-gray   { background-color: #64748b; height:4px; width:60%; border-radius:9999px; }
        .stat-bar-yellow { background-color: #f59e0b; height:4px; width:60%; border-radius:9999px; }
        .stat-bar-green  { background-color: #10b981; height:4px; width:60%; border-radius:9999px; }
        .stat-bar-red    { background-color: #ef4444; height:4px; width:60%; border-radius:9999px; }
        select option    { background-color: var(--bg-card); }
    </style>
</head>
<body class="text-slate-200 min-h-screen">

<div class="flex min-h-screen">

    {{-- MAIN CONTENT --}}
    <div class="flex-1 flex flex-col min-h-screen main-content">

        {{-- Top bar --}}
        <div class="px-8 pt-8 pb-4 flex items-center justify-between">
            <h1 class="text-3xl font-bold text-white">@yield('page-title', 'Dashboard')</h1>
            <a href="{{ route('tasks.create') }}"
               class="flex items-center gap-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                + New Task
            </a>
        </div>

        {{-- Search / Filter bar --}}
        <div class="px-8 pb-2">
            @yield('filters')
        </div>

        {{-- Alerts --}}
        <div class="px-8">
            @if(session('success'))
                <div class="mb-4 flex items-center gap-2 bg-green-500/20 border border-green-500/40 text-green-300 rounded-lg px-4 py-2.5 text-sm">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 flex items-center gap-2 bg-red-500/20 border border-red-500/40 text-red-300 rounded-lg px-4 py-2.5 text-sm">
                    {{ session('error') }}
                </div>
            @endif
        </div>

        {{-- Page content --}}
        <main class="flex-1 px-8 pb-8">
            @yield('content')
        </main>
    </div>

    {{-- RIGHT SIDEBAR --}}
    <aside class="w-64 sidebar-bg min-h-screen flex flex-col flex-shrink-0 fixed right-0 top-0 h-full overflow-y-auto">

        {{-- User info --}}
        <div class="p-5 border-b border-white/10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div>
                    <p class="text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                </div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="p-4 border-b border-white/10">
            <div class="mb-1">
                <div class="flex items-center justify-between px-3 py-2 text-sm text-slate-300 cursor-pointer">
                    <span class="font-medium">Tasks</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                </div>
                <a href="{{ route('tasks.index') }}"
                   class="flex items-center px-3 py-2 text-sm rounded-lg font-medium transition {{ request()->routeIs('tasks.*') ? 'nav-active' : 'text-slate-300 hover:bg-white/10' }}">
                    Tasks
                </a>
            </div>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('users.index') }}"
               class="flex items-center px-3 py-2 text-sm rounded-lg font-medium transition {{ request()->routeIs('users.*') ? 'nav-active' : 'text-slate-300 hover:bg-white/10' }}">
                Users
                <span class="ml-1 text-xs {{ request()->routeIs('users.*') ? 'text-blue-200' : 'text-slate-500' }}">(Admin only)</span>
            </a>
            @endif
            <form id="logout-form" method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" form="logout-form"
                        class="w-full text-left px-3 py-2 text-sm text-slate-300 hover:bg-white/10 rounded-lg transition">
                    Logout
                </button>
            </form>
        </nav>

        {{-- Stats donut --}}
        <div class="p-4 border-b border-white/10">
            @php $stats = app(\App\Services\TaskService::class)->stats(); @endphp
            <div class="flex items-center justify-around mb-2">
                <div class="text-center">
                    <canvas id="donutChart" width="90" height="90"></canvas>
                </div>
            </div>
            <div class="flex justify-around text-center text-xs mt-1">
                <div>
                    <p class="text-white font-bold text-lg">{{ $stats['total'] }}</p>
                    <p class="text-slate-400">Total</p>
                </div>
                <div>
                    <p class="text-green-400 font-bold text-lg">{{ $stats['completed'] }}</p>
                    <p class="text-slate-400">Done</p>
                </div>
                <div>
                    <p class="text-blue-400 font-bold text-lg">{{ $stats['in_progress'] }}</p>
                    <p class="text-slate-400">Active</p>
                </div>
            </div>
            <p class="text-xs text-center text-slate-400 mt-2">Monthly Task Completion</p>
        </div>

        {{-- Bar chart --}}
        <div class="p-4">
            <p class="text-xs font-semibold text-white mb-3">Monthly Task Completion</p>
            <canvas id="barChart" height="120"></canvas>
        </div>

        {{-- Extra sidebar content --}}
        @yield('sidebar-extra')
    </aside>
</div>

@push('scripts')
<script>
// Donut chart
new Chart(document.getElementById('donutChart'), {
    type: 'doughnut',
    data: {
        datasets: [{
            data: [{{ $stats['completed'] }}, {{ $stats['in_progress'] }}, {{ $stats['pending'] }}],
            backgroundColor: ['#10b981','#3b82f6','#64748b'],
            borderWidth: 0,
        }]
    },
    options: { cutout: '70%', plugins: { legend: { display: false } }, responsive: false }
});

// Bar chart
new Chart(document.getElementById('barChart'), {
    type: 'bar',
    data: {
        labels: ['Jan','Feb','Mar','Apr','May'],
        datasets: [{
            data: [30, 45, 28, 60, {{ $stats['completed'] }}],
            backgroundColor: '#3b82f6',
            borderRadius: 4,
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        scales: {
            x: { ticks: { color: '#94a3b8', font: { size: 10 } }, grid: { display: false } },
            y: { ticks: { color: '#94a3b8', font: { size: 10 } }, grid: { color: '#374060' } }
        }
    }
});
</script>
@endpush

@stack('scripts')
</body>
</html>

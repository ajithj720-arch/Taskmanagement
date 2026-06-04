<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Task Manager') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { fontFamily: { sans: ['Inter','sans-serif'] } } } }</script>
    <style>
        body      { background-color: #1a2035; }
        .auth-card { background-color: #242f4b; border: 1px solid rgba(255,255,255,0.07); }
        .input-dark { background-color: #1a2035; border: 1px solid #374060; color: #cbd5e1; }
        .input-dark::placeholder { color: #64748b; }
        .input-dark:focus { outline: none; border-color: #4a7cdc; }
    </style>
</head>
<body class="font-sans antialiased min-h-screen">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <div class="mb-6">
            <a href="/" class="text-2xl font-bold text-white tracking-tight">TaskManager</a>
        </div>
        <div class="w-full sm:max-w-md px-8 py-8 overflow-hidden rounded-2xl shadow-xl auth-card">
            {{ $slot }}
        </div>
    </div>
</body>
</html>

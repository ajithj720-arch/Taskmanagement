@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'input-dark w-full px-4 py-2.5 rounded-lg text-sm placeholder-slate-500 transition']) }}>

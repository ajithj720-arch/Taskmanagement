<button {{ $attributes->merge(['type' => 'submit', 'class' => 'w-full px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold rounded-lg transition']) }}>
    {{ $slot }}
</button>

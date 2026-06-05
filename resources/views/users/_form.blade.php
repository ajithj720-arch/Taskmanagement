@props(['user' => null])

<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-slate-300 mb-1.5">Name <span class="text-red-400">*</span></label>
        <input type="text" name="name" value="{{ old('name', $user?->name) }}"
               placeholder="Full name"
               class="input-dark w-full px-4 py-2.5 rounded-lg text-sm @error('name') border-red-500 @enderror">
        @error('name')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-300 mb-1.5">Email <span class="text-red-400">*</span></label>
        <input type="email" name="email" value="{{ old('email', $user?->email) }}"
               placeholder="email@example.com"
               class="input-dark w-full px-4 py-2.5 rounded-lg text-sm @error('email') border-red-500 @enderror">
        @error('email')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-300 mb-1.5">
            Password {{ $user ? '(leave blank to keep current)' : '*' }}
        </label>
        <input type="password" name="password"
               placeholder="{{ $user ? 'Leave blank to keep current' : 'Min 8 characters' }}"
               class="input-dark w-full px-4 py-2.5 rounded-lg text-sm @error('password') border-red-500 @enderror">
        @error('password')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-300 mb-1.5">Confirm Password</label>
        <input type="password" name="password_confirmation"
               placeholder="Repeat password"
               class="input-dark w-full px-4 py-2.5 rounded-lg text-sm">
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-300 mb-2">Role <span class="text-red-400">*</span></label>
        <div class="flex items-center gap-3">
            @foreach(['user' => 'Regular User', 'admin' => 'Admin'] as $val => $lbl)
            <label class="cursor-pointer">
                <input type="radio" name="role" value="{{ $val }}" class="sr-only peer"
                       {{ old('role', $user?->role ?? 'user') === $val ? 'checked' : '' }}>
                <span class="px-4 py-2 rounded-lg text-sm font-medium border transition
                    peer-checked:bg-blue-500 peer-checked:border-blue-500 peer-checked:text-white
                    border-slate-600 text-slate-400 hover:border-blue-400">
                    {{ $lbl }}
                </span>
            </label>
            @endforeach
        </div>
        @error('role')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
    </div>
</div>

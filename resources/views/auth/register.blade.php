<x-guest-layout>
    <h2 class="auth-heading">Create account</h2>
    <p class="auth-subheading">Sign up to get started with TaskManager</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="form-group">
            <label for="name">Full Name</label>
            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name') }}"
                placeholder="John Doe"
                required
                autofocus
                autocomplete="name"
            />
            @error('name')
                <div class="form-error">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#f87171" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Email -->
        <div class="form-group">
            <label for="email">Email Address</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="you@example.com"
                required
                autocomplete="username"
            />
            @error('email')
                <div class="form-error">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#f87171" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-group">
            <label for="password">Password</label>
            <input
                id="password"
                type="password"
                name="password"
                placeholder="••••••••"
                required
                autocomplete="new-password"
            />
            @error('password')
                <div class="form-error">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#f87171" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
            <label for="password_confirmation">Confirm Password</label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                placeholder="••••••••"
                required
                autocomplete="new-password"
            />
            @error('password_confirmation')
                <div class="form-error">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#f87171" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <button type="submit" class="btn-login" style="margin-top: 1.25rem;">Create Account &rarr;</button>
    </form>

    <div class="create-account">
        Already have an account?
        <a href="{{ route('login') }}">Sign in</a>
    </div>
</x-guest-layout>

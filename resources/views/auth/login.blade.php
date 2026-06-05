<x-guest-layout>
    <h2 class="auth-heading">Welcome back</h2>
    <p class="auth-subheading">Sign in to your account to continue</p>

    <!-- Session status -->
    @if (session('status'))
        <div class="session-status">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

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
                autofocus
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
                autocomplete="current-password"
            />
            @error('password')
                <div class="form-error">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#f87171" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Remember + Forgot -->
        <div class="auth-row">
            <label class="remember-label">
                <input type="checkbox" name="remember" id="remember_me">
                <span>Remember me</span>
            </label>
            @if (Route::has('password.request'))
                <a class="forgot-link" href="{{ route('password.request') }}">Forgot password?</a>
            @endif
        </div>

        <button type="submit" class="btn-login">Sign in &rarr;</button>
    </form>

    <div class="create-account">
        Don't have an account?
        @if (Route::has('register'))
            <a href="{{ route('register') }}">Create one</a>
        @endif
    </div>

    <div class="auth-divider"><span>Demo credentials</span></div>

    <div class="demo-card">
        <p>Quick access</p>
        <div class="demo-cred">
            <span class="demo-badge admin">Admin</span>
            <span>admin@example.com / password</span>
        </div>
        <div class="demo-cred">
            <span class="demo-badge user">User</span>
            <span>user@example.com / password</span>
        </div>
    </div>
</x-guest-layout>

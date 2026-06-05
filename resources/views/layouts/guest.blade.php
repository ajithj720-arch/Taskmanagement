<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Task Manager') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: #0f1623;
            min-height: 100vh;
            display: flex;
        }

        /* ── Left panel ── */
        .auth-left {
            display: none;
            width: 50%;
            background: linear-gradient(135deg, #1a2a4a 0%, #0f1e38 50%, #0a1628 100%);
            position: relative;
            overflow: hidden;
            padding: 3rem;
            flex-direction: column;
            justify-content: space-between;
        }

        @media (min-width: 1024px) {
            .auth-left { display: flex; }
        }

        /* Decorative blobs */
        .auth-left::before {
            content: '';
            position: absolute;
            top: -120px; left: -120px;
            width: 420px; height: 420px;
            background: radial-gradient(circle, rgba(74,124,220,0.25) 0%, transparent 70%);
            border-radius: 50%;
        }
        .auth-left::after {
            content: '';
            position: absolute;
            bottom: -100px; right: -80px;
            width: 380px; height: 380px;
            background: radial-gradient(circle, rgba(99,102,241,0.2) 0%, transparent 70%);
            border-radius: 50%;
        }

        .auth-left-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
            z-index: 1;
        }

        .auth-left-logo .logo-icon {
            width: 38px; height: 38px;
            background: linear-gradient(135deg, #4a7cdc, #6366f1);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
        }

        .auth-left-logo .logo-icon svg {
            width: 20px; height: 20px; fill: white;
        }

        .auth-left-logo span {
            font-size: 1.25rem;
            font-weight: 700;
            color: white;
            letter-spacing: -0.02em;
        }

        .auth-left-hero {
            position: relative;
            z-index: 1;
        }

        .auth-left-hero h1 {
            font-size: 2.5rem;
            font-weight: 800;
            color: white;
            line-height: 1.15;
            letter-spacing: -0.03em;
            margin-bottom: 1rem;
        }

        .auth-left-hero h1 span {
            background: linear-gradient(90deg, #4a7cdc, #818cf8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .auth-left-hero p {
            color: #8fa3c0;
            font-size: 1rem;
            line-height: 1.65;
            max-width: 360px;
        }

        /* Feature list */
        .auth-features {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
        }

        .auth-feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .auth-feature-item .check {
            width: 26px; height: 26px; flex-shrink: 0;
            background: rgba(74,124,220,0.15);
            border: 1px solid rgba(74,124,220,0.3);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
        }

        .auth-feature-item .check svg {
            width: 13px; height: 13px; stroke: #4a7cdc; stroke-width: 2.5;
            fill: none;
        }

        .auth-feature-item span {
            color: #94adc8;
            font-size: 0.88rem;
        }

        /* ── Right panel ── */
        .auth-right {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.5rem;
            background: #0f1623;
        }

        .auth-box {
            width: 100%;
            max-width: 420px;
        }

        /* Mobile logo (hidden on desktop) */
        .auth-mobile-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 2.5rem;
        }

        @media (min-width: 1024px) {
            .auth-mobile-logo { display: none; }
        }

        .auth-mobile-logo .logo-icon {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, #4a7cdc, #6366f1);
            border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
        }

        .auth-mobile-logo .logo-icon svg {
            width: 18px; height: 18px; fill: white;
        }

        .auth-mobile-logo span {
            font-size: 1.15rem;
            font-weight: 700;
            color: white;
        }

        .auth-heading {
            font-size: 1.75rem;
            font-weight: 800;
            color: #f1f5f9;
            letter-spacing: -0.03em;
            margin-bottom: 0.4rem;
        }

        .auth-subheading {
            color: #64748b;
            font-size: 0.9rem;
            margin-bottom: 2rem;
        }

        /* Form group */
        .form-group {
            margin-bottom: 1.15rem;
        }

        .form-group label {
            display: block;
            font-size: 0.825rem;
            font-weight: 600;
            color: #94a3b8;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            margin-bottom: 0.45rem;
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem 1rem;
            background: #1a2338;
            border: 1.5px solid #263045;
            border-radius: 10px;
            color: #e2e8f0;
            font-size: 0.95rem;
            font-family: 'Inter', sans-serif;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }

        .form-group input::placeholder { color: #3d5175; }

        .form-group input:focus {
            border-color: #4a7cdc;
            box-shadow: 0 0 0 3px rgba(74,124,220,0.12);
        }

        /* Error message */
        .form-error {
            color: #f87171;
            font-size: 0.8rem;
            margin-top: 0.35rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Remember + Forgot row */
        .auth-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 1.25rem 0 1.5rem;
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .remember-label input[type="checkbox"] {
            width: 16px; height: 16px;
            accent-color: #4a7cdc;
            cursor: pointer;
        }

        .remember-label span {
            font-size: 0.875rem;
            color: #64748b;
        }

        .forgot-link {
            font-size: 0.875rem;
            color: #4a7cdc;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.15s;
        }

        .forgot-link:hover { color: #6d9de8; }

        /* Submit button */
        .btn-login {
            width: 100%;
            padding: 0.85rem;
            background: linear-gradient(135deg, #4a7cdc, #6366f1);
            color: white;
            font-size: 0.95rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.1s, box-shadow 0.2s;
            letter-spacing: 0.01em;
            box-shadow: 0 4px 20px rgba(74,124,220,0.35);
        }

        .btn-login:hover {
            opacity: 0.92;
            box-shadow: 0 6px 24px rgba(74,124,220,0.45);
        }

        .btn-login:active { transform: scale(0.99); }

        /* Create account link */
        .create-account {
            text-align: center;
            margin-top: 1.1rem;
            font-size: 0.875rem;
            color: #4a6080;
        }

        .create-account a {
            color: #4a7cdc;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.15s;
        }

        .create-account a:hover { color: #6d9de8; }

        /* Divider */
        .auth-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 1.5rem 0;
        }

        .auth-divider::before, .auth-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #1e2d45;
        }

        .auth-divider span {
            font-size: 0.8rem;
            color: #3d5175;
            white-space: nowrap;
        }

        /* Demo credentials card */
        .demo-card {
            background: #131e30;
            border: 1px solid #1e2d45;
            border-radius: 10px;
            padding: 0.9rem 1rem;
        }

        .demo-card p {
            font-size: 0.78rem;
            color: #4a6080;
            margin-bottom: 0.5rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .demo-cred {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 0.3rem;
        }

        .demo-cred:last-child { margin-bottom: 0; }

        .demo-badge {
            font-size: 0.7rem;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            min-width: 46px;
            text-align: center;
        }

        .demo-badge.admin { background: rgba(74,124,220,0.15); color: #4a7cdc; }
        .demo-badge.user  { background: rgba(99,102,241,0.12); color: #818cf8; }

        .demo-cred span {
            font-size: 0.8rem;
            color: #5a7095;
            font-family: monospace;
        }

        /* Session status */
        .session-status {
            background: rgba(74,124,220,0.1);
            border: 1px solid rgba(74,124,220,0.2);
            color: #7ab0f5;
            padding: 0.6rem 0.9rem;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 1.25rem;
        }
    </style>
</head>
<body>

    <!-- Left decorative panel -->
    <div class="auth-left">
        <div class="auth-left-logo">
            <div class="logo-icon">
                <svg viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            </div>
            <span>TaskManager</span>
        </div>

        <div class="auth-left-hero">
            <h1>Manage tasks<br>with <span>clarity</span><br>and speed.</h1>
            <p>A clean, AI-assisted task management system built for teams who want to stay organized and move fast.</p>
        </div>

        <div class="auth-features">
            <div class="auth-feature-item">
                <div class="check">
                    <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <span>Assign, track and prioritize tasks in one place</span>
            </div>
            <div class="auth-feature-item">
                <div class="check">
                    <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <span>AI-generated summaries for every task</span>
            </div>
            <div class="auth-feature-item">
                <div class="check">
                    <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <span>Role-based access — Admin &amp; User views</span>
            </div>
            <div class="auth-feature-item">
                <div class="check">
                    <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <span>Dashboard analytics with live charts</span>
            </div>
        </div>
    </div>

    <!-- Right form panel -->
    <div class="auth-right">
        <div class="auth-box">

            <!-- Mobile logo -->
            <div class="auth-mobile-logo">
                <div class="logo-icon">
                    <svg viewBox="0 0 24 24" fill="white" width="18" height="18"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
                <span>TaskManager</span>
            </div>

            {{ $slot }}

        </div>
    </div>

</body>
</html>

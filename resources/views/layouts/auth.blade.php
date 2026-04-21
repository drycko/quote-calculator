<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Sign In') — {{ config('app.name', 'Quote Calculator') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Tracklyt Theme -->
    <link href="{{ asset('vendor/tracklyt-bs5-theme/css/variables.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/tracklyt-bs5-theme/css/theme.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/tracklyt-bs5-theme/css/components.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/tracklyt-bs5-theme/css/utilities.css') }}" rel="stylesheet">

    <style>
        html, body { height: 100%; }
        body {
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 1rem;
        }
        .auth-card {
            width: 100%;
            max-width: 420px;
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 20px 60px rgba(0,0,0,.2);
            overflow: hidden;
        }
        .auth-header {
            background: linear-gradient(135deg, var(--bs-primary, #4361ee) 0%, #2d47c1 100%);
            padding: 2rem;
            text-align: center;
            color: #fff;
        }
        .auth-header .brand-icon {
            width: 56px; height: 56px;
            background: rgba(255,255,255,.15);
            border-radius: 1rem;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto .75rem;
            font-size: 1.6rem;
        }
        .auth-body { padding: 2rem; }
        .auth-footer {
            border-top: 1px solid #f0f0f0;
            padding: .75rem 2rem;
            text-align: center;
            background: #fafafa;
            font-size: .8rem;
            color: #999;
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="auth-card">
        <div class="auth-header">
            <div class="brand-icon">
                <i class="bi bi-calculator-fill"></i>
            </div>
            <h5 class="mb-0 fw-bold">{{ config('app.name', 'Quote Calculator') }}</h5>
            <p class="mb-0 opacity-75 small mt-1">@yield('auth-subtitle', 'Internal Staff Portal')</p>
        </div>

        <div class="auth-body">
            @yield('content')
        </div>

        <div class="auth-footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>

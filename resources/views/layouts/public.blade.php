<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Quote Calculator') — {{ config('app.name', 'Quote Calculator') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Tracklyt Theme -->
    <link href="{{ asset('vendor/tracklyt-bs5-theme/css/variables.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/tracklyt-bs5-theme/css/theme.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/tracklyt-bs5-theme/css/components.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/tracklyt-bs5-theme/css/utilities.css') }}" rel="stylesheet">

    @stack('styles')
</head>
<body class="bg-light">

{{-- Top bar --}}
<nav class="navbar navbar-light bg-white border-bottom shadow-sm">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ route('welcome') }}">
            <span class="bg-primary text-white rounded p-1 lh-1" style="font-size:.9rem;">
                <i class="bi bi-calculator-fill"></i>
            </span>
            {{ config('app.name', 'Quote Calculator') }}
        </a>
        <div class="d-flex align-items-center gap-3">
            @yield('nav-actions')
            <a href="{{ route('login') }}" class="text-muted small">
                <i class="bi bi-person-lock me-1"></i>Staff
            </a>
        </div>
    </div>
</nav>

{{-- Flash messages --}}
@if(session('success') || session('error') || $errors->any())
<div class="container-fluid px-4 mt-3">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-0 py-2">
            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-0 py-2">
            <i class="bi bi-exclamation-circle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-0 py-2">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
</div>
@endif

{{-- Page content --}}
<main class="py-4">
    <div class="@yield('container-class', 'container-fluid px-4')">
        @yield('content')
    </div>
</main>

<footer class="border-top bg-white py-3 text-center text-muted small mt-4">
    &copy; {{ date('Y') }} {{ config('app.name') }}
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>

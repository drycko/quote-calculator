<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Quote Calculator'))</title>

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
<body>

<div class="d-flex" id="wrapper">

    {{-- ===== SIDEBAR ===== --}}
    <div id="sidebar-wrapper">
        <div class="sidebar-heading p-4">
            <div class="d-flex align-items-center">
                <i class="bi bi-calculator-fill fs-3 text-white me-3"></i>
                <div>
                    <h6 class="mb-0 text-white fw-bold">{{ config('app.name') }}</h6>
                    <small class="text-white-50">Internal Tool</small>
                </div>
            </div>
        </div>

        <div class="px-3 py-2">
            <div class="sidebar-section-header">Quoting</div>

            <a href="{{ route('quotes.index') }}"
               class="sidebar-nav-link {{ request()->routeIs('quotes.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text me-2"></i>Quotes
            </a>

            <a href="{{ route('templates.index') }}"
               class="sidebar-nav-link {{ request()->routeIs('templates.*') ? 'active' : '' }}">
                <i class="bi bi-grid me-2"></i>Templates
            </a>

            <div class="sidebar-section-header">Account</div>

            <a href="{{ route('logout') }}"
               class="sidebar-nav-link"
               onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();">
                <i class="bi bi-box-arrow-right me-2"></i>Logout
            </a>
            <form id="sidebar-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        </div>
    </div>
    {{-- /sidebar --}}

    {{-- ===== PAGE CONTENT ===== --}}
    <div id="page-content-wrapper">

        {{-- Top navbar --}}
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
            <div class="container-fluid">
                <button class="btn btn-sm btn-outline-secondary" id="sidebarToggle" type="button">
                    <i class="bi bi-list"></i>
                </button>

                <div class="navbar-nav ms-auto d-flex flex-row gap-2 align-items-center">
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="dropdown">
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2"
                                 style="width:32px;height:32px;">
                                <span class="text-white fw-bold small">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                </span>
                            </div>
                            {{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item text-danger"
                                   href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        {{-- Flash messages --}}
        <div class="container-fluid px-4 pt-3">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>

        {{-- Main content --}}
        <main class="container-fluid p-4">
            @yield('content')
        </main>

    </div>
    {{-- /page-content-wrapper --}}

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Tracklyt Theme JS -->
<script src="{{ asset('vendor/tracklyt-bs5-theme/js/theme.js') }}"></script>

@stack('scripts')
</body>
</html>

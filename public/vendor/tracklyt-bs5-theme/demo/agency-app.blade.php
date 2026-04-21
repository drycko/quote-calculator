{{--
    DEMO: Agency App — Teal Sidebar Layout
    =======================================
    Copy this into resources/views/layouts/app.blade.php (or a child view).
    Swap asset() paths to match your project's public/vendor location.

    Key classes:
        #wrapper                   — flex container; toggled class slides sidebar out on mobile
        #sidebar-wrapper           — 260 px fixed-width sidebar; background uses --sidebar-agency-bg
        #page-content-wrapper      — main area, width: 100%
        .sidebar-heading           — header strip at top of sidebar (darker shade)
        .sidebar-nav-link          — every nav anchor inside the sidebar
        .sidebar-nav-link.active   — current page highlight
        .sidebar-section-header    — small uppercase label between nav groups
        .sidebar-logo              — logo img (inverted to white automatically)
        #sidebarToggle             — button that calls theme.js sidebar toggle
--}}
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Agency App — Layout Demo</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Theme CSS (swap ../css/ for {{ asset('vendor/tracklyt-bs5-theme/css/') }} in Laravel) -->
    <link href="../css/variables.css" rel="stylesheet">
    <link href="../css/theme.css" rel="stylesheet">
    <link href="../css/components.css" rel="stylesheet">
    <link href="../css/utilities.css" rel="stylesheet">

    @stack('styles')
</head>
<body>

{{-- ===== OUTER WRAPPER — flex row, overflow hidden ===== --}}
<div class="d-flex" id="wrapper">

    {{-- ===== SIDEBAR ===== --}}
    <div id="sidebar-wrapper">

        {{-- Logo / brand strip --}}
        <div class="sidebar-heading p-4">
            <div class="d-flex align-items-center">
                {{-- Swap the icon for an <img class="sidebar-logo"> for a real logo --}}
                <i class="bi bi-grid-3x3-gap-fill fs-3 text-white me-3"></i>
                <div>
                    <h6 class="mb-0 text-white fw-bold">MyApp</h6>
                    <small class="text-white-50">Workspace</small>
                </div>
            </div>
        </div>

        {{-- Nav links --}}
        <div class="px-3 py-2">

            {{-- Section label --}}
            <div class="sidebar-section-header">Main</div>

            <a href="#" class="sidebar-nav-link active">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard
            </a>
            <a href="#" class="sidebar-nav-link">
                <i class="bi bi-briefcase me-2"></i>Projects
            </a>
            <a href="#" class="sidebar-nav-link">
                <i class="bi bi-people me-2"></i>Clients
            </a>
            <a href="#" class="sidebar-nav-link">
                <i class="bi bi-clock me-2"></i>Time Entries
            </a>

            <div class="sidebar-section-header">Finance</div>

            <a href="#" class="sidebar-nav-link">
                <i class="bi bi-receipt me-2"></i>Invoices
            </a>
            <a href="#" class="sidebar-nav-link">
                <i class="bi bi-file-earmark-text me-2"></i>Quotes
            </a>

            <div class="sidebar-section-header">Account</div>

            <a href="#" class="sidebar-nav-link">
                <i class="bi bi-gear me-2"></i>Settings
            </a>
            <a href="#" class="sidebar-nav-link">
                <i class="bi bi-box-arrow-right me-2"></i>Logout
            </a>

        </div>
    </div>
    {{-- /sidebar --}}

    {{-- ===== PAGE CONTENT ===== --}}
    <div id="page-content-wrapper">

        {{-- Top navbar --}}
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
            <div class="container-fluid">
                {{-- Hamburger — #sidebarToggle is picked up by theme.js --}}
                <button class="btn btn-xs btn-outline-secondary" id="sidebarToggle" type="button">
                    <i class="bi bi-list"></i>
                </button>

                <div class="navbar-nav ms-auto d-flex flex-row gap-2 align-items-center">
                    <button class="btn btn-xs btn-sm btn-outline-secondary position-relative">
                        <i class="bi bi-bell"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">3</span>
                    </button>
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="dropdown">
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2"
                                 style="width:32px;height:32px;">
                                <span class="text-white fw-bold small">JD</span>
                            </div>
                            Jane Doe
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        {{-- Main content --}}
        <main class="container-fluid p-4">

            {{-- Page header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-1" style="font-size:.875rem;">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </nav>
                    <h1 class="h3 mb-0">Dashboard</h1>
                </div>
                <button class="btn btn-xs btn-primary">
                    <i class="bi bi-plus me-1"></i>New Project
                </button>
            </div>

            {{-- ===== STAT CARDS ===== --}}
            {{--
                .stat-card          — base card with icon, value, label
                .stat-card-icon     — coloured circle icon wrapper
                .stat-card-value    — large number
                .stat-card-label    — descriptive label underneath
                Wrap in Bootstrap grid columns for layout.
            --}}
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-card-icon bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-clock"></i>
                        </div>
                        <div class="stat-card-value">142 h</div>
                        <div class="stat-card-label">Hours this month</div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-card-icon bg-success bg-opacity-10 text-success">
                            <i class="bi bi-briefcase"></i>
                        </div>
                        <div class="stat-card-value">8</div>
                        <div class="stat-card-label">Active projects</div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-card-icon bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-receipt"></i>
                        </div>
                        <div class="stat-card-value">$4,200</div>
                        <div class="stat-card-label">Outstanding invoices</div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-card-icon bg-info bg-opacity-10 text-info">
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="stat-card-value">12</div>
                        <div class="stat-card-label">Active clients</div>
                    </div>
                </div>
            </div>

            {{-- ===== GRADIENT CARD ===== --}}
            {{--
                .gradient-card.gradient-primary / gradient-success / gradient-danger
                .price-display — large styled number inside the card
            --}}
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="gradient-card gradient-primary">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="mb-1 opacity-75">Monthly Revenue</p>
                                <div class="price-display">$18,400</div>
                            </div>
                            <i class="bi bi-graph-up-arrow fs-2 opacity-50"></i>
                        </div>
                        <small class="opacity-75"><i class="bi bi-arrow-up me-1"></i>12% vs last month</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="gradient-card gradient-success">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="mb-1 opacity-75">Collected this month</p>
                                <div class="price-display">$14,200</div>
                            </div>
                            <i class="bi bi-check-circle fs-2 opacity-50"></i>
                        </div>
                        <small class="opacity-75">5 payments received</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="gradient-card gradient-danger">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="mb-1 opacity-75">Overdue</p>
                                <div class="price-display">$2,100</div>
                            </div>
                            <i class="bi bi-exclamation-circle fs-2 opacity-50"></i>
                        </div>
                        <small class="opacity-75">2 invoices overdue</small>
                    </div>
                </div>
            </div>

            {{-- ===== STATUS BADGES + TABLE ===== --}}
            {{--
                .status-badge.pending / success / active / failed / rejected / processing
                Wrap in a normal Bootstrap card for the table container.
            --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">Recent Invoices</div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Client</th>
                                <th>Amount</th>
                                <th>Due</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-muted small">INV-001</td>
                                <td>Acme Corp</td>
                                <td>$1,200</td>
                                <td>Apr 10, 2026</td>
                                <td><span class="status-badge pending">Pending</span></td>
                                <td><a href="#" class="btn btn-xs btn-sm btn-outline-primary">View</a></td>
                            </tr>
                            <tr>
                                <td class="text-muted small">INV-002</td>
                                <td>Globex Ltd</td>
                                <td>$3,400</td>
                                <td>Mar 28, 2026</td>
                                <td><span class="status-badge success">Paid</span></td>
                                <td><a href="#" class="btn btn-xs btn-sm btn-outline-primary">View</a></td>
                            </tr>
                            <tr>
                                <td class="text-muted small">INV-003</td>
                                <td>Initech</td>
                                <td>$900</td>
                                <td>Mar 15, 2026</td>
                                <td><span class="status-badge failed">Overdue</span></td>
                                <td><a href="#" class="btn btn-xs btn-sm btn-outline-primary">View</a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>
    {{-- /page-content-wrapper --}}

</div>
{{-- /wrapper --}}

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Theme JS — handles #sidebarToggle → #wrapper.toggled and tooltips -->
<script src="../js/theme.js"></script>

@stack('scripts')
</body>
</html>

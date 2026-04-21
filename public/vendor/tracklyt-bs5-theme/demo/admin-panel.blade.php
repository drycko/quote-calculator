{{--
    DEMO: Admin Panel — Dark Slate Sidebar Layout
    ==============================================
    Copy this into resources/views/admin/layout.blade.php (or a child view).
    Swap asset() paths to match your project's public/vendor location.

    Key classes:
        body.admin-layout          — activates dark sidebar tokens (--sidebar-admin-*)
        #wrapper                   — flex container; .toggled slides sidebar out on mobile
        #sidebar-wrapper           — 260 px sidebar; colour comes from .admin-layout override in components.css
        .admin-nav-link            — every nav anchor; dark slate variant of sidebar-nav-link
        .admin-nav-link.active     — current page highlight (blue accent)
        .sidebar-divider           — <hr> between nav groups
        .admin-card                — elevated card with hover lift
        .admin-card-header         — white header strip with bottom border
        .step-item.active/.completed — onboarding/wizard step state
        .timeline-item             — activity feed row
        #sidebarToggle             — button picked up by theme.js
--}}
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Panel — Layout Demo</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Theme CSS -->
    <link href="../css/variables.css" rel="stylesheet">
    <link href="../css/theme.css" rel="stylesheet">
    <link href="../css/components.css" rel="stylesheet">
    <link href="../css/utilities.css" rel="stylesheet">

    @stack('styles')
</head>
{{-- body.admin-layout switches #sidebar-wrapper to --sidebar-admin-bg (#1e293b dark slate) --}}
<body class="admin-layout">

<div class="d-flex" id="wrapper">

    {{-- ===== ADMIN SIDEBAR ===== --}}
    <div class="border-end" id="sidebar-wrapper">

        {{-- Brand strip --}}
        <div class="sidebar-heading p-4">
            <div class="d-flex align-items-center">
                <div class="bg-primary rounded d-flex align-items-center justify-content-center me-3"
                     style="width:36px;height:36px;">
                    <i class="bi bi-shield-check fs-5 text-white"></i>
                </div>
                <div>
                    <h6 class="mb-0 text-white fw-bold">MyApp</h6>
                    <small class="text-white-50">Admin Panel</small>
                </div>
            </div>
        </div>

        {{-- Nav --}}
        <div class="list-group list-group-flush px-3 py-2">

            <a href="#" class="list-group-item list-group-item-action admin-nav-link border-0 active">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard
            </a>

            <div class="px-3 pt-3 pb-1">
                <small class="text-white-50 text-uppercase" style="font-size:.75rem;font-weight:600;letter-spacing:.05em;">Management</small>
            </div>

            <a href="#" class="list-group-item list-group-item-action admin-nav-link border-0">
                <i class="bi bi-building me-2"></i>Organisations
            </a>
            <a href="#" class="list-group-item list-group-item-action admin-nav-link border-0">
                <i class="bi bi-rocket-takeoff me-2"></i>Onboarding
            </a>
            <a href="#" class="list-group-item list-group-item-action admin-nav-link border-0">
                <i class="bi bi-plus-circle me-2"></i>Create New
            </a>

            <div class="px-3 pt-3 pb-1">
                <small class="text-white-50 text-uppercase" style="font-size:.75rem;font-weight:600;letter-spacing:.05em;">Billing</small>
            </div>

            <a href="#" class="list-group-item list-group-item-action admin-nav-link border-0">
                <i class="bi bi-credit-card-2-front me-2"></i>Subscriptions
            </a>
            <a href="#" class="list-group-item list-group-item-action admin-nav-link border-0">
                <i class="bi bi-diagram-3 me-2"></i>Plans
            </a>

            <div class="px-3 pt-3 pb-1">
                <small class="text-white-50 text-uppercase" style="font-size:.75rem;font-weight:600;letter-spacing:.05em;">Support</small>
            </div>

            <a href="#" class="list-group-item list-group-item-action admin-nav-link border-0">
                <i class="bi bi-book me-2"></i>Knowledge Base
            </a>
            <a href="#" class="list-group-item list-group-item-action admin-nav-link border-0">
                <i class="bi bi-bug me-2"></i>Bug Reports
            </a>

            <div class="px-3 pt-3 pb-1">
                <small class="text-white-50 text-uppercase" style="font-size:.75rem;font-weight:600;letter-spacing:.05em;">System</small>
            </div>

            <a href="#" class="list-group-item list-group-item-action admin-nav-link border-0">
                <i class="bi bi-shield-lock me-2"></i>Access Control
            </a>
            <a href="#" class="list-group-item list-group-item-action admin-nav-link border-0">
                <i class="bi bi-gear-wide-connected me-2"></i>Platform Settings
            </a>

            {{-- .sidebar-divider styles the HR with correct border colour --}}
            <hr class="sidebar-divider">

            <div class="px-3 pt-1 pb-1">
                <small class="text-white-50 text-uppercase" style="font-size:.75rem;font-weight:600;letter-spacing:.05em;">Account</small>
            </div>

            <a href="#" class="list-group-item list-group-item-action admin-nav-link border-0">
                <i class="bi bi-person-circle me-2"></i>My Profile
            </a>
        </div>
    </div>
    {{-- /sidebar --}}

    {{-- ===== PAGE CONTENT ===== --}}
    <div id="page-content-wrapper">

        {{-- Top navbar --}}
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
            <div class="container-fluid">
                <button class="btn btn-xs btn-outline-secondary" id="sidebarToggle" type="button">
                    <i class="bi bi-list"></i>
                </button>
                <div class="navbar-nav ms-auto">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#"
                           data-bs-toggle="dropdown">
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2"
                                 style="width:32px;height:32px;">
                                <span class="text-white fw-bold">A</span>
                            </div>
                            Admin User
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">Platform Admin</h6></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <main class="container-fluid p-4">

            {{-- Page header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Admin Dashboard</h1>
                <a href="#" class="btn btn-xs btn-primary btn-sm">
                    <i class="bi bi-plus me-1"></i>Create Organisation
                </a>
            </div>

            {{-- ===== STAT CARDS ===== --}}
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-card-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-building"></i></div>
                        <div class="stat-card-value">24</div>
                        <div class="stat-card-label">Active organisations</div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-card-icon bg-success bg-opacity-10 text-success"><i class="bi bi-people"></i></div>
                        <div class="stat-card-value">187</div>
                        <div class="stat-card-label">Total users</div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-card-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-credit-card"></i></div>
                        <div class="stat-card-value">$12,840</div>
                        <div class="stat-card-label">MRR</div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-card-icon bg-danger bg-opacity-10 text-danger"><i class="bi bi-bug"></i></div>
                        <div class="stat-card-value">3</div>
                        <div class="stat-card-label">Open bug reports</div>
                    </div>
                </div>
            </div>

            <div class="row g-4">

                {{-- ===== ADMIN CARD with onboarding step list ===== --}}
                {{--
                    .admin-card          — elevated card, hover lift effect
                    .admin-card-header   — white header with bottom border
                    .admin-card-body     — padded body

                    .step-item           — single step row
                    .step-item.active    — current step (blue label)
                    .step-item.completed — done step (green label)
                    .step-connector      — indented line connecting steps
                --}}
                <div class="col-lg-5">
                    <div class="admin-card">
                        <div class="admin-card-header d-flex justify-content-between align-items-center">
                            <span>Onboarding Progress — Acme Corp</span>
                            <span class="badge bg-warning text-dark">Step 3 / 5</span>
                        </div>
                        <div class="admin-card-body">

                            <div class="step-item completed mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-success rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0"
                                         style="width:28px;height:28px;">
                                        <i class="bi bi-check text-white small"></i>
                                    </div>
                                    <div class="step-content">
                                        <div class="fw-semibold small">Organisation Details</div>
                                        <div class="text-muted" style="font-size:.8rem;">Name, timezone, currency</div>
                                    </div>
                                </div>
                            </div>

                            <div class="step-item completed mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-success rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0"
                                         style="width:28px;height:28px;">
                                        <i class="bi bi-check text-white small"></i>
                                    </div>
                                    <div class="step-content">
                                        <div class="fw-semibold small">Invite Team Members</div>
                                        <div class="text-muted" style="font-size:.8rem;">3 users invited</div>
                                    </div>
                                </div>
                            </div>

                            <div class="step-item active mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0"
                                         style="width:28px;height:28px;">
                                        <span class="text-white fw-bold small">3</span>
                                    </div>
                                    <div class="step-content">
                                        <div class="fw-semibold small">Subscription Plan</div>
                                        <div class="text-muted" style="font-size:.8rem;">Choose a plan to continue</div>
                                    </div>
                                </div>
                            </div>

                            <div class="step-item mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="border rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0"
                                         style="width:28px;height:28px;">
                                        <span class="text-muted small">4</span>
                                    </div>
                                    <div class="step-content">
                                        <div class="fw-semibold small text-muted">Preferences</div>
                                    </div>
                                </div>
                            </div>

                            <div class="step-item">
                                <div class="d-flex align-items-center">
                                    <div class="border rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0"
                                         style="width:28px;height:28px;">
                                        <span class="text-muted small">5</span>
                                    </div>
                                    <div class="step-content">
                                        <div class="fw-semibold small text-muted">Report Types</div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3">
                                <div class="progress" style="height:6px;">
                                    <div class="progress-bar bg-primary" style="width:40%"></div>
                                </div>
                                <small class="text-muted">40% complete</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===== ADMIN CARD with timeline ===== --}}
                {{--
                    .timeline-item          — activity row
                    .timeline-item.completed  — green heading
                    .timeline-item.current    — blue bold heading
                    Last item hides the connecting border-start line via CSS.
                --}}
                <div class="col-lg-7">
                    <div class="admin-card h-100">
                        <div class="admin-card-header">Recent Activity</div>
                        <div class="admin-card-body">

                            <div class="timeline-item completed">
                                <div class="d-flex">
                                    <div class="border-start border-2 border-success me-3 ps-3 flex-grow-1">
                                        <div class="timeline-content">
                                            <h6 class="mb-1 small">Acme Corp — onboarded</h6>
                                            <p class="text-muted mb-0" style="font-size:.8rem;">All 5 setup steps completed.</p>
                                            <small class="text-muted">2 hours ago</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="timeline-item current mt-3">
                                <div class="d-flex">
                                    <div class="border-start border-2 border-primary me-3 ps-3 flex-grow-1">
                                        <div class="timeline-content">
                                            <h6 class="mb-1 small">Globex Ltd — subscription upgraded</h6>
                                            <p class="text-muted mb-0" style="font-size:.8rem;">Moved from Starter to Pro plan.</p>
                                            <small class="text-muted">5 hours ago</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="timeline-item mt-3">
                                <div class="d-flex">
                                    <div class="border-start border-2 border-secondary me-3 ps-3 flex-grow-1">
                                        <div class="timeline-content">
                                            <h6 class="mb-1 small text-muted">Bug report #47 — opened</h6>
                                            <p class="text-muted mb-0" style="font-size:.8rem;">Invoice PDF generation fails for non-USD.</p>
                                            <small class="text-muted">Yesterday</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>{{-- /row --}}
        </main>
    </div>
    {{-- /page-content-wrapper --}}

</div>
{{-- /wrapper --}}

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/theme.js"></script>
@stack('scripts')
</body>
</html>

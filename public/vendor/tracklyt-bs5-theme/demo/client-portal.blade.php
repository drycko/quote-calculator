{{--
    DEMO: Client Portal Layout
    ==========================
    Copy this into resources/views/layouts/client.blade.php (or a child view).
    Swap asset() paths to match your project's public/vendor location.

    Key classes:
        body.client-portal  — scopes .stat-card left-border accent to --client-accent
        .client-navbar      — gradient top navbar (start: --client-nav-gradient-start, end: --client-nav-gradient-end)
        .client-nav-link    — transparent white text link in the navbar
        .client-nav-link.active — white text + semi-transparent white background
        .client-portal .stat-card — gets left border using --client-accent (overrides default stat-card)

    To change the navbar gradient, override in your own stylesheet:
        :root { --client-nav-gradient-start: #0ea5e9; --client-nav-gradient-end: #6366f1; }
--}}
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Client Portal — Layout Demo</title>

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
{{-- body.client-portal scopes the .stat-card left-border accent --}}
<body class="client-portal">

    {{-- ===== GRADIENT NAVBAR ===== --}}
    {{--
        .client-navbar applies the CSS gradient from variables.css tokens.
        .client-nav-link / .client-nav-link.active handle text colour + hover background.
        On mobile, wrap links in a collapsible div or reduce to icons.
    --}}
    <nav class="client-navbar">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">

                {{-- Brand --}}
                <a href="#" class="text-white text-decoration-none">
                    <h4 class="mb-0 fw-bold">
                        <i class="bi bi-building me-2"></i>Acme Corp — Portal
                    </h4>
                </a>

                {{-- Nav links --}}
                <div class="d-flex gap-1 flex-wrap">
                    <a href="#" class="client-nav-link active">
                        <i class="bi bi-speedometer2 me-1"></i>Dashboard
                    </a>
                    <a href="#" class="client-nav-link">
                        <i class="bi bi-file-earmark-text me-1"></i>Quotes
                    </a>
                    <a href="#" class="client-nav-link">
                        <i class="bi bi-briefcase me-1"></i>Projects
                    </a>
                    <a href="#" class="client-nav-link">
                        <i class="bi bi-receipt me-1"></i>Invoices
                    </a>
                    <a href="#" class="client-nav-link">
                        <i class="bi bi-box-arrow-right me-1"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    {{-- ===== MAIN CONTENT ===== --}}
    <main class="py-4">
        <div class="container">

            {{-- Flash messages --}}
            {{-- <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle me-2"></i>Payment received. Thank you!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div> --}}

            {{-- Page heading --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h4 mb-1">Welcome back, Jane</h2>
                    <p class="text-muted mb-0 small">Here's an overview of your account.</p>
                </div>
            </div>

            {{-- ===== STAT CARDS ===== --}}
            {{--
                Inside .client-portal, .stat-card gets a left border via --client-accent.
                The icon, value, and label classes are the same as the agency app.
            --}}
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="stat-card">
                        <div class="stat-card-icon bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-receipt"></i>
                        </div>
                        <div class="stat-card-value">$3,200</div>
                        <div class="stat-card-label">Outstanding balance</div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="stat-card">
                        <div class="stat-card-icon bg-success bg-opacity-10 text-success">
                            <i class="bi bi-check2-circle"></i>
                        </div>
                        <div class="stat-card-value">$18,500</div>
                        <div class="stat-card-label">Total paid to date</div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="stat-card">
                        <div class="stat-card-icon bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-briefcase"></i>
                        </div>
                        <div class="stat-card-value">4</div>
                        <div class="stat-card-label">Active projects</div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="stat-card">
                        <div class="stat-card-icon bg-info bg-opacity-10 text-info">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                        <div class="stat-card-value">2</div>
                        <div class="stat-card-label">Pending quotes</div>
                    </div>
                </div>
            </div>

            <div class="row g-4">

                {{-- ===== Recent invoices ===== --}}
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                            Recent Invoices
                            <a href="#" class="btn btn-xs btn-sm btn-outline-primary">View all</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Description</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-muted small">INV-009</td>
                                        <td>Monthly retainer — March</td>
                                        <td>$2,000</td>
                                        <td><span class="status-badge pending">Pending</span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted small">INV-008</td>
                                        <td>Website redesign phase 2</td>
                                        <td>$1,200</td>
                                        <td><span class="status-badge success">Paid</span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted small">INV-007</td>
                                        <td>Monthly retainer — February</td>
                                        <td>$2,000</td>
                                        <td><span class="status-badge success">Paid</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- ===== Active projects ===== --}}
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white fw-semibold">Active Projects</div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                    <div>
                                        <div class="fw-medium small">Website Redesign</div>
                                        <div class="text-muted" style="font-size:.8rem;">Due Apr 30, 2026</div>
                                    </div>
                                    <div style="width:80px;">
                                        <div class="progress" style="height:6px;">
                                            <div class="progress-bar" style="width:65%;background:var(--client-accent);"></div>
                                        </div>
                                        <small class="text-muted">65%</small>
                                    </div>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                    <div>
                                        <div class="fw-medium small">Mobile App MVP</div>
                                        <div class="text-muted" style="font-size:.8rem;">Due Jun 15, 2026</div>
                                    </div>
                                    <div style="width:80px;">
                                        <div class="progress" style="height:6px;">
                                            <div class="progress-bar" style="width:20%;background:var(--client-accent);"></div>
                                        </div>
                                        <small class="text-muted">20%</small>
                                    </div>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                    <div>
                                        <div class="fw-medium small">SEO Retainer</div>
                                        <div class="text-muted" style="font-size:.8rem;">Ongoing</div>
                                    </div>
                                    <span class="badge bg-success">Active</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>{{-- /row --}}
        </div>
    </main>

    {{-- Footer --}}
    <footer class="bg-light py-3 mt-5">
        <div class="container text-center text-muted">
            <small>&copy; {{ date('Y') }} MyApp. All rights reserved.</small>
        </div>
    </footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/theme.js"></script>
@stack('scripts')
</body>
</html>

{{--
    DEMO: Auth / Guest Layout
    =========================
    Copy this into resources/views/layouts/guest.blade.php (or a child view that extends it).
    Swap asset() paths to match your project's public/vendor location.

    Key classes:
        body                — background comes from --auth-bg (#f8fafc) set in variables.css;
                              flex centred via theme.css body overrides on guest pages
        .auth-card          — white rounded card, overflow hidden
        .auth-header        — top section of the card; top accent bar via ::before pseudo element
        .auth-header::before — 4 px colour bar using --auth-primary (#3b82f6)
        .auth-logo          — large icon or image above the heading
        .auth-body          — padded form area

    Bootstrap form overrides (defined in guest section of components.css):
        .form-control       — rounded corners, soft border, blue focus ring
        .btn-primary        — uses --auth-primary, hover lifts with box-shadow

    To preview alert states, uncomment the alert divs below.
--}}
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Auth — Layout Demo</title>

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
<body>

    {{-- ===== TOP NAV (optional — included in guest layout) ===== --}}
    <nav class="position-fixed top-0 start-0 w-100 py-3"
         style="background:white;box-shadow:0 1px 3px rgba(0,0,0,.1);z-index:1000;">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <a href="#" class="text-decoration-none d-flex align-items-center">
                    <i class="bi bi-grid-3x3-gap-fill me-2" style="color:var(--auth-primary);font-size:1.5rem;"></i>
                    <span class="fw-bold" style="color:var(--auth-dark);font-size:1.25rem;">MyApp</span>
                </a>
                <a href="#" class="text-muted text-decoration-none small">
                    <i class="bi bi-arrow-left me-1"></i>Back to home
                </a>
            </div>
        </div>
    </nav>

    {{-- ===== AUTH CARD ===== --}}
    <div class="container" style="padding-top:80px;">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-5">

                <div class="auth-card">

                    {{-- Header — contains logo icon and welcome text --}}
                    <div class="auth-header">
                        {{-- .auth-logo sets font-size + colour from --auth-primary --}}
                        <div class="auth-logo">
                            <i class="bi bi-grid-3x3-gap-fill"></i>
                        </div>
                        <h2>Welcome back</h2>
                        <p>Sign in to your account to continue</p>
                    </div>

                    {{-- Body — the actual form --}}
                    <div class="auth-body">

                        {{-- Uncomment to preview flash messages --}}
                        {{-- <div class="alert alert-success mb-4">Password reset successfully. Please sign in.</div> --}}
                        {{-- <div class="alert alert-info mb-4">Check your email for a magic link.</div> --}}

                        <form method="POST" action="#" class="needs-validation" novalidate>
                            @csrf

                            <div class="mb-3">
                                <label class="form-label" for="email">Email address</label>
                                <input id="email" type="email" name="email" class="form-control"
                                       placeholder="you@example.com" required autofocus>
                                <div class="invalid-feedback">Please enter a valid email.</div>
                            </div>

                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-label mb-0" for="password">Password</label>
                                    <a href="#" style="font-size:.85rem;">Forgot password?</a>
                                </div>
                                <input id="password" type="password" name="password" class="form-control mt-1"
                                       placeholder="••••••••" required>
                                <div class="invalid-feedback">Password is required.</div>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-xs btn-primary">
                                    Sign in
                                </button>
                            </div>

                            <hr>

                            <p class="text-center mb-0 text-muted small">
                                Don't have an account? <a href="#">Create one</a>
                            </p>
                        </form>

                    </div>
                </div>

                {{-- ===== MAGIC LINK VARIANT ===== --}}
                {{--
                    Swap the form above for this if you use passwordless login.
                --}}
                {{-- <div class="auth-card mt-3">
                    <div class="auth-header">
                        <div class="auth-logo"><i class="bi bi-envelope-open"></i></div>
                        <h2>Check your email</h2>
                        <p>We sent a sign-in link to you@example.com</p>
                    </div>
                    <div class="auth-body text-center">
                        <p class="text-muted">Click the link in the email to sign in. The link expires in 15 minutes.</p>
                        <a href="#" class="btn btn-xs btn-outline-primary">Resend link</a>
                    </div>
                </div> --}}

            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/theme.js"></script>
@stack('scripts')
</body>
</html>

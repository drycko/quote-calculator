<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Quote Calculator') }} — Website Quoting Tool</title>

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
        :root { --accent: #4361ee; --accent-dark: #2d47c1; }

        body { font-family: 'Segoe UI', system-ui, sans-serif; }

        /* ── Navbar ─────────────────────────────────── */
        .landing-nav {
            position: sticky; top: 0; z-index: 100;
            background: rgba(255,255,255,.95);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid #f0f0f0;
            padding: .75rem 0;
        }

        /* ── Hero ───────────────────────────────────── */
        .hero {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
            color: #fff;
            padding: 80px 0 90px;
            position: relative;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute; inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        .hero-badge {
            display: inline-block;
            background: rgba(255,255,255,.15);
            border: 1px solid rgba(255,255,255,.3);
            border-radius: 50px;
            padding: .3rem .9rem;
            font-size: .8rem;
            margin-bottom: 1.25rem;
        }
        .hero h1 { font-size: clamp(2rem, 5vw, 3.25rem); font-weight: 800; line-height: 1.15; }
        .hero .lead { font-size: 1.1rem; opacity: .85; max-width: 560px; margin: 0 auto; }
        .btn-hero-primary {
            background: #fff; color: var(--accent);
            font-weight: 700; padding: .75rem 2rem;
            border-radius: .5rem; border: none;
            font-size: 1rem;
            box-shadow: 0 4px 20px rgba(0,0,0,.15);
            transition: transform .15s, box-shadow .15s;
        }
        .btn-hero-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(0,0,0,.2);
            color: var(--accent-dark);
        }
        .btn-hero-outline {
            background: transparent; color: #fff;
            font-weight: 600; padding: .75rem 1.75rem;
            border-radius: .5rem; border: 2px solid rgba(255,255,255,.5);
            font-size: 1rem;
            transition: border-color .15s, background .15s;
        }
        .btn-hero-outline:hover { border-color: #fff; background: rgba(255,255,255,.1); color: #fff; }

        /* ── Stats strip ────────────────────────────── */
        .stats-strip {
            background: #fff;
            border-bottom: 1px solid #f0f0f0;
            padding: 1.25rem 0;
        }
        .stat-item { text-align: center; }
        .stat-item .num { font-size: 1.6rem; font-weight: 800; color: var(--accent); }
        .stat-item .lbl { font-size: .8rem; color: #888; }

        /* ── Features ───────────────────────────────── */
        .section-label {
            font-size: .75rem; font-weight: 700; letter-spacing: 1.5px;
            text-transform: uppercase; color: var(--accent);
        }
        .feature-icon {
            width: 52px; height: 52px;
            background: #eef0ff; border-radius: .75rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.35rem; color: var(--accent);
            margin-bottom: 1rem;
        }

        /* ── Steps ──────────────────────────────────── */
        .step-num {
            width: 40px; height: 40px; min-width: 40px;
            background: var(--accent); color: #fff;
            border-radius: 50%; display: flex; align-items: center;
            justify-content: center; font-weight: 700; font-size: 1rem;
        }

        /* ── CTA band ───────────────────────────────── */
        .cta-band {
            background: linear-gradient(135deg, #1a1a2e 0%, var(--accent-dark) 100%);
            color: #fff; padding: 64px 0;
        }

        /* ── Footer ─────────────────────────────────── */
        footer { background: #1a1a2e; color: #aaa; padding: 1.5rem 0; font-size: .85rem; }
        footer a { color: #aaa; text-decoration: none; }
        footer a:hover { color: #fff; }
    </style>
</head>
<body>

{{-- ══════════════════════════ NAVBAR ══════════════════════════ --}}
<nav class="landing-nav">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="{{ route('welcome') }}" class="text-decoration-none d-flex align-items-center gap-2">
            <span class="bg-primary text-white rounded p-1 lh-1" style="font-size:.9rem;">
                <i class="bi bi-calculator-fill"></i>
            </span>
            <span class="fw-bold text-dark" style="font-size:1.05rem;">{{ config('app.name', 'Quote Calculator') }}</span>
        </a>
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('calculator.create') }}" class="btn btn-primary btn-sm fw-semibold px-3">
                Start a Quote
            </a>
            <a href="{{ route('login') }}" class="text-muted small text-decoration-none">
                <i class="bi bi-person-lock me-1"></i>Staff
            </a>
        </div>
    </div>
</nav>

{{-- ══════════════════════════ HERO ═════════════════════════════ --}}
<section class="hero text-center">
    <div class="container position-relative">
        <div class="hero-badge">
            <i class="bi bi-lightning-charge-fill me-1"></i> Fast &amp; accurate website quoting
        </div>
        <h1 class="mb-3">
            Build a Professional<br>Website Quote in Minutes
        </h1>
        <p class="lead mb-5">
            Add your line items, pick from our template library,<br class="d-none d-md-block">
            and download a polished PDF — no account needed.
        </p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="{{ route('calculator.create') }}" class="btn-hero-primary text-decoration-none">
                <i class="bi bi-calculator me-2"></i>Start a Free Quote
            </a>
            <a href="#how-it-works" class="btn-hero-outline text-decoration-none">
                How it works
            </a>
        </div>
    </div>
</section>

{{-- ══════════════════════════ STATS ════════════════════════════ --}}
<div class="stats-strip d-none d-md-block">
    <div class="container">
        <div class="row g-0">
            <div class="col stat-item border-end">
                <div class="num">130+</div>
                <div class="lbl">Line item templates</div>
            </div>
            <div class="col stat-item border-end">
                <div class="num">3</div>
                <div class="lbl">Quote phases</div>
            </div>
            <div class="col stat-item border-end">
                <div class="num">Auto</div>
                <div class="lbl">Markup calculation</div>
            </div>
            <div class="col stat-item">
                <div class="num">PDF</div>
                <div class="lbl">One-click export</div>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════ FEATURES ═════════════════════════ --}}
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-label mb-2">Features</div>
            <h2 class="fw-bold h3 mb-0">Everything you need to quote confidently</h2>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="bg-white rounded-3 shadow-sm p-4 h-100">
                    <div class="feature-icon"><i class="bi bi-grid-3x3-gap-fill"></i></div>
                    <h5 class="fw-bold mb-2">Template Library</h5>
                    <p class="text-muted mb-0">
                        Over 130 pre-built line items covering Design, Development, Plugins, SEO, Content and more — pick and go.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="bg-white rounded-3 shadow-sm p-4 h-100">
                    <div class="feature-icon"><i class="bi bi-percent"></i></div>
                    <h5 class="fw-bold mb-2">Smart Markup Tiers</h5>
                    <p class="text-muted mb-0">
                        Automatic markup applied by quote size — from 10% to 22.5% — with a separate 10% flat rate for plugins.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="bg-white rounded-3 shadow-sm p-4 h-100">
                    <div class="feature-icon"><i class="bi bi-currency-exchange"></i></div>
                    <h5 class="fw-bold mb-2">Multi-Currency Support</h5>
                    <p class="text-muted mb-0">
                        Enter costs in USD, EUR or GBP with a conversion rate — totals are always shown in ZAR.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="bg-white rounded-3 shadow-sm p-4 h-100">
                    <div class="feature-icon"><i class="bi bi-layout-three-columns"></i></div>
                    <h5 class="fw-bold mb-2">Three Phase Structure</h5>
                    <p class="text-muted mb-0">
                        Organise your quote into Design, Development, and Plugins &amp; PM phases for clear, professional presentation.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="bg-white rounded-3 shadow-sm p-4 h-100">
                    <div class="feature-icon"><i class="bi bi-file-earmark-pdf-fill"></i></div>
                    <h5 class="fw-bold mb-2">PDF Download</h5>
                    <p class="text-muted mb-0">
                        Generate a polished, branded PDF quote ready to send to your client — one click, no design skills needed.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="bg-white rounded-3 shadow-sm p-4 h-100">
                    <div class="feature-icon"><i class="bi bi-bookmark-star"></i></div>
                    <h5 class="fw-bold mb-2">Shareable Link</h5>
                    <p class="text-muted mb-0">
                        Every quote gets a unique URL — bookmark it, share it, or return later to update without losing your work.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════ HOW IT WORKS ═════════════════════ --}}
<section id="how-it-works" class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-label mb-2">How it works</div>
            <h2 class="fw-bold h3 mb-0">Three steps to a finished quote</h2>
        </div>

        <div class="row g-4 justify-content-center">
            <div class="col-md-4 text-center">
                <div class="d-flex justify-content-center mb-3">
                    <div class="step-num">1</div>
                </div>
                <h5 class="fw-bold mb-2">Start your quote</h5>
                <p class="text-muted">Enter the client name, choose Web / Manual / iLead, add your name and hit <em>Start</em>.</p>
            </div>
            <div class="col-md-4 text-center">
                <div class="d-flex justify-content-center mb-3">
                    <div class="step-num">2</div>
                </div>
                <h5 class="fw-bold mb-2">Add line items</h5>
                <p class="text-muted">Pick from the template library or add custom items. Set rates, quantities, currencies and plugin flags.</p>
            </div>
            <div class="col-md-4 text-center">
                <div class="d-flex justify-content-center mb-3">
                    <div class="step-num">3</div>
                </div>
                <h5 class="fw-bold mb-2">Recalculate &amp; download</h5>
                <p class="text-muted">Hit Recalculate to apply markup and VAT, then download your branded PDF quote.</p>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════ CTA BAND ═════════════════════════ --}}
<section class="cta-band text-center">
    <div class="container">
        <h2 class="fw-bold mb-3">Ready to build your first quote?</h2>
        <p class="opacity-75 mb-4">No sign-up required. Start building now and download your PDF in minutes.</p>
        <a href="{{ route('calculator.create') }}" class="btn-hero-primary text-decoration-none d-inline-block">
            <i class="bi bi-calculator me-2"></i>Start a Free Quote
        </a>
    </div>
</section>

{{-- ══════════════════════════ FOOTER ═══════════════════════════ --}}
<footer>
    <div class="container d-flex flex-wrap justify-content-between align-items-center gap-3">
        <span>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</span>
        <a href="{{ route('login') }}">
            <i class="bi bi-person-lock me-1"></i>Staff Login
        </a>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


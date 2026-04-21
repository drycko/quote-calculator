# tracklyt-bs5-theme

A modern, mobile-first Bootstrap 5 theme for Laravel applications with multiple layout contexts — a main app sidebar, an admin panel sidebar, an auth/guest layout, and a client portal. CSS class and variable names carry the `tracklyt-` prefix (rename on adoption for a new project); the architecture and patterns are fully reusable.

**Stack:** Bootstrap 5.2.3 · Bootstrap Icons 1.11.3 · Vanilla JS (no extra dependencies)

---

## Features

- **CSS custom properties** — all colours, spacing, shadows, radii, and transitions defined as tokens in `variables.css`
- **Bootstrap overrides** — tasteful reskins of cards, buttons, forms, tables, modals, and alerts in `theme.css`
- **Ready-made components** — stat cards, gradient cards, price displays, status badges, tier cards, sidebar layouts, auth cards, client navbar, and more in `components.css`
- **Utility classes** — text gradients, background fills, border helpers, shadow levels, and spacing shortcuts in `utilities.css`
- **Sidebar layouts** — two pre-built sidebar variants (teal agency, dark-slate admin) that use the shared `#wrapper / #sidebar-wrapper` Bootstrap wrapper pattern, toggled via `theme.js`
- **Auth layout** — centred card layout with top-accent bar for guest/login pages
- **Client portal layout** — gradient navbar and stat-card accent for a client-facing portal
- **Interactive JS** — sidebar toggle (both `#sidebarToggle` and `[data-sidebar-toggle]`), Bootstrap tooltips, scroll-triggered animations, form validation, price counter animation, scroll-direction navbar effects, toast notifications, and clipboard helpers exposed on `window.Tracklyt`
- **Fluid typography** — font-size tokens use `clamp()` for smooth scaling
- **Mobile-first** — all breakpoint overrides build upward from small screens

---

## File Structure

```
tracklyt-bs5-theme/
├── css/
│   ├── variables.css     # CSS custom properties (colours, spacing, radii, shadows, sidebar palettes, auth/client vars)
│   ├── theme.css         # Bootstrap 5 overrides (cards, buttons, forms, tables, modals, alerts, body)
│   ├── components.css    # Pre-built components — stat cards, gradient cards, status badges, sidebars,
│   │                     # auth layout, client navbar, timeline, admin cards, empty states, skeletons …
│   └── utilities.css     # Utility classes (text-gradient, bg-*, border-*, shadow-*, spacing)
├── demo/
│   ├── agency-app.blade.php    # Agency teal sidebar layout demo
│   ├── admin-panel.blade.php   # Admin dark-slate sidebar layout demo
│   ├── auth.blade.php          # Auth / guest login card demo
│   └── client-portal.blade.php # Client portal navbar + cards demo
└── js/
    └── theme.js          # TracklytTheme IIFE + window.Tracklyt helpers
```

### Demo files

The `demo/` directory contains four self-contained HTML files (`.blade.php` extension so they drop straight into a Laravel `resources/views/` tree). Each file:

- loads Bootstrap and Bootstrap Icons from CDN
- loads the theme CSS and JS via `../css/` / `../js/` relative paths (or swap for `{{ asset(...) }}` in Laravel)
- renders a realistic page for that layout variant
- includes inline comments explaining the key classes used

Open any file directly in a browser from the `demo/` folder to preview, or copy it into `resources/views/` and wire up a route to use it as a starting point.

### CSS load order

Load the files in this order so that `theme.css` can reference variables defined in `variables.css`:

```html
<link href="vendor/tracklyt-bs5-theme/css/variables.css" rel="stylesheet">
<link href="vendor/tracklyt-bs5-theme/css/theme.css" rel="stylesheet">
<link href="vendor/tracklyt-bs5-theme/css/components.css" rel="stylesheet">
<link href="vendor/tracklyt-bs5-theme/css/utilities.css" rel="stylesheet">
```

Load `theme.js` **after** Bootstrap JS:

```html
<script src="bootstrap.bundle.min.js"></script>
<script src="vendor/tracklyt-bs5-theme/js/theme.js"></script>
```

---

## Layout Variants

The theme ships with four named layout variants. Each is activated by a class on `<body>` and/or by the colour tokens already set in `variables.css`.

### App Sidebar (Teal — Agency)

```html
<body>
  <div class="d-flex" id="wrapper">
    <div id="sidebar-wrapper"> … </div>
    <div id="page-content-wrapper"> … </div>
  </div>
</body>
```

Uses CSS custom properties: `--sidebar-agency-bg`, `--sidebar-agency-text`, etc.  
Nav links use the `.sidebar-nav-link` class. Section headers use `.sidebar-section-header`.  
Toggle button must have `id="sidebarToggle"` — `theme.js` toggles `.toggled` on `#wrapper`.

### Admin Sidebar (Dark Slate)

Same HTML structure. Add `class="admin-layout"` to `<body>`.

```html
<body class="admin-layout">
```

Nav links use `.admin-nav-link`. `.sidebar-divider` styles the `<hr>` separators.  
Additional admin components: `.admin-card`, `.admin-card-header`, `.admin-card-body`, `.step-item`, `.step-connector`, `.timeline-item`, `.team-member-card`.

### Auth / Guest Layout

Full-page centred card layout. No extra body class needed.

```html
<div class="auth-card">
  <div class="auth-header">
    <div class="auth-logo"><i class="bi bi-clock-history"></i></div>
    <h2>Sign in</h2>
  </div>
  <div class="auth-body">
    …form…
  </div>
</div>
```

Key tokens: `--auth-primary`, `--auth-bg`, `--auth-dark`.

### Client Portal

```html
<body class="client-portal">
  <nav class="client-navbar"> … </nav>
</body>
```

Nav links use `.client-nav-link`. The `.stat-card` inside `.client-portal` gets a coloured left border accent via `--client-accent`.

---

## Components

### Stat Card

```html
<div class="stat-card">
  <div class="stat-card-icon"><i class="bi bi-clock"></i></div>
  <div class="stat-card-value">42</div>
  <div class="stat-card-label">Hours this month</div>
</div>
```

### Gradient Card

```html
<div class="gradient-card gradient-primary">
  <h5>Total Revenue</h5>
  <p class="price-display">$12,400</p>
</div>
```

Gradient variants: `gradient-primary`, `gradient-success`, `gradient-danger`.

### Status Badge

```html
<span class="status-badge pending">Pending</span>
<span class="status-badge success">Paid</span>
<span class="status-badge active">Active</span>
<span class="status-badge failed">Failed</span>
```

### Empty State

```html
<div class="empty-state">
  <div class="empty-state-icon"><i class="bi bi-inbox"></i></div>
  <h5 class="empty-state-title">Nothing here yet</h5>
  <p class="empty-state-message">Create your first item to get started.</p>
</div>
```

### Skeleton Loader

```html
<div class="skeleton skeleton-text"></div>
<div class="skeleton skeleton-card"></div>
```

### Scroll-triggered Animation

Add `data-animate="fade-up"` to any element — it receives `animate-fade-up` when it enters the viewport.

---

## JavaScript API

`theme.js` exposes two globals after the DOM is ready.

### `window.TracklytTheme`

Auto-initialised. Methods you can call manually if needed:

| Method | Description |
|---|---|
| `TracklytTheme.initSidebar()` | Bind sidebar toggle handlers |
| `TracklytTheme.initTooltips()` | Bootstrap tooltip init on `[data-bs-toggle="tooltip"]` |
| `TracklytTheme.initAnimations()` | IntersectionObserver for `[data-animate]` elements |
| `TracklytTheme.initFormValidation()` | `.needs-validation` submit guard + real-time blur feedback |
| `TracklytTheme.initScrollEffects()` | Adds `scroll-up` / `scroll-down` class to `.navbar` |

### `window.Tracklyt`

Utility helpers:

| Method | Description |
|---|---|
| `Tracklyt.formatCurrency(amount, currency)` | `Intl.NumberFormat` currency string |
| `Tracklyt.formatNumber(number, decimals)` | Number with commas |
| `Tracklyt.toast(message, type, duration)` | Bootstrap toast notification |
| `Tracklyt.copyToClipboard(text, successMsg)` | Clipboard API with fallback |
| `Tracklyt.setButtonLoading(button, loading)` | Spinner state on buttons |
| `Tracklyt.scrollTo(element, offset)` | Smooth scroll with offset |
| `Tracklyt.debounce(func, wait)` | Debounce wrapper |
| `Tracklyt.throttle(func, limit)` | Throttle wrapper |
| `window.animatePrice(element, newValue, duration)` | Animated number counter |

---

## CSS Variables Reference

All tokens are defined on `:root` in `variables.css`.

### Brand colours
| Variable | Default | Use |
|---|---|---|
| `--tracklyt-primary` | `#2563eb` | Primary actions, links |
| `--tracklyt-secondary` | `#7c3aed` | Accent / secondary |
| `--tracklyt-success` | `#059669` | Positive states |
| `--tracklyt-danger` | `#dc2626` | Errors / destructive |
| `--tracklyt-warning` | `#f59e0b` | Warnings |

### Sidebar palettes
| Variable | Default |
|---|---|
| `--sidebar-agency-bg` | `#0f766e` |
| `--sidebar-agency-active-bg` | `#14b8a6` |
| `--sidebar-admin-bg` | `#1e293b` |
| `--sidebar-admin-active-accent` | `#3b82f6` |

Override any token in your own stylesheet or in a page-level `<style>` block after the theme is loaded.

---

## Customising for a New Project

1. Copy the `tracklyt-bs5-theme/` folder into your `public/vendor/` directory.
2. Load the four CSS files and `theme.js` in your base layout (order matters — see above).
3. Override brand tokens in a project-specific stylesheet:
   ```css
   :root {
     --tracklyt-primary: #your-colour;
     --sidebar-agency-bg: #your-sidebar-colour;
   }
   ```
4. Choose your layout variant (`admin-layout`, `client-portal`, or neither for the agency app).
5. Use component classes directly in your HTML — no build step required.

---

## Dependencies

| Dependency | Version | How to load |
|---|---|---|
| Bootstrap CSS | 5.2.3 | CDN or npm |
| Bootstrap JS bundle | 5.2.3 | CDN or npm (required for tooltips, modals, dropdowns) |
| Bootstrap Icons | 1.11.3 | CDN or npm (required for icon classes used in components) |
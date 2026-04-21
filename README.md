# Quote Calculator

A Laravel 12 web application that provides a public-facing pricing calculator for web agency quotes, with a separate authenticated backoffice for staff to manage and review submissions.

---

## Features

- **Public calculator** — clients build their own quote online with no account required. Shareable via a unique UUID token URL.
- **Template-driven line items** — 130+ pre-seeded templates covering design, development, plugins and PM across multiple project types (Web, Manual Adjust, iLead).
- **Automatic pricing** — two-pass calculation engine handles fixed, hourly, percentage and foreign-currency (converted) line items.
- **Markup tiers** — tiered markup applied automatically on admin quotes (not exposed to public clients).
- **VAT** — 15% VAT calculated and displayed separately.
- **PDF download** — dompdf-powered PDF export for both public and admin quotes.
- **Live currency conversion** — frankfurter.app (ECB data, no API key) fetches live rates; results cached for 24 hours with a configurable fallback.
- **Backoffice** — authenticated admin area to manage quotes and line item templates.

---

## Tech Stack

| Layer | Package |
|---|---|
| Framework | Laravel 12 |
| Auth scaffolding | laravel/ui |
| PDF generation | barryvdh/laravel-dompdf |
| Frontend | Bootstrap 5.2 + Bootstrap Icons 1.11 |
| Theme | Tracklyt BS5 Theme |
| Currency API | frankfurter.app (free, no key) |
| Database | MySQL (via Laravel Sail / WSL) |

---

## Local Setup

### Prerequisites

- WSL 2 (Ubuntu 24.04)
- Docker Desktop with Laravel Sail, **or** PHP 8.2+ and MySQL directly

### Steps

```bash
# 1. Clone and install dependencies
git clone <repo-url> quote-calculator
cd quote-calculator
composer install

# 2. Environment
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set your database credentials, then add:

```env
APP_CURRENCY=ZAR
DEFAULT_CONVERSION_RATE=18.50
```

```bash
# 3. Run migrations and seed templates
php artisan migrate
php artisan db:seed

# 4. Install PDF package (if not already in vendor/)
composer require barryvdh/laravel-dompdf

# 5. Publish pagination views (for Bootstrap 5 styling)
php artisan vendor:publish --tag=laravel-pagination

# 6. Register helpers autoload (already in composer.json)
composer dump-autoload

# 7. Build frontend assets
npm install && npm run build

# 8. Serve
php artisan serve
```

---

## Architecture

### Public Calculator (no auth)

| Route | Description |
|---|---|
| `GET /` | Landing page |
| `GET /calculator` | Start a new quote |
| `POST /calculator` | Create quote (type locked to `web`) |
| `GET /calculator/{token}` | View & edit quote by UUID token |
| `PUT /calculator/{token}` | Update client info |
| `POST /calculator/{token}/recalculate` | Recalculate totals |
| `GET /calculator/{token}/pdf` | Download PDF |
| `DELETE /calculator/{token}/items/{id}` | Remove a line item |

Public quotes are identified by `user_id = null`. No markup is applied and no markup figures are exposed in the UI or PDF.

### Admin Backoffice (`/quotes`, auth required)

| Route | Description |
|---|---|
| `GET /quotes` | All quotes (paginated) |
| `GET /quotes/create` | New quote form |
| `GET /quotes/{id}/edit` | Edit quote + manage line items |
| `POST /quotes/{id}/recalculate` | Recalculate with markup tiers |
| `GET /quotes/{id}/pdf` | Download PDF (includes markup) |
| `GET /templates` | Line item template list |
| `GET /templates/create` | New template |
| `GET /templates/{id}/edit` | Edit template |

---

## Pricing Engine

`App\Services\QuoteCalculator` runs a two-pass calculation:

**Pass 1** — fixed, hourly, and converted items are calculated first.

**Pass 2** — percentage items are calculated as a percentage of the Pass 1 subtotal.

### Markup Tiers (admin quotes only)

| Main subtotal | Markup |
|---|---|
| ≥ R 85,000 | 10% |
| R 55,000 – R 84,999 | 12% |
| R 35,500 – R 54,999 | 15% |
| R 12,500 – R 35,499 | 18% |
| < R 12,500 | 22.5% |

Plugins and PM items always receive a fixed **10% markup** regardless of tier.

Public quotes (`user_id = null`) skip markup entirely — totals are raw subtotals + 15% VAT.

---

## Line Item Calculation Types

| Type | How it works |
|---|---|
| `fixed` | `rate × 1` (quantity ignored) |
| `hourly` | `rate × quantity` |
| `percentage` | `percentage_value / 100 × pass-1 subtotal` |
| `converted` | `rate / conversion_rate` (converts foreign currency to ZAR) |

---

## Currency Conversion

`get_conversion_rate($from, $to)` in `app/Helpers/helpers.php`:

- Calls `https://api.frankfurter.app/latest?from=USD&to=ZAR`
- Caches result for **24 hours** using Laravel's cache driver
- Falls back to `DEFAULT_CONVERSION_RATE` env value if the API is unavailable

The template form has a live fetch button that calls the API directly from the browser and populates the conversion rate field.

---

## Key Files

```
app/
  Http/Controllers/
    CalculatorController.php   — public calculator flow
    QuoteController.php        — admin quote management
    LineItemController.php     — add/remove line items on admin quotes
    LineItemTemplateController.php — CRUD for templates
  Models/
    Quote.php
    Phase.php
    LineItem.php
    LineItemTemplate.php
  Services/
    QuoteCalculator.php        — pricing engine
  Helpers/
    helpers.php                — format_money(), get_conversion_rate(), etc.

resources/views/
  welcome.blade.php            — public landing page
  calculator/
    create.blade.php           — start a quote
    show.blade.php             — public calculator UI
  quotes/
    index.blade.php            — admin quote list
    create.blade.php           — admin new quote
    edit.blade.php             — admin quote editor
    pdf.blade.php              — PDF template
  templates/
    index.blade.php            — template list
    create.blade.php           — new template
    edit.blade.php             — edit template
    _form.blade.php            — shared form fields
  layouts/
    app.blade.php              — admin sidebar layout
    public.blade.php           — public top-bar layout
    auth.blade.php             — centered card layout (login)
```

---

## Seeded Templates

The database seeder populates **136 line item templates** sourced from the internal Excel pricing sheet, covering:

- UI/UX Design
- Frontend & Backend Development
- E-commerce & CMS
- Integrations & APIs
- Plugins, PM and Support

Run `php artisan db:seed` to load them.

---

## Staff Login

Navigate to `/login`. Staff accounts are created via `php artisan tinker`:

```php
App\Models\User::create([
    'name'     => 'Admin',
    'email'    => 'admin@example.com',
    'password' => bcrypt('password'),
]);
```

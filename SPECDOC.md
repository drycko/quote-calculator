Good call — this is the right moment to **update the specdoc**, not just patch code.

Below is your **clean, updated Specdoc v2**, aligned with the real workbook logic and production-ready architecture.

---

# Specdoc v2 — Quote Calculator (Laravel 12 + UI Auth)

---

## Core Philosophy

This is:

> **A rule-based pricing engine + internal dashboard**

NOT:

* a simple calculator
* not a flat CRUD system
* not a full ERP (yet)

---

## Key System Capabilities

* Modular line item system
* Multi-type calculation engine
* Dynamic markup tiers
* Currency conversion support
* Plugin cost separation (2-invoice model)
* Snapshot-based audit trail
* Template-driven quoting (Excel replacement)

---

# ⚙️ STEP 1 — Project Setup

*(unchanged)*

---

# STEP 2 — Auth System

Using Laravel UI + Bootstrap Auth:

```bash
sail composer require laravel/ui
artisan ui bootstrap --auth

snpm install && snpm run build
artisan migrate
```

---

# STEP 3 — Core Models

```bash
artisan make:model Quote -mcr
artisan make:model Phase -m
artisan make:model LineItem -m
artisan make:model LineItemTemplate -m
```

---

# 🗄️ STEP 4 — Database Design (UPDATED)

---

## Quotes Table

```php
$table->id();
$table->foreignId('user_id')->constrained()->cascadeOnDelete();

$table->string('client_name')->nullable();
$table->string('salesperson_name')->nullable();
$table->string('salesperson_email')->nullable();

$table->string('template_type')->default('web'); // web | manual | ilead

$table->decimal('subtotal', 10, 2)->default(0);
$table->decimal('main_total', 10, 2)->default(0);
$table->decimal('plugin_total', 10, 2)->default(0);

$table->decimal('markup_rate', 5, 2)->default(0);
$table->decimal('markup_amount', 10, 2)->default(0);

$table->decimal('vat', 10, 2)->default(0);
$table->decimal('total_ex_vat', 10, 2)->default(0);
$table->decimal('total_inc_vat', 10, 2)->default(0);

$table->json('snapshot')->nullable();

$table->timestamps();
```

---

## Phases Table

```php
$table->id();
$table->foreignId('quote_id')->constrained()->cascadeOnDelete();
$table->string('type'); // design, development, plugins_pm
$table->timestamps();
```

---

## Line Items Table (CRITICAL UPGRADE)

```php
$table->id();
$table->foreignId('phase_id')->constrained()->cascadeOnDelete();

$table->string('name');

$table->decimal('rate', 10, 2)->nullable();
$table->decimal('quantity', 10, 2)->default(1);

$table->string('calculation_type'); 
// fixed | hourly | percentage | converted

$table->decimal('percentage_value', 5, 2)->nullable();

$table->string('currency')->default('ZAR');
$table->decimal('conversion_rate', 10, 2)->nullable();

$table->boolean('is_plugin')->default(false);

$table->text('notes')->nullable();
$table->decimal('total', 10, 2)->default(0);

$table->timestamps();
```

---

## Line Item Templates Table (Excel Replacement Layer)

```php
$table->id();

$table->string('name');
$table->string('category'); // design/dev/plugin
$table->string('template_type'); // web | manual | ilead

$table->string('calculation_type');

$table->decimal('default_rate', 10, 2)->nullable();
$table->decimal('default_percentage', 5, 2)->nullable();

$table->string('currency')->nullable();
$table->decimal('conversion_rate', 10, 2)->nullable();

$table->boolean('is_plugin')->default(false);

$table->text('default_notes')->nullable();

$table->timestamps();
```

---

# STEP 5 — Calculation Engine (UPDATED CONCEPT)

## Two-Pass Calculation

### Pass 1:

* fixed
* hourly
* converted

### Pass 2:

* percentage-based items (depend on subtotal)

---

## 📐 Calculation Types

| Type       | Logic                        |
| ---------- | ---------------------------- |
| fixed      | rate                         |
| hourly     | rate × quantity              |
| percentage | subtotal × %                 |
| converted  | rate × conversion × quantity |

---

## Currency Conversion

Config-based:

```php
config/currency.php
```

```php
return [
  'USD' => 20,
  'EUR' => 20,
  'GBP' => 24,
];
```

---

# STEP 6 — Markup Engine (REPLACES COMMISSION)

## Dynamic Markup Tiers (Ex VAT)

| Range         | Markup |
| ------------- | ------ |
| 85,000+       | 10%    |
| 55,000–85,000 | 12%    |
| 35,500–55,000 | 15%    |
| 12,500–35,500 | 18%    |
| <12,500       | 22.5%  |

---

## Special Rules

* Plugins → fixed 10% markup
* Design concepts → 20% markup *(future extension)*

---

# STEP 7 — Plugin Separation (CRITICAL)

System MUST:

* Track plugin totals separately
* Apply plugin markup separately
* Allow **second invoice generation**

---

## Quote Fields

```php
main_total
plugin_total
```

---

# STEP 8 — Snapshot System

Each calculation stores:

```json
{
  "items": [...],
  "totals": {
    "subtotal": 0,
    "main_total": 0,
    "plugin_total": 0,
    "markup_rate": 0,
    "markup_amount": 0,
    "vat": 0,
    "total": 0
  }
}
```

---

# STEP 9 — Controller Responsibilities

* Create quote (attach user)
* Load templates
* Trigger recalculation
* Save overrides

---

# STEP 10 — Routes

```php
Route::middleware(['auth'])->group(function () {
  Route::resource('quotes', QuoteController::class);
  Route::post('/quotes/{quote}/recalculate', ...);
});
```

---

# STEP 11 — UI Structure (UPDATED)

---

## Template Selection (NEW)

User must choose:

* Web Template
* Manual Adjust
* iLead

---

## Quote Screen

### Sections:

* Client Info
* Template Type
* Phase Tabs

---

### Line Items Table (Enhanced)

* Name
* Calculation Type
* Rate
* Qty
* Currency
* % (if applicable)
* Include/Exclude toggle
* Total
* Notes

---

## Summary Panel

* Subtotal
* Main Total
* Plugin Total
* Markup
* VAT
* Final Total

---

# STEP 12 — Template System (IMPORTANT)

Templates act as:

> **Digitized Excel rows**

System must:

* Load templates by type
* Allow enabling/disabling items
* Allow overrides

---

# STEP 13 — Manual Adjust Mode

Allows:

* Override rates
* Override totals
* Skip automated logic when needed

---

# STEP 14 — Output

* Internal calculation view
* Client-facing PDF (later)
* Separate plugin invoice

---

# FINAL SYSTEM SUMMARY

You are building:

> **A modular, rule-driven pricing engine with dynamic calculation logic**

---

# What Changed From v1

| v1               | v2                |
| ---------------- | ----------------- |
| Flat calculation | Rule-based engine |
| Commission       | Dynamic markup    |
| Simple items     | Multi-type items  |
| No currency      | Currency-aware    |
| Single invoice   | Split invoices    |
| Static templates | Template engine   |

---

# Final Note

This spec now:

* Matches your workbook
* Prevents future rewrites
* Scales into SaaS

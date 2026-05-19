@extends('layouts.app')

@section('title', 'Edit Quote — ' . ($quote->client_name ?? 'Draft'))

@section('content')

@include('partials.choices-cdn')
{{-- ── Header ─────────────────────────────────────────────── --}}
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1" style="font-size:.875rem;">
                <li class="breadcrumb-item"><a href="{{ route('quotes.index') }}">Quotes</a></li>
                <li class="breadcrumb-item active">{{ $quote->client_name ?? 'Draft #' . $quote->id }}</li>
            </ol>
        </nav>
        <h1 class="h3 mb-0">
            {{ $quote->client_name ?? 'Draft Quote' }}
            <span class="badge bg-primary bg-opacity-10 text-primary ms-2" style="font-size:.65rem;vertical-align:middle;">
                {{ $quote->quote_number }}
            </span>
            <span class="badge bg-secondary bg-opacity-10 text-secondary text-uppercase ms-2" style="font-size:.65rem;vertical-align:middle;">
                {{ $quote->template_type }}
            </span>
            <span class="badge bg-info bg-opacity-10 text-info ms-2" style="font-size:.65rem;vertical-align:middle;">
                {{ \App\Models\Quote::statuses()[$quote->status] ?? $quote->status }}
            </span>
        </h1>
    </div>
    <div class="d-flex gap-2">
        {{-- Recalculate --}}
        <form action="{{ route('quotes.recalculate', $quote) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-success btn-sm">
                <i class="bi bi-arrow-repeat me-1"></i> Recalculate
            </button>
        </form>
        {{-- PDF --}}
        <a href="{{ route('quotes.pdf', $quote) }}" class="btn btn-outline-dark btn-sm">
            <i class="bi bi-file-earmark-pdf me-1"></i> PDF
        </a>
        {{-- Delete --}}
        <form action="{{ route('quotes.destroy', $quote) }}" method="POST"
              onsubmit="return confirm('Delete this quote?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-trash"></i>
            </button>
        </form>
    </div>
</div>

<div class="row g-4">

    {{-- ── LEFT COLUMN ──────────────────────────────────────── --}}
    <div class="col-xl-8">

        {{-- Client Info --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent fw-semibold border-bottom">
                <i class="bi bi-person me-2"></i>Client Information
            </div>
            <div class="card-body">
                <form action="{{ route('quotes.update', $quote) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Client Business Name</label>
                            <input type="text" class="form-control form-control-sm" name="client_name"
                                   value="{{ $quote->client_name }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Template Type</label>
                            <select class="form-select form-select-sm" name="template_type">
                                <option value="web"    {{ $quote->template_type === 'web'    ? 'selected' : '' }}>Web</option>
                                <option value="manual" {{ $quote->template_type === 'manual' ? 'selected' : '' }}>Manual Adjust</option>
                                <option value="ilead"  {{ $quote->template_type === 'ilead'  ? 'selected' : '' }}>iLead</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Status</label>
                            <select class="form-select form-select-sm" name="status">
                                @foreach(\App\Models\Quote::statuses() as $value => $label)
                                    <option value="{{ $value }}" {{ $quote->status === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Client Contact Name</label>
                            <input type="text" class="form-control form-control-sm" name="client_contact_name"
                                   value="{{ $quote->client_contact_name }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Client Contact Email</label>
                            <input type="email" class="form-control form-control-sm" name="client_contact_email"
                                   value="{{ $quote->client_contact_email }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Salesperson Name</label>
                            <input type="text" class="form-control form-control-sm" name="salesperson_name"
                                   value="{{ $quote->salesperson_name }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Salesperson Email</label>
                            <input type="email" class="form-control form-control-sm" name="salesperson_email"
                                   value="{{ $quote->salesperson_email }}">
                        </div>
                        <div class="col-12">
                            <div class="d-flex gap-4">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="apply_markup" value="0">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                           name="apply_markup" value="1" id="apply_markup"
                                           {{ $quote->apply_markup ? 'checked' : '' }}>
                                    <label class="form-check-label small fw-semibold" for="apply_markup">
                                        Apply markup
                                        <span class="text-muted fw-normal">(uncheck for cost-price quotes)</span>
                                    </label>
                                </div>
                                <div class="form-check form-switch">
                                    <input type="hidden" name="apply_vat" value="0">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                           name="apply_vat" value="1" id="apply_vat"
                                           {{ $quote->apply_vat ? 'checked' : '' }}>
                                    <label class="form-check-label small fw-semibold" for="apply_vat">
                                        Apply VAT (15%)
                                        <span class="text-muted fw-normal">(uncheck for VAT-exclusive quotes)</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-save me-1"></i> Save Info
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Phase Tabs --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom p-0">
                <ul class="nav nav-tabs border-0 px-3 pt-2" id="phaseTabs" role="tablist">
                    @foreach($quote->phases as $index => $phase)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $index === 0 ? 'active' : '' }}"
                                    id="tab-{{ $phase->type }}"
                                    data-bs-toggle="tab"
                                    data-bs-target="#phase-{{ $phase->id }}"
                                    type="button" role="tab">
                                @if($phase->type === 'design')
                                    <i class="bi bi-palette me-1"></i> Design
                                @elseif($phase->type === 'development')
                                    <i class="bi bi-code-slash me-1"></i> Development
                                @else
                                    <i class="bi bi-plugin me-1"></i> Plugins & PM
                                @endif
                                <span class="badge bg-secondary bg-opacity-10 text-secondary ms-1">
                                    {{ $phase->lineItems->count() }}
                                </span>
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="tab-content p-3" id="phaseTabsContent">
                @foreach($quote->phases as $index => $phase)
                <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}"
                     id="phase-{{ $phase->id }}" role="tabpanel">

                    {{-- Line Items Table --}}
                    @if($phase->lineItems->isNotEmpty())
                    <div class="table-responsive mb-3">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th style="width:110px;">Type</th>
                                    <th style="width:90px;">Rate</th>
                                    <th style="width:70px;">Qty</th>
                                    <th style="width:70px;">%</th>
                                    <th style="width:60px;">Curr</th>
                                    <th style="width:55px;" class="text-center">Plugin</th>
                                    <th style="width:100px;" class="text-end">Total</th>
                                    <th style="width:80px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($phase->lineItems as $item)
                                <tr>
                                    <td>
                                        <div class="fw-semibold" style="font-size:.875rem;">{{ $item->name }}</div>
                                        @if($item->notes)
                                            <div class="text-muted" style="font-size:.75rem;">{{ $item->notes }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border" style="font-size:.7rem;">
                                            {{ $item->calculation_type }}
                                        </span>
                                    </td>
                                    <td class="text-muted small">{{ $item->rate ? format_money($item->rate, $item->currency) : '—' }}</td>
                                    <td class="text-muted small">{{ $item->calculation_type !== 'fixed' && $item->calculation_type !== 'percentage' ? $item->quantity : '—' }}</td>
                                    <td class="text-muted small">{{ $item->percentage_value ? $item->percentage_value.'%' : '—' }}</td>
                                    <td class="text-muted small">{{ $item->currency ?? 'ZAR' }}</td>
                                    <td class="text-center">
                                        @if($item->is_plugin)
                                            <i class="bi bi-check-circle-fill text-success" style="font-size:.85rem;"></i>
                                        @else
                                            <i class="bi bi-dash text-muted" style="font-size:.85rem;"></i>
                                        @endif
                                    </td>
                                    <td class="text-end fw-semibold small">{{ format_money($item->total) }}</td>
                                    <td class="text-end">
                                        <div class="btn-group" role="group">
                                            {{-- Move to another phase --}}
                                            <div class="dropdown">
                                                <button type="button"
                                                        class="btn btn-xs btn-sm btn-outline-secondary py-0 px-1 dropdown-toggle"
                                                        data-bs-toggle="dropdown"
                                                        data-bs-popper-config='{"strategy":"fixed"}'
                                                        title="Move to phase">
                                                    <i class="bi bi-arrow-left-right" style="font-size:.85rem;"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end" style="min-width:140px;">
                                                    @foreach($quote->phases as $targetPhase)
                                                        @if($targetPhase->id !== $phase->id)
                                                            <li>
                                                                <form action="{{ route('line-items.move', $item->id) }}"
                                                                      method="POST" class="d-inline">
                                                                    @csrf @method('PATCH')
                                                                    <input type="hidden" name="phase_id" value="{{ $targetPhase->id }}">
                                                                    <button type="submit" class="dropdown-item" style="font-size:.8rem;">
                                                                        @if($targetPhase->type === 'design')
                                                                            <i class="bi bi-palette me-1"></i> Design
                                                                        @elseif($targetPhase->type === 'development')
                                                                            <i class="bi bi-code-slash me-1"></i> Development
                                                                        @else
                                                                            <i class="bi bi-plug me-1"></i> Plugins & PM
                                                                        @endif
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            </div>
                                            {{-- Edit --}}
                                            <button type="button"
                                                    class="btn btn-xs btn-sm btn-outline-primary"
                                                    onclick="openEditModal({{ $item->id }}, {{ json_encode($item->only(['name','calculation_type','rate','quantity','percentage_value','currency','conversion_rate','is_plugin','notes'])) }})">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            {{-- Delete --}}
                                            <button type="button"
                                                    class="btn btn-xs btn-sm btn-outline-danger"
                                                    onclick="deleteItem({{ $item->id }})">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                        <p class="text-muted small my-3">No line items yet. Add one below.</p>
                    @endif

                    {{-- Add Line Item Form --}}
                    <div class="border rounded p-3 bg-light">
                        <div class="fw-semibold small mb-2">
                            <i class="bi bi-plus-circle me-1"></i> Add Line Item
                        </div>
                        <form action="{{ route('line-items.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="phase_id" value="{{ $phase->id }}">

                            @php
                                $phaseCategories = [
                                    'design'      => ['design'],
                                    'development' => ['dev'],
                                    'plugins_pm'  => ['plugin', 'pm'],
                                ];
                                $allowedCats = $phaseCategories[$phase->type] ?? [];
                                $phaseTemplates = $templates->filter(fn($items, $cat) => in_array($cat, $allowedCats));
                            @endphp

                            <div class="row g-2 mb-2">
                                {{-- Line item picker --}}
                                <div class="col-md-6">
                                    <label class="form-label small">Load from line items</label>
                                    <select class="form-select form-select-sm lineitem-picker-select" id="template-picker-{{ $phase->id }}"
                                            onchange="fillFromTemplate(this, {{ $phase->id }})">
                                        <option value="">— pick a line item —</option>
                                        @foreach($phaseTemplates as $cat => $items)
                                            <optgroup label="{{ ucfirst($cat) }}">
                                                @foreach($items as $tpl)
                                                    <option value="{{ $tpl->id }}"
                                                            data-name="{{ $tpl->name }}"
                                                            data-type="{{ $tpl->calculation_type }}"
                                                            data-rate="{{ $tpl->default_rate }}"
                                                            data-pct="{{ $tpl->default_percentage }}"
                                                            data-currency="{{ $tpl->currency }}"
                                                            data-conversion="{{ $tpl->conversion_rate }}"
                                                            data-plugin="{{ $tpl->is_plugin ? '1' : '0' }}"
                                                            data-notes="{{ $tpl->default_notes }}">
                                                        {{ $tpl->name }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm"
                                           name="name" id="name-{{ $phase->id }}" required>
                                </div>
                            </div>

                            <div class="row g-2 mb-2">
                                <div class="col">
                                    <label class="form-label small">Calc Type</label>
                                    <select class="form-select form-select-sm" name="calculation_type"
                                            id="calc-{{ $phase->id }}">
                                        <option value="fixed">Fixed</option>
                                        <option value="hourly">Hourly</option>
                                        <option value="percentage">Percentage</option>
                                        <option value="converted">Converted</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <label class="form-label small">Rate</label>
                                    <input type="number" step="0.01" class="form-control form-control-sm"
                                           name="rate" id="rate-{{ $phase->id }}" placeholder="0.00">
                                </div>
                                <div class="col">
                                    <label class="form-label small">Qty</label>
                                    <input type="number" step="0.01" class="form-control form-control-sm"
                                           name="quantity" value="1" id="qty-{{ $phase->id }}">
                                </div>
                                <div class="col">
                                    <label class="form-label small">%</label>
                                    <input type="number" step="0.01" class="form-control form-control-sm"
                                           name="percentage_value" id="pct-{{ $phase->id }}" placeholder="0.00">
                                </div>
                                <div class="col">
                                    <label class="form-label small">Currency</label>
                                    <select class="form-select form-select-sm" name="currency" id="curr-{{ $phase->id }}">
                                        <option value="ZAR">ZAR</option>
                                        <option value="USD">USD</option>
                                        <option value="EUR">EUR</option>
                                        <option value="GBP">GBP</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <label class="form-label small">Conv. Rate</label>
                                    <input type="number" step="0.01" class="form-control form-control-sm"
                                           name="conversion_rate" id="conv-{{ $phase->id }}" placeholder="20">
                                </div>
                                <div class="col-auto d-flex align-items-end">
                                    <div class="form-check mb-1">
                                        <input type="hidden" name="is_plugin" value="0">
                                        <input class="form-check-input" type="checkbox"
                                               name="is_plugin" value="1" id="plugin-{{ $phase->id }}">
                                        <label class="form-check-label small" for="plugin-{{ $phase->id }}">Plugin</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-2 mb-2">
                                <div class="col">
                                    <label class="form-label small">Notes</label>
                                    <input type="text" class="form-control form-control-sm"
                                           name="notes" id="notes-{{ $phase->id }}" placeholder="Optional">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus me-1"></i> Add Item
                            </button>
                        </form>
                    </div>

                </div>
                @endforeach
            </div>
        </div>

    </div>
    {{-- /left column --}}

    {{-- ── RIGHT COLUMN — Summary Panel ────────────────────── --}}
    <div class="col-xl-4">
        <div class="card border-0 shadow-sm sticky-top" style="top:1.5rem;">
            <div class="card-header bg-transparent fw-semibold border-bottom">
                <i class="bi bi-calculator me-2"></i>Summary
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <tbody>
                        <tr>
                            <td class="text-muted ps-3">Subtotal</td>
                            <td class="text-end pe-3 fw-semibold">{{ format_money($quote->subtotal) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-3">Main Total</td>
                            <td class="text-end pe-3">{{ format_money($quote->main_total) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-3">Plugin Total</td>
                            <td class="text-end pe-3">{{ format_money($quote->plugin_total) }}</td>
                        </tr>
                        <tr class="table-light">
                            <td class="ps-3 text-muted">Markup ({{ $quote->markup_rate }}%)</td>
                            <td class="text-end pe-3">{{ format_money($quote->markup_amount) }}</td>
                        </tr>
                        <tr>
                            <td class="ps-3 text-muted">Total ex VAT</td>
                            <td class="text-end pe-3">{{ format_money($quote->total_ex_vat) }}</td>
                        </tr>
                        <tr>
                            <td class="ps-3 text-muted">VAT (15%)</td>
                            <td class="text-end pe-3">{!! $quote->apply_vat ? format_money($quote->vat) : '<span class="text-muted small">excl.</span>' !!}</td>
                        </tr>
                        <tr class="table-success">
                            <td class="ps-3 fw-bold">Total {{ $quote->apply_vat ? 'inc VAT' : 'ex VAT' }}</td>
                            <td class="text-end pe-3 fw-bold fs-5">{{ format_money($quote->total_inc_vat) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-transparent text-center row">
                <div class="col-6">
                    <form action="{{ route('quotes.recalculate', $quote) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-arrow-repeat me-1"></i> Update Totals
                        </button>
                    </form>
                </div>
                <div class="col-6">
                    <button type="button" class="btn btn-primary w-100"
                            data-bs-toggle="modal" data-bs-target="#emailQuoteModal">
                        <i class="bi bi-envelope me-1"></i> Email Client
                    </button>
                </div>
            </div>
        </div>

        {{-- Markup tier reference --}}
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-transparent fw-semibold border-bottom small">
                <i class="bi bi-info-circle me-1"></i> Markup Tiers
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0 small">
                    <tbody>
                        <tr><td class="ps-3 text-muted">R50 001 – R300 000</td><td class="text-end pe-3">15%</td></tr>
                        <tr><td class="ps-3 text-muted">R30 001 – R50 000</td><td class="text-end pe-3">18%</td></tr>
                        <tr><td class="ps-3 text-muted">R12 501 – R30 000</td><td class="text-end pe-3">22.5%</td></tr>
                        <tr><td class="ps-3 text-muted">Below R12 500</td><td class="text-end pe-3">10%</td></tr>
                        
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    {{-- /right column --}}

</div>

{{-- Email Quote Modal --}}
<div class="modal fade" id="emailQuoteModal" tabindex="-1" aria-labelledby="emailQuoteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('quotes.email', $quote) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="emailQuoteModalLabel">Email Quote to Client</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Recipient Email</label>
                            <input type="email" name="to_email"
                                   class="form-control @error('to_email') is-invalid @enderror"
                                   value="{{ old('to_email', $quote->client_contact_email) }}"
                                   placeholder="client@example.com" required>
                            @error('to_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Subject</label>
                            <input type="text" name="subject"
                                   class="form-control @error('subject') is-invalid @enderror"
                                   value="{{ old('subject', 'Your quote ' . $quote->quote_number) }}" required>
                            @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Message</label>
                            <textarea name="message" rows="7"
                                      class="form-control @error('message') is-invalid @enderror"
                                      required>{{ old('message', "Hi " . ($quote->client_contact_name ?: $quote->client_name ?: 'there') . ",\n\nThank you for the opportunity to quote on your project. You can view and approve your quote using the secure link below.\n\nPlease let me know if you have any questions.") }}</textarea>
                            @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Quote Link</label>
                            <input type="text" class="form-control form-control-sm"
                                   value="{{ route('calculator.show', $quote->public_token) }}" readonly>
                            <div class="form-text">
                                Sending this email will mark the quote as Sent to Client unless it is already converted.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send me-1"></i> Send Email
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

{{-- ── Edit Line Item Modal ──────────────────────────────────── --}}
<div class="modal fade" id="editItemModal" tabindex="-1" aria-labelledby="editItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editItemForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editItemModalLabel">Edit Line Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" id="ei-name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Calc Type</label>
                            <select class="form-select" name="calculation_type" id="ei-calc">
                                <option value="fixed">Fixed</option>
                                <option value="hourly">Hourly</option>
                                <option value="percentage">Percentage</option>
                                <option value="converted">Converted</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Rate</label>
                            <input type="number" step="0.01" class="form-control" name="rate" id="ei-rate" placeholder="0.00">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Qty</label>
                            <input type="number" step="0.01" class="form-control" name="quantity" id="ei-qty">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">%</label>
                            <input type="number" step="0.01" class="form-control" name="percentage_value" id="ei-pct" placeholder="0.00">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Currency</label>
                            <select class="form-select" name="currency" id="ei-curr">
                                <option value="ZAR">ZAR</option>
                                <option value="USD">USD</option>
                                <option value="EUR">EUR</option>
                                <option value="GBP">GBP</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Conv. Rate</label>
                            <input type="number" step="0.01" class="form-control" name="conversion_rate" id="ei-conv" placeholder="20">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <div class="form-check mb-1">
                                <input type="hidden" name="is_plugin" value="0">
                                <input class="form-check-input" type="checkbox" name="is_plugin" value="1" id="ei-plugin">
                                <label class="form-check-label fw-semibold" for="ei-plugin">Plugin</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes</label>
                            <input type="text" class="form-control" name="notes" id="ei-notes" placeholder="Optional">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check me-1"></i> Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Restore active tab from URL hash on page load
document.addEventListener('DOMContentLoaded', function () {
    const hash = window.location.hash;
    if (hash) {
        const target = document.querySelector('[data-bs-target="' + hash + '"]');
        if (target) {
            bootstrap.Tab.getOrCreateInstance(target).show();
        }
    }
    
    // choices js for lineitem pickers
    document.querySelectorAll('select.lineitem-picker-select').forEach(function(select) {
        new Choices(select, {
            searchEnabled: true,
            itemSelectText: '',
            placeholder: true,
            placeholderValue: '— add line item —',
            searchPlaceholderValue: 'Type to search...',
            shouldSort: false,
        });
    });
});

function fillFromTemplate(select, phaseId) {
    const opt = select.options[select.selectedIndex];
    if (!opt.value) return;

    document.getElementById('name-' + phaseId).value        = opt.dataset.name    ?? '';
    document.getElementById('rate-' + phaseId).value        = opt.dataset.rate    ?? '';
    document.getElementById('pct-'  + phaseId).value        = opt.dataset.pct     ?? '';
    document.getElementById('conv-' + phaseId).value        = opt.dataset.conversion ?? '';
    document.getElementById('notes-'+ phaseId).value        = opt.dataset.notes   ?? '';

    const calcSel = document.getElementById('calc-' + phaseId);
    if (opt.dataset.type) {
        for (let o of calcSel.options) o.selected = (o.value === opt.dataset.type);
    }

    const currSel = document.getElementById('curr-' + phaseId);
    if (opt.dataset.currency) {
        for (let o of currSel.options) o.selected = (o.value === opt.dataset.currency);
    }

    document.getElementById('plugin-' + phaseId).checked = (opt.dataset.plugin === '1');
}

function openEditModal(id, data) {
    const form = document.getElementById('editItemForm');
    form.action = '/line-items/' + id;

    document.getElementById('ei-name').value  = data.name        ?? '';
    document.getElementById('ei-rate').value  = data.rate        ?? '';
    document.getElementById('ei-qty').value   = data.quantity    ?? '';
    document.getElementById('ei-pct').value   = data.percentage_value ?? '';
    document.getElementById('ei-conv').value  = data.conversion_rate  ?? '';
    document.getElementById('ei-notes').value = data.notes       ?? '';

    const calcSel = document.getElementById('ei-calc');
    for (let o of calcSel.options) o.selected = (o.value === data.calculation_type);

    const currSel = document.getElementById('ei-curr');
    for (let o of currSel.options) o.selected = (o.value === (data.currency || 'ZAR'));

    document.getElementById('ei-plugin').checked = (data.is_plugin == 1 || data.is_plugin === true);

    bootstrap.Modal.getOrCreateInstance(document.getElementById('editItemModal')).show();
}

function deleteItem(id) {
    if (!confirm('Remove this line item?')) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/line-items/' + id;
    const csrf = document.createElement('input');
    csrf.type  = 'hidden'; csrf.name  = '_token'; csrf.value = '{{ csrf_token() }}';
    const method = document.createElement('input');
    method.type  = 'hidden'; method.name  = '_method'; method.value = 'DELETE';
    form.appendChild(csrf);
    form.appendChild(method);
    document.body.appendChild(form);
    form.submit();
}
</script>
@endpush

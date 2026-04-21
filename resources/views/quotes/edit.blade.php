@extends('layouts.app')

@section('title', 'Edit Quote — ' . ($quote->client_name ?? 'Draft'))

@section('content')

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
            <span class="badge bg-secondary bg-opacity-10 text-secondary text-uppercase ms-2" style="font-size:.65rem;vertical-align:middle;">
                {{ $quote->template_type }}
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
                            <label class="form-label small fw-semibold">Client Name</label>
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

                            <div class="row g-2 mb-2">
                                {{-- Template picker --}}
                                <div class="col-md-6">
                                    <label class="form-label small">Load from template</label>
                                    <select class="form-select form-select-sm" id="template-picker-{{ $phase->id }}"
                                            onchange="fillFromTemplate(this, {{ $phase->id }})">
                                        <option value="">— pick a template —</option>
                                        @foreach($templates as $cat => $items)
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
            <div class="card-footer bg-transparent text-center">
                <form action="{{ route('quotes.recalculate', $quote) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-arrow-repeat me-1"></i> Recalculate
                    </button>
                </form>
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
                        <tr><td class="ps-3 text-muted">R85 000+</td><td class="text-end pe-3">10%</td></tr>
                        <tr><td class="ps-3 text-muted">R55 000 – R85 000</td><td class="text-end pe-3">12%</td></tr>
                        <tr><td class="ps-3 text-muted">R35 500 – R55 000</td><td class="text-end pe-3">15%</td></tr>
                        <tr><td class="ps-3 text-muted">R12 500 – R35 500</td><td class="text-end pe-3">18%</td></tr>
                        <tr><td class="ps-3 text-muted">Below R12 500</td><td class="text-end pe-3">22.5%</td></tr>
                        <tr class="table-light"><td class="ps-3 text-muted">Plugins (always)</td><td class="text-end pe-3">10%</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    {{-- /right column --}}

</div>

@endsection

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

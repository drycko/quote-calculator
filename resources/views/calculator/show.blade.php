@extends('layouts.public')

@section('title', $quote->client_name ? 'Quote — ' . $quote->client_name : 'Quote Builder')

@section('nav-actions')
    {{-- <a href="{{ route('calculator.pdf', $quote->public_token) }}"
       class="btn btn-success btn-sm">
        <i class="bi bi-file-earmark-pdf me-1"></i> Download PDF
    </a> --}}
    @if($quote->isPublicEditable())
        <form action="{{ route('calculator.recalculate', $quote->public_token) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-arrow-repeat me-1"></i> Recalculate
            </button>
        </form>
    @endif
@endsection

@section('content')

@include('partials.choices-cdn')
@php
    $isEditable = $quote->isPublicEditable();
@endphp
<style>
    .phase-builder-card,
    .phase-builder-card .card-body,
    .phase-builder-card .tab-content,
    .phase-builder-card .tab-pane,
    .template-picker-group {
        overflow: visible;
    }

    .template-picker-group .choices {
        flex: 1 1 auto;
        width: 1%;
        min-width: 0;
        margin-bottom: 0;
    }

    .template-picker-group .choices__inner {
        min-height: calc(1.5em + .5rem + 2px);
        padding: .25rem 2rem .25rem .5rem;
        font-size: .875rem;
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }

    .template-picker-group .choices[data-type*=select-one]::after {
        right: .75rem;
        margin-top: -3px;
    }

    .template-picker-group .choices__list--single {
        padding: 0;
    }

    .template-picker-group .choices__list--dropdown,
    .template-picker-group .choices__list[aria-expanded] {
        z-index: 2000;
    }
</style>
<div class="row g-4">

    {{-- ── LEFT: Builder ──────────────────────────────────────── --}}
    <div class="col-xl-8">
        @if(!$isEditable)
            <div class="alert alert-info border-0 shadow-sm">
                <div class="fw-semibold">Quote submitted for review</div>
                <div class="small mb-0">Your quote is locked while our sales team reviews it.</div>
            </div>
        @endif

        {{-- Quote info --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent fw-semibold border-bottom d-flex justify-content-between align-items-center">
                <span><i class="bi bi-person me-2"></i>Quote Details</span>
                <div class="d-flex gap-2">
                    <span class="badge bg-primary bg-opacity-10 text-primary" style="font-size:.65rem;">
                        {{ $quote->quote_number }}
                    </span>
                    <span class="badge bg-secondary bg-opacity-10 text-secondary" style="font-size:.65rem;">
                        {{ \App\Models\Quote::statuses()[$quote->status] ?? $quote->status }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('calculator.update', $quote->public_token) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Business Name</label>
                            <input type="text" class="form-control form-control-sm" name="client_name"
                                   value="{{ $quote->client_name }}" {{ $isEditable ? '' : 'disabled' }}>
                        </div>
                        {{-- Quote type is locked to 'web' for the public calculator --}}
                        <input type="hidden" name="template_type" value="web">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Contact Name</label>
                            <input type="text" class="form-control form-control-sm" name="client_contact_name"
                                   value="{{ $quote->client_contact_name }}" {{ $isEditable ? '' : 'disabled' }}>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Contact Email</label>
                            <input type="email" class="form-control form-control-sm" name="client_contact_email"
                                   value="{{ $quote->client_contact_email }}" {{ $isEditable ? '' : 'disabled' }}>
                        </div>
                        @if($isEditable)
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-save me-1"></i> Save Details
                                </button>
                            </div>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- Phase Tabs --}}
        <div class="card border-0 shadow-sm phase-builder-card">
            <div class="card-header bg-transparent border-bottom p-0">
                <ul class="nav nav-tabs border-0 px-3 pt-2" id="phaseTabs" role="tablist">
                    @foreach($quote->phases as $index => $phase)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $index === 0 ? 'active' : '' }}"
                                    data-bs-toggle="tab"
                                    data-bs-target="#phase-{{ $phase->id }}"
                                    type="button" role="tab">
                                @if($phase->type === 'design')
                                    <i class="bi bi-palette me-1"></i> Design
                                @elseif($phase->type === 'development')
                                    <i class="bi bi-code-slash me-1"></i> Development
                                @else
                                    <i class="bi bi-plug me-1"></i> Plugins & PM
                                @endif
                                <span class="badge bg-secondary bg-opacity-10 text-secondary ms-1">
                                    {{ $phase->lineItems->count() }}
                                </span>
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="tab-content p-3">
                @foreach($quote->phases as $index => $phase)
                @php
                    $phaseCategories = [
                        'design'      => ['design'],
                        'development' => ['dev'],
                        'plugins_pm'  => ['plugin', 'pm'],
                    ];
                    $allowedCats = $phaseCategories[$phase->type] ?? [];
                    $phaseTemplates = $templates->filter(fn($items, $cat) => in_array($cat, $allowedCats));
                @endphp
                <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}"
                     id="phase-{{ $phase->id }}" role="tabpanel">

                    {{-- Existing line items --}}
                    @if($phase->lineItems->isNotEmpty())
                    <div class="table-responsive mb-3">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th style="width:100px;">Type</th>
                                    <th style="width:90px;">Rate</th>
                                    <th style="width:65px;">Qty</th>
                                    <th style="width:65px;">%</th>
                                    <th style="width:60px;">Curr</th>
                                    <th style="width:55px;" class="text-center">Plugin</th>
                                    <th style="width:95px;" class="text-end">Total</th>
                                    @if($isEditable)
                                        <th style="width:80px;"></th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($phase->lineItems as $item)
                                <tr>
                                    <td>
                                        <span class="fw-semibold" style="font-size:.875rem;">{{ $item->name }}</span>
                                        @if($item->notes)
                                            <div class="text-muted" style="font-size:.75rem;">{{ $item->notes }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border" style="font-size:.7rem;">
                                            {{ $item->calculation_type }}
                                        </span>
                                    </td>
                                    <td class="text-muted small">
                                        @if($item->rate)
                                            {{ format_money($item->rate, $item->currency) }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="text-muted small">
                                        @if($isEditable && !in_array($item->calculation_type, ['fixed','percentage']))
                                            <form action="{{ route('calculator.items.qty', [$quote->public_token, $item->id]) }}" method="POST" class="d-flex">
                                                @csrf @method('PATCH')
                                                <input type="number" name="quantity" step="1" min="0"
                                                       value="{{ number_format($item->quantity, 0) }}"
                                                       class="form-control form-control-sm p-0 text-center border-0 bg-transparent"
                                                       style="width:55px;"
                                                       onchange="this.form.submit()">
                                            </form>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="text-muted small">{{ $item->percentage_value ? $item->percentage_value . '%' : '—' }}</td>
                                    <td class="text-muted small">{{ $item->currency ?? 'ZAR' }}</td>
                                    <td class="text-center">
                                        @if($item->is_plugin)
                                            <i class="bi bi-check-circle-fill text-success" style="font-size:.85rem;"></i>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-end fw-semibold small">{{ format_money($item->total) }}</td>
                                    @if($isEditable)
                                    <td class="text-end">
                                        <div class="btn-group" role="group">
                                            {{-- Move to another phase --}}
                                            <div class="dropdown">
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-secondary py-0 px-1 dropdown-toggle"
                                                        data-bs-toggle="dropdown"
                                                        data-bs-popper-config='{"strategy":"fixed"}'
                                                        title="Move to phase">
                                                    <i class="bi bi-arrow-left-right" style="font-size:.85rem;"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end" style="min-width:140px;">
                                                    @foreach($quote->phases as $targetPhase)
                                                        @if($targetPhase->id !== $phase->id)
                                                            <li>
                                                                <form action="{{ route('calculator.items.move', [$quote->public_token, $item->id]) }}"
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
                                            <button type="button" class="btn btn-sm btn-outline-danger py-0 px-1"
                                                    onclick="deleteItem('{{ $quote->public_token }}', {{ $item->id }})">
                                                <i class="bi bi-x" style="font-size:.85rem;"></i>

                                            </button>
                                        </div>
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                        <p class="text-muted small my-2">No items in this phase.</p>
                    @endif

                    {{-- Add from template --}}
                    @if($isEditable)
                    <div class="mt-3">
                        <form action="{{ route('calculator.items.store', $quote->public_token) }}"
                              method="POST" id="add-form-{{ $phase->id }}">
                            @csrf
                            <input type="hidden" name="phase_id"         value="{{ $phase->id }}">
                            <input type="hidden" name="name"             id="h-name-{{ $phase->id }}">
                            <input type="hidden" name="calculation_type" id="h-type-{{ $phase->id }}">
                            <input type="hidden" name="rate"             id="h-rate-{{ $phase->id }}">
                            <input type="hidden" name="quantity"         value="1">
                            <input type="hidden" name="percentage_value" id="h-pct-{{ $phase->id }}">
                            <input type="hidden" name="currency"         id="h-curr-{{ $phase->id }}">
                            <input type="hidden" name="conversion_rate"  id="h-conv-{{ $phase->id }}">
                            <input type="hidden" name="is_plugin"        id="h-plugin-{{ $phase->id }}" value="0">
                            <input type="hidden" name="notes"            id="h-notes-{{ $phase->id }}">

                            <div class="input-group template-picker-group">
                                <select class="form-select form-select-sm template-picker-select"
                                        onchange="pickTemplate(this, {{ $phase->id }})">
                                    <option value="">— add line item —</option>
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
                                <button type="submit" class="btn btn-primary btn-sm" id="add-btn-{{ $phase->id }}" disabled>
                                    <i class="bi bi-plus me-1"></i> Add
                                </button>
                            </div>
                        </form>
                    </div>
                    @endif

                </div>
                @endforeach
            </div>
        </div>

    </div>
    {{-- /left --}}

    {{-- ── RIGHT: Summary Panel ────────────────────────────────── --}}
    <div class="col-xl-4">
        <div class="card border-0 shadow-sm sticky-top" style="top:1.25rem;">
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
                            <td class="ps-3 text-muted">Total ex VAT</td>
                            <td class="text-end pe-3">{{ format_money($quote->total_ex_vat) }}</td>
                        </tr>
                        <tr>
                            <td class="ps-3 text-muted">VAT ({{ config('quote.vat_rate') * 100 }}%)</td>
                            <td class="text-end pe-3">{!! $quote->apply_vat ? format_money($quote->vat) : '<span class="text-muted small">excl.</span>' !!}</td>
                        </tr>
                        <tr class="table-success">
                            <td class="ps-3 fw-bold">Total {{ $quote->apply_vat ? 'inc VAT' : 'ex VAT' }}</td>
                            <td class="text-end pe-3 fw-bold fs-5">{{ format_money($quote->total_inc_vat) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-transparent row gx-2">
                @if($isEditable)
                <div class="col-6">
                    <form action="{{ route('calculator.recalculate', $quote->public_token) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-success w-100 fw-semibold">
                            <i class="bi bi-arrow-repeat me-1"></i> Update Totals
                        </button>
                    </form>
                </div>
                <div class="col-6">
                    <form action="{{ route('calculator.submit', $quote->public_token) }}" method="POST"
                          onsubmit="return confirm('Submit this quote for review? You will not be able to make further changes.')">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100 fw-semibold">
                            <i class="bi bi-send me-1"></i> Submit for Review
                        </button>
                    </form>
                </div>
                @else
                <div class="col-12">
                    <div class="text-muted small">Status: {{ \App\Models\Quote::statuses()[$quote->status] ?? $quote->status }}</div>
                    @if($quote->status === 'sent_to_client')
                        <form action="{{ route('calculator.approve', $quote->public_token) }}" method="POST" class="mt-2"
                              onsubmit="return confirm('Approve this quote?')">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100 fw-semibold">
                                <i class="bi bi-check2-circle me-1"></i> Approve Quote
                            </button>
                        </form>
                    @endif
                </div>
                @endif
                {{-- <a href="{{ route('calculator.pdf', $quote->public_token) }}"
                   class="btn btn-outline-dark w-100">
                    <i class="bi bi-file-earmark-pdf me-1"></i> Download PDF
                </a> --}}
            </div>
        </div>

        {{-- Bookmark reminder --}}
        <div class="card border-0 shadow-sm mt-3 bg-primary bg-opacity-10">
            <div class="card-body py-2 px-3 d-flex align-items-center gap-2">
                <i class="bi bi-bookmark-star text-primary fs-5"></i>
                <div>
                    <div class="small fw-semibold">Bookmark this quote</div>
                    <div class="text-muted" style="font-size:.75rem;">Your quote link is unique — bookmark it to return.</div>
                </div>
            </div>
        </div>

    </div>
    {{-- /right --}}

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

    // choices js for template pickers
    document.querySelectorAll('select.template-picker-select').forEach(function(select) {
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

function pickTemplate(select, phaseId) {
    const opt = select.options[select.selectedIndex];
    const btn = document.getElementById('add-btn-' + phaseId);

    if (!opt.value) {
        btn.disabled = true;
        return;
    }

    document.getElementById('h-name-'   + phaseId).value = opt.dataset.name       ?? '';
    document.getElementById('h-type-'   + phaseId).value = opt.dataset.type       ?? 'fixed';
    document.getElementById('h-rate-'   + phaseId).value = opt.dataset.rate       ?? '';
    document.getElementById('h-pct-'    + phaseId).value = opt.dataset.pct        ?? '';
    document.getElementById('h-curr-'   + phaseId).value = opt.dataset.currency   ?? 'ZAR';
    document.getElementById('h-conv-'   + phaseId).value = opt.dataset.conversion ?? '';
    document.getElementById('h-plugin-' + phaseId).value = opt.dataset.plugin     ?? '0';
    document.getElementById('h-notes-'  + phaseId).value = opt.dataset.notes      ?? '';

    btn.disabled = false;
}

function deleteItem(token, itemId) {
    if (!confirm('Remove this line item?')) return;
    const form   = document.createElement('form');
    form.method  = 'POST';
    form.action  = '/calculator/' + token + '/items/' + itemId;
    const csrf   = document.createElement('input');
    csrf.type    = 'hidden'; csrf.name = '_token'; csrf.value = '{{ csrf_token() }}';
    const method = document.createElement('input');
    method.type  = 'hidden'; method.name = '_method'; method.value = 'DELETE';
    form.appendChild(csrf);
    form.appendChild(method);
    document.body.appendChild(form);
    form.submit();
}
</script>
@endpush

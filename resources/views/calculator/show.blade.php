@extends('layouts.public')

@section('title', $quote->client_name ? 'Quote — ' . $quote->client_name : 'Quote Builder')

@section('nav-actions')
    <a href="{{ route('calculator.pdf', $quote->public_token) }}"
       class="btn btn-success btn-sm">
        <i class="bi bi-file-earmark-pdf me-1"></i> Download PDF
    </a>
    <form action="{{ route('calculator.recalculate', $quote->public_token) }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-arrow-repeat me-1"></i> Recalculate
        </button>
    </form>
@endsection

@section('content')
<div class="row g-4">

    {{-- ── LEFT: Builder ──────────────────────────────────────── --}}
    <div class="col-xl-8">

        {{-- Quote info --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent fw-semibold border-bottom d-flex justify-content-between align-items-center">
                <span><i class="bi bi-person me-2"></i>Quote Details</span>
                <span class="badge bg-secondary bg-opacity-10 text-secondary text-uppercase" style="font-size:.65rem;">
                    {{ $quote->template_type }}
                </span>
            </div>
            <div class="card-body">
                <form action="{{ route('calculator.update', $quote->public_token) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Client Name</label>
                            <input type="text" class="form-control form-control-sm" name="client_name"
                                   value="{{ $quote->client_name }}">
                        </div>
                        {{-- Quote type is locked to 'web' for the public calculator --}}
                        <input type="hidden" name="template_type" value="web">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Your Name</label>
                            <input type="text" class="form-control form-control-sm" name="salesperson_name"
                                   value="{{ $quote->salesperson_name }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Your Email</label>
                            <input type="email" class="form-control form-control-sm" name="salesperson_email"
                                   value="{{ $quote->salesperson_email }}">
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-save me-1"></i> Save Details
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
                                    <th style="width:50px;"></th>
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
                                    <td class="text-muted small">{{ !in_array($item->calculation_type, ['fixed','percentage']) ? $item->quantity : '—' }}</td>
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
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-outline-danger py-0 px-1"
                                                onclick="deleteItem('{{ $quote->public_token }}', {{ $item->id }})">
                                            <i class="bi bi-x" style="font-size:.85rem;"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                        <p class="text-muted small my-2">No items in this phase.</p>
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
                            <td class="ps-3 text-muted">VAT (15%)</td>
                            <td class="text-end pe-3">{{ format_money($quote->vat) }}</td>
                        </tr>
                        <tr class="table-success">
                            <td class="ps-3 fw-bold">Total inc VAT</td>
                            <td class="text-end pe-3 fw-bold fs-5">{{ format_money($quote->total_inc_vat) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-transparent">
                <form action="{{ route('calculator.recalculate', $quote->public_token) }}" method="POST" class="mb-2">
                    @csrf
                    <button type="submit" class="btn btn-success w-100 fw-semibold">
                        <i class="bi bi-arrow-repeat me-1"></i> Recalculate
                    </button>
                </form>
                <a href="{{ route('calculator.pdf', $quote->public_token) }}"
                   class="btn btn-outline-dark w-100">
                    <i class="bi bi-file-earmark-pdf me-1"></i> Download PDF
                </a>
            </div>
        </div>

        {{-- Bookmark reminder --}}
        <div class="card border-0 shadow-sm mt-3 bg-primary bg-opacity-10">
            <div class="card-body py-2 px-3 d-flex align-items-center gap-2">
                <i class="bi bi-bookmark-star text-primary fs-5"></i>
                <div>
                    <div class="small fw-semibold">Bookmark this page</div>
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
function fillFromTemplate(select, phaseId) {
    const opt = select.options[select.selectedIndex];
    if (!opt.value) return;

    document.getElementById('name-'   + phaseId).value = opt.dataset.name       ?? '';
    document.getElementById('rate-'   + phaseId).value = opt.dataset.rate       ?? '';
    document.getElementById('pct-'    + phaseId).value = opt.dataset.pct        ?? '';
    document.getElementById('conv-'   + phaseId).value = opt.dataset.conversion ?? '';
    document.getElementById('notes-'  + phaseId).value = opt.dataset.notes      ?? '';

    ['calc-' + phaseId, 'curr-' + phaseId].forEach((id, i) => {
        const val = i === 0 ? opt.dataset.type : opt.dataset.currency;
        if (val) {
            const sel = document.getElementById(id);
            for (let o of sel.options) o.selected = (o.value === val);
        }
    });

    document.getElementById('plugin-' + phaseId).checked = (opt.dataset.plugin === '1');
}

function deleteItem(token, itemId) {
    if (!confirm('Remove this line item?')) return;
    const form    = document.createElement('form');
    form.method   = 'POST';
    form.action   = '/calculator/' + token + '/items/' + itemId;
    const csrf    = document.createElement('input');
    csrf.type     = 'hidden'; csrf.name = '_token'; csrf.value = '{{ csrf_token() }}';
    const method  = document.createElement('input');
    method.type   = 'hidden'; method.name = '_method'; method.value = 'DELETE';
    form.appendChild(csrf);
    form.appendChild(method);
    document.body.appendChild(form);
    form.submit();
}
</script>
@endpush

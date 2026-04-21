@extends('layouts.app')

@section('title', 'Line Item Templates')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Line Item Templates</h1>
    <a href="{{ route('templates.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus me-1"></i> New Template
    </a>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" action="{{ route('templates.index') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small mb-1">Type</label>
                <select name="type" class="form-select form-select-sm">
                    <option value="">All types</option>
                    @foreach(['web' => 'Web', 'manual' => 'Manual Adjust', 'ilead' => 'iLead'] as $val => $label)
                        <option value="{{ $val }}" {{ request('type') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Category</label>
                <select name="category" class="form-select form-select-sm">
                    <option value="">All categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small mb-1">Search</label>
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Template name…" value="{{ request('search') }}">
            </div>
            <div class="col-auto d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                <a href="{{ route('templates.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
            </div>
        </form>
    </div>
</div>

@if($templates->isEmpty())
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-grid fs-1 mb-3 d-block"></i>
            <p class="mb-3">No templates found.</p>
            <a href="{{ route('templates.create') }}" class="btn btn-primary btn-sm">Create your first template</a>
        </div>
    </div>
@else
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:.875rem;">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Category</th>
                        <th>Calc Type</th>
                        <th class="text-end">Rate</th>
                        <th class="text-end">%</th>
                        <th>Currency</th>
                        <th class="text-center">Plugin</th>
                        <th>Notes</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($templates as $tpl)
                    <tr>
                        <td class="fw-semibold">{{ $tpl->name }}</td>
                        <td>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary text-uppercase" style="font-size:.7rem;">
                                {{ $tpl->template_type }}
                            </span>
                        </td>
                        <td class="text-muted">{{ ucfirst($tpl->category) }}</td>
                        <td>
                            <span class="badge bg-light text-dark border" style="font-size:.7rem;">
                                {{ $tpl->calculation_type }}
                            </span>
                        </td>
                        <td class="text-end text-muted">
                            @if($tpl->default_rate)
                                {{ format_money($tpl->default_rate, $tpl->currency ?? 'ZAR') }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="text-end text-muted">
                            {{ $tpl->default_percentage ? $tpl->default_percentage . '%' : '—' }}
                        </td>
                        <td class="text-muted">{{ $tpl->currency ?? 'ZAR' }}</td>
                        <td class="text-center">
                            @if($tpl->is_plugin)
                                <i class="bi bi-check-circle-fill text-success"></i>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-muted" style="max-width:180px;">
                            <span class="d-inline-block text-truncate" style="max-width:160px;" title="{{ $tpl->default_notes }}">
                                {{ $tpl->default_notes ?? '—' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('templates.edit', $tpl) }}" class="btn btn-sm btn-outline-primary py-0 px-2 me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('templates.destroy', $tpl) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Delete this template?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger py-0 px-2">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($templates->hasPages())
        <div class="container-fluid py-3">
            <div class="row align-items-center">
                <div class="col-md-12 float-end">
                {{ $templates->appends(request()->except('page'))->links('vendor.pagination.bootstrap-5') }}
                </div>
            </div>
        </div>
        @endif
    </div>
@endif
@endsection

@extends('layouts.app')

@section('title', 'Quotes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Quotes</h1>
    <a href="{{ route('quotes.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus me-1"></i> New Quote
    </a>
</div>

@if($quotes->isEmpty())
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-file-earmark-text fs-1 mb-3 d-block"></i>
            <p class="mb-3">No quotes yet.</p>
            <a href="{{ route('quotes.create') }}" class="btn btn-primary btn-sm">Create your first quote</a>
        </div>
    </div>
@else
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Client</th>
                        <th>Template</th>
                        <th>Salesperson</th>
                        <th class="text-end">Subtotal</th>
                        <th class="text-end">Total ex VAT</th>
                        <th class="text-end">Total inc VAT</th>
                        <th>Created</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quotes as $quote)
                    <tr>
                        <td class="text-muted small">{{ $quote->id }}</td>
                        <td>
                            <span class="fw-semibold">{{ $quote->client_name ?? '—' }}</span>
                        </td>
                        <td>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary text-uppercase" style="font-size:.7rem;">
                                {{ $quote->template_type }}
                            </span>
                        </td>
                        <td class="text-muted small">{{ $quote->salesperson_name ?? '—' }}</td>
                        <td class="text-end">{{ format_money($quote->subtotal) }}</td>
                        <td class="text-end">{{ format_money($quote->total_ex_vat) }}</td>
                        <td class="text-end fw-semibold">{{ format_money($quote->total_inc_vat) }}</td>
                        <td class="text-muted small">{{ $quote->created_at->format('d M Y') }}</td>
                        <td class="text-end">
                            <a href="{{ route('quotes.edit', $quote) }}" class="btn btn-xs btn-sm btn-outline-primary me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('quotes.destroy', $quote) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Delete this quote?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-xs btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @if($quotes->hasPages())
    <div class="container-fluid py-3">
        <div class="row align-items-center">
            <div class="col-md-12 float-end">
            {{ $quotes->appends(request()->except('page'))->links('vendor.pagination.bootstrap-5') }}
            </div>
        </div>
    </div>
    @endif
@endif
@endsection

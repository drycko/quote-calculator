@extends('layouts.app')

@section('title', 'New Quote')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1" style="font-size:.875rem;">
                <li class="breadcrumb-item"><a href="{{ route('quotes.index') }}">Quotes</a></li>
                <li class="breadcrumb-item active">New Quote</li>
            </ol>
        </nav>
        <h1 class="h3 mb-0">New Quote</h1>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('quotes.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Template Type <span class="text-danger">*</span></label>
                        <div class="d-flex gap-2">
                            @foreach(['web' => 'Web', 'manual' => 'Manual Adjust', 'ilead' => 'iLead'] as $val => $label)
                                <div class="flex-fill">
                                    <input type="radio" class="btn-check" name="template_type"
                                           id="type_{{ $val }}" value="{{ $val }}"
                                           {{ old('template_type', 'manual') === $val ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary w-100" for="type_{{ $val }}">
                                        {{ $label }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('template_type')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="client_name" class="form-label fw-semibold">Client Business Name</label>
                        <input type="text" class="form-control @error('client_name') is-invalid @enderror"
                               id="client_name" name="client_name"
                               value="{{ old('client_name') }}" placeholder="e.g. Acme Corp">
                        @error('client_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="client_contact_name" class="form-label fw-semibold">Client Contact Name</label>
                            <input type="text" class="form-control @error('client_contact_name') is-invalid @enderror"
                                   id="client_contact_name" name="client_contact_name"
                                   value="{{ old('client_contact_name') }}">
                            @error('client_contact_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="client_contact_email" class="form-label fw-semibold">Client Contact Email</label>
                            <input type="email" class="form-control @error('client_contact_email') is-invalid @enderror"
                                   id="client_contact_email" name="client_contact_email"
                                   value="{{ old('client_contact_email') }}">
                            @error('client_contact_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="salesperson_name" class="form-label fw-semibold">Salesperson Name</label>
                            <input type="text" class="form-control @error('salesperson_name') is-invalid @enderror"
                                   id="salesperson_name" name="salesperson_name"
                                   value="{{ old('salesperson_name', auth()->user()->name) }}">
                            @error('salesperson_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="salesperson_email" class="form-label fw-semibold">Salesperson Email</label>
                            <input type="email" class="form-control @error('salesperson_email') is-invalid @enderror"
                                   id="salesperson_email" name="salesperson_email"
                                   value="{{ old('salesperson_email', auth()->user()->email) }}">
                            @error('salesperson_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-arrow-right me-1"></i> Create Quote
                        </button>
                        <a href="{{ route('quotes.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

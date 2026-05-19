@extends('layouts.public')

@section('title', 'New Quote')
@section('container-class', 'container')

@section('content')
<div class="row justify-content-center mt-3">
    <div class="col-lg-5">

        <div class="text-center mb-4">
            <h2 class="fw-bold mb-1">Build a Quote</h2>
            <p class="text-muted">Fill in the details below to start building your website quote.</p>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('calculator.store') }}" method="POST">
                    @csrf

                    {{-- template_type is always 'web' for the public calculator --}}
                    <input type="hidden" name="template_type" value="web">

                    <div class="mb-3">
                        <label for="client_name" class="form-label fw-semibold small">Business Name</label>
                        <input type="text" class="form-control @error('client_name') is-invalid @enderror"
                               id="client_name" name="client_name"
                               value="{{ old('client_name') }}" placeholder="e.g. Acme Corp">
                        @error('client_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="client_contact_name" class="form-label fw-semibold small">Contact Name</label>
                            <input type="text" class="form-control @error('client_contact_name') is-invalid @enderror"
                                   id="client_contact_name" name="client_contact_name"
                                   value="{{ old('client_contact_name') }}">
                            @error('client_contact_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="client_contact_email" class="form-label fw-semibold small">Contact Email</label>
                            <input type="email" class="form-control @error('client_contact_email') is-invalid @enderror"
                                   id="client_contact_email" name="client_contact_email"
                                   value="{{ old('client_contact_email') }}">
                            @error('client_contact_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-semibold">
                        <i class="bi bi-arrow-right me-1"></i> Start Quote
                    </button>
                </form>
            </div>
        </div>

        <p class="text-center text-muted small mt-3">
            Your quote gets a unique link — bookmark it to return later.
        </p>

    </div>
</div>
@endsection

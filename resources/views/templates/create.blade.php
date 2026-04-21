@extends('layouts.app')

@section('title', 'New Template')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1" style="font-size:.875rem;">
                <li class="breadcrumb-item"><a href="{{ route('templates.index') }}">Templates</a></li>
                <li class="breadcrumb-item active">New Template</li>
            </ol>
        </nav>
        <h1 class="h3 mb-0">New Template</h1>
    </div>
</div>

<div class="row justify-content-left">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('templates.store') }}" method="POST">
                    @csrf
                    @include('templates._form')
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check me-1"></i> Create Template
                        </button>
                        <a href="{{ route('templates.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

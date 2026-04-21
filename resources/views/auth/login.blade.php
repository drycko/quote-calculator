@extends('layouts.auth')

@section('title', 'Sign In')
@section('auth-subtitle', 'Sign in to your account')

@section('content')
<form method="POST" action="{{ route('login') }}">
    @csrf

    @if(session('status'))
        <div class="alert alert-success mb-3">{{ session('status') }}</div>
    @endif

    <div class="mb-3">
        <label for="email" class="form-label small fw-semibold">Email Address</label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0">
                <i class="bi bi-envelope text-muted"></i>
            </span>
            <input id="email" type="email"
                   class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror"
                   name="email" value="{{ old('email') }}"
                   required autocomplete="email" autofocus placeholder="you@agency.com">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="mb-3">
        <div class="d-flex justify-content-between">
            <label for="password" class="form-label small fw-semibold">Password</label>
            @if(Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="small text-muted">Forgot password?</a>
            @endif
        </div>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0">
                <i class="bi bi-lock text-muted"></i>
            </span>
            <input id="password" type="password"
                   class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror"
                   name="password" required autocomplete="current-password" placeholder="••••••••">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="remember"
                   {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label small" for="remember">Keep me signed in</label>
        </div>
    </div>

    <button type="submit" class="btn btn-primary w-100 fw-semibold">
        <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
    </button>
</form>
@endsection

{{-- Shared form fields for create & edit --}}

<div class="row g-3">

    {{-- Name --}}
    <div class="col-12">
        <label for="name" class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
        <input type="text" id="name" name="name"
               class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', ($user ?? null)?->name ?? '') }}" required autofocus>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Email --}}
    <div class="col-12">
        <label for="email" class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
        <input type="email" id="email" name="email"
               class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email', ($user ?? null)?->email ?? '') }}" required>
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Password --}}
    <div class="col-md-6">
        <label for="password" class="form-label fw-semibold">
            Password {{ isset($user) ? '(leave blank to keep current)' : '' }}
            @unless(isset($user))<span class="text-danger">*</span>@endunless
        </label>
        <input type="password" id="password" name="password"
               class="form-control @error('password') is-invalid @enderror"
               {{ isset($user) ? '' : 'required' }}>
        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label for="password_confirmation" class="form-label fw-semibold">Confirm Password</label>
        <input type="password" id="password_confirmation" name="password_confirmation"
               class="form-control" {{ isset($user) ? '' : 'required' }}>
    </div>

    {{-- Role --}}
    <div class="col-md-6">
        <label for="role" class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
        <select id="role" name="role"
                class="form-select @error('role') is-invalid @enderror" required>
            <option value="">— Select a role —</option>
            @foreach($roles as $role)
                <option value="{{ $role }}"
                    {{ old('role', ($user ?? null)?->roles->first()?->name ?? '') === $role ? 'selected' : '' }}>
                    {{ ucfirst($role) }}
                </option>
            @endforeach
        </select>
        @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

</div>

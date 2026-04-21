{{-- Shared form fields for create & edit --}}

<div class="row g-3">

    {{-- Name --}}
    <div class="col-12">
        <label for="name" class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
        <input type="text" id="name" name="name"
               class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $template->name ?? '') }}" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Template Type --}}
    <div class="col-md-6">
        <label class="form-label fw-semibold">Template Type <span class="text-danger">*</span></label>
        <div class="d-flex gap-2">
            @foreach(['web' => 'Web', 'manual' => 'Manual', 'ilead' => 'iLead'] as $val => $label)
                <div class="flex-fill">
                    <input type="radio" class="btn-check" name="template_type"
                           id="type_{{ $val }}" value="{{ $val }}"
                           {{ old('template_type', $template->template_type ?? 'web') === $val ? 'checked' : '' }}>
                    <label class="btn btn-outline-primary w-100 btn-sm" for="type_{{ $val }}">{{ $label }}</label>
                </div>
            @endforeach
        </div>
        @error('template_type') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
    </div>

    {{-- Category --}}
    <div class="col-md-6">
        <label for="category" class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
        <input type="text" id="category" name="category" list="category-list"
               class="form-control @error('category') is-invalid @enderror"
               value="{{ old('category', $template->category ?? '') }}"
               placeholder="e.g. design, development, plugin" required>
        <datalist id="category-list">
            @foreach(\App\Models\LineItemTemplate::distinct()->orderBy('category')->pluck('category') as $cat)
                <option value="{{ $cat }}">
            @endforeach
        </datalist>
        @error('category') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Calculation Type --}}
    <div class="col-md-6">
        <label for="calculation_type" class="form-label fw-semibold">Calculation Type <span class="text-danger">*</span></label>
        <select id="calculation_type" name="calculation_type"
                class="form-select @error('calculation_type') is-invalid @enderror">
            @foreach(['fixed' => 'Fixed', 'hourly' => 'Hourly', 'percentage' => 'Percentage', 'converted' => 'Converted'] as $val => $label)
                <option value="{{ $val }}"
                    {{ old('calculation_type', $template->calculation_type ?? 'fixed') === $val ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @error('calculation_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Currency --}}
    <div class="col-md-3">
        <label for="currency" class="form-label fw-semibold">Currency</label>
        <select id="currency" name="currency"
                class="form-select @error('currency') is-invalid @enderror"
                onchange="fetchConversionRate(this.value)">
            <option value="">— none —</option>
            @foreach(['ZAR', 'USD', 'EUR', 'GBP'] as $cur)
                <option value="{{ $cur }}"
                    {{ old('currency', $template->currency ?? 'ZAR') === $cur ? 'selected' : '' }}>
                    {{ $cur }}
                </option>
            @endforeach
        </select>
        @error('currency') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Conversion Rate --}}
    <div class="col-md-3">
        <label for="conversion_rate" class="form-label fw-semibold">
            Conv. Rate
            <span id="conv-rate-spinner" class="spinner-border spinner-border-sm text-secondary ms-1 d-none" role="status"></span>
        </label>
        <div class="input-group">
            <input type="number" step="0.0001" min="0" id="conversion_rate" name="conversion_rate"
                   class="form-control @error('conversion_rate') is-invalid @enderror"
                   value="{{ old('conversion_rate', $template->conversion_rate ?? '') }}"
                   placeholder="{{ env('DEFAULT_CONVERSION_RATE', '18.50') }}">
            <button type="button" class="btn btn-outline-secondary btn-sm" title="Fetch live rate"
                    onclick="fetchConversionRate(document.getElementById('currency').value)">
                <i class="bi bi-arrow-repeat"></i>
            </button>
        </div>
        <div id="conv-rate-hint" class="form-text text-muted"></div>
        @error('conversion_rate') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Default Rate --}}
    <div class="col-md-4">
        <label for="default_rate" class="form-label fw-semibold">Default Rate</label>
        <input type="number" step="0.01" min="0" id="default_rate" name="default_rate"
               class="form-control @error('default_rate') is-invalid @enderror"
               value="{{ old('default_rate', $template->default_rate ?? '') }}"
               placeholder="0.00">
        @error('default_rate') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Default Percentage --}}
    <div class="col-md-4">
        <label for="default_percentage" class="form-label fw-semibold">Default %</label>
        <input type="number" step="0.01" min="0" max="100" id="default_percentage" name="default_percentage"
               class="form-control @error('default_percentage') is-invalid @enderror"
               value="{{ old('default_percentage', $template->default_percentage ?? '') }}"
               placeholder="0.00">
        @error('default_percentage') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Is Plugin --}}
    <div class="col-md-4 d-flex align-items-end">
        <div class="form-check mb-2">
            <input type="hidden" name="is_plugin" value="0">
            <input class="form-check-input" type="checkbox" name="is_plugin" value="1"
                   id="is_plugin"
                   {{ old('is_plugin', $template->is_plugin ?? false) ? 'checked' : '' }}>
            <label class="form-check-label fw-semibold" for="is_plugin">Is Plugin / PM</label>
        </div>
    </div>

    {{-- Notes --}}
    <div class="col-12">
        <label for="default_notes" class="form-label fw-semibold">Default Notes</label>
        <textarea id="default_notes" name="default_notes" rows="2"
                  class="form-control @error('default_notes') is-invalid @enderror"
                  placeholder="Optional notes pre-filled on the line item">{{ old('default_notes', $template->default_notes ?? '') }}</textarea>
        @error('default_notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

</div>

@push('scripts')
<script>
function fetchConversionRate(fromCurrency) {
    const baseCurrency = '{{ config('app.currency', 'ZAR') }}';
    const field    = document.getElementById('conversion_rate');
    const spinner  = document.getElementById('conv-rate-spinner');
    const hint     = document.getElementById('conv-rate-hint');

    if (!fromCurrency || fromCurrency === baseCurrency) {
        hint.textContent = '';
        return;
    }

    spinner.classList.remove('d-none');
    hint.textContent = 'Fetching live rate…';

    fetch(`https://api.frankfurter.app/latest?from=${fromCurrency}&to=${baseCurrency}`)
        .then(r => r.json())
        .then(data => {
            const rate = data.rates?.[baseCurrency];
            if (rate) {
                field.value = parseFloat(rate).toFixed(4);
                hint.textContent = `Live: 1 ${fromCurrency} = ${rate} ${baseCurrency}`;
            } else {
                hint.textContent = 'Rate not found.';
            }
        })
        .catch(() => {
            hint.textContent = 'Could not fetch rate. Enter manually.';
        })
        .finally(() => {
            spinner.classList.add('d-none');
        });
}
</script>
@endpush

<?php

namespace App\Http\Controllers;

use App\Models\LineItem;
use App\Models\LineItemTemplate;
use App\Models\Phase;
use App\Models\Quote;
use App\Services\QuoteCalculator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CalculatorController extends Controller
{
    // ── Entry ────────────────────────────────────────────────────

    public function create(): View
    {
        return view('calculator.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_name'          => ['nullable', 'string', 'max:255'],
            'client_contact_name'  => ['nullable', 'string', 'max:255'],
            'client_contact_email' => ['nullable', 'email', 'max:255'],
            'template_type'        => ['required', 'in:web'],
        ]);

        $quote = Quote::create([
            'public_token' => Str::uuid()->toString(),
            'user_id'      => null,
            'status'       => 'draft',
            ...$validated,
        ]);

        foreach (['design', 'development', 'plugins_pm'] as $type) {
            Phase::create(['quote_id' => $quote->id, 'type' => $type]);
        }

        return redirect()->route('calculator.show', $quote->public_token);
    }

    // ── Calculator view ─────────────────────────────────────────

    public function show(string $token): View
    {
        $quote = Quote::where('public_token', $token)
            ->with('phases.lineItems')
            ->firstOrFail();

        $templates = LineItemTemplate::orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        return view('calculator.show', compact('quote', 'templates'));
    }

    // ── Update client info ───────────────────────────────────────

    public function update(Request $request, string $token): RedirectResponse
    {
        $quote = Quote::where('public_token', $token)->firstOrFail();

        $this->abortIfSubmitted($quote);

        $validated = $request->validate([
            'client_name'          => ['nullable', 'string', 'max:255'],
            'client_contact_name'  => ['nullable', 'string', 'max:255'],
            'client_contact_email' => ['nullable', 'email', 'max:255'],
            'template_type'        => ['required', 'in:web'],
        ]);

        $quote->update($validated);

        return back()->with('success', 'Quote details saved.');
    }

    // ── Recalculate ──────────────────────────────────────────────

    public function recalculate(string $token, QuoteCalculator $calculator): RedirectResponse
    {
        $quote = Quote::where('public_token', $token)->firstOrFail();

        $this->abortIfSubmitted($quote);

        $calculator->calculate($quote);

        return back()->with('success', 'Totals updated.');
    }

    public function submit(string $token, QuoteCalculator $calculator): RedirectResponse
    {
        $quote = Quote::where('public_token', $token)->firstOrFail();

        $this->abortIfSubmitted($quote);

        $calculator->calculate($quote);

        $quote->update(['status' => 'in_review']);

        return back()->with('success', 'Quote submitted for review. You can no longer make changes.');
    }

    public function approve(string $token): RedirectResponse
    {
        $quote = Quote::where('public_token', $token)->firstOrFail();

        abort_unless($quote->status === 'sent_to_client', 403, 'This quote is not ready for approval.');

        $quote->update(['status' => 'approved']);

        return back()->with('success', 'Quote approved. Thank you.');
    }

    // ── Line items ───────────────────────────────────────────────

    public function addItem(Request $request, string $token, QuoteCalculator $calculator): RedirectResponse
    {
        $quote = Quote::where('public_token', $token)->firstOrFail();

        $this->abortIfSubmitted($quote);

        $data = $request->validate([
            'phase_id'         => ['required', 'integer', 'exists:phases,id'],
            'name'             => ['required', 'string', 'max:255'],
            'calculation_type' => ['required', 'in:fixed,hourly,percentage,converted'],
            'rate'             => ['nullable', 'numeric', 'min:0'],
            'quantity'         => ['nullable', 'numeric', 'min:0'],
            'percentage_value' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'currency'         => ['nullable', 'string', 'max:10'],
            'conversion_rate'  => ['nullable', 'numeric', 'min:0'],
            'is_plugin'        => ['nullable', 'boolean'],
            'notes'            => ['nullable', 'string', 'max:1000'],
        ]);

        $data['currency']        = $data['currency'] ?? config('app.currency', 'ZAR');
        $data['conversion_rate'] = $data['conversion_rate'] ?? config('app.default_conversion_rate', 18.50);

        $phase = Phase::where('id', $data['phase_id'])
            ->where('quote_id', $quote->id)
            ->firstOrFail();

        LineItem::create([
            ...$data,
            'is_plugin' => (bool) ($data['is_plugin'] ?? false),
            'quantity'  => $data['quantity'] ?? 1,
            'total'     => 0,
        ]);

        $calculator->calculate($quote);

        return redirect(
            route('calculator.show', $token) . '#phase-' . $phase->id
        )->with('success', 'Item added.');
    }

    public function removeItem(string $token, LineItem $lineItem, QuoteCalculator $calculator): RedirectResponse
    {
        $quote = Quote::where('public_token', $token)->firstOrFail();

        $this->abortIfSubmitted($quote);

        abort_if($lineItem->phase->quote_id !== $quote->id, 403);

        $phaseId = $lineItem->phase_id;
        $lineItem->delete();

        $calculator->calculate($quote);

        return redirect(
            route('calculator.show', $token) . '#phase-' . $phaseId
        )->with('success', 'Item removed.');
    }

    public function updateItemQty(Request $request, string $token, LineItem $lineItem, QuoteCalculator $calculator): RedirectResponse
    {
        $quote = Quote::where('public_token', $token)->firstOrFail();

        $this->abortIfSubmitted($quote);

        abort_if($lineItem->phase->quote_id !== $quote->id, 403);

        $data = $request->validate([
            'quantity' => ['required', 'numeric', 'min:0'],
        ]);

        $lineItem->update(['quantity' => $data['quantity']]);

        $calculator->calculate($quote);

        return redirect(
            route('calculator.show', $token) . '#phase-' . $lineItem->phase_id
        )->with('success', 'Quantity updated.');
    }

    public function moveItem(Request $request, string $token, LineItem $lineItem, QuoteCalculator $calculator): RedirectResponse
    {
        $quote = Quote::where('public_token', $token)->firstOrFail();

        $this->abortIfSubmitted($quote);

        abort_if($lineItem->phase->quote_id !== $quote->id, 403);

        $data = $request->validate([
            'phase_id' => ['required', 'integer', 'exists:phases,id'],
        ]);

        $newPhase = Phase::where('id', $data['phase_id'])
            ->where('quote_id', $quote->id)
            ->firstOrFail();

        $lineItem->update(['phase_id' => $newPhase->id]);

        $calculator->calculate($quote);

        return redirect(
            route('calculator.show', $token) . '#phase-' . $newPhase->id
        )->with('success', 'Item moved.');
    }

    // ── PDF download ─────────────────────────────────────────────

    public function downloadPdf(string $token): Response
    {
        $quote = Quote::where('public_token', $token)
            ->with('phases.lineItems')
            ->firstOrFail();

        $pdf = Pdf::loadView('quotes.pdf', compact('quote'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('quote-' . ($quote->quote_number ?? $quote->public_token) . '.pdf');
    }

    private function abortIfSubmitted(Quote $quote): void
    {
        abort_if(!$quote->isPublicEditable(), 403, 'This quote has already been submitted for review.');
    }
}

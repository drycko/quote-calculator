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
            'client_name'       => ['nullable', 'string', 'max:255'],
            'salesperson_name'  => ['nullable', 'string', 'max:255'],
            'salesperson_email' => ['nullable', 'email', 'max:255'],
            'template_type'     => ['required', 'in:web'],
        ]);

        $quote = Quote::create([
            'public_token' => Str::uuid()->toString(),
            'user_id'      => null,
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
            ->groupBy('template_type');

        \Log::info('Showing quote', ['quote_id' => $quote->id, 'public_token' => $token]);
        // Log available templates for debugging
        \Log::info('Available templates', ['templates' => $templates->pluck('name')]);

        //  return view('calculator.show', compact('quote', 'templates'));

        return view('calculator.show', compact('quote', 'templates'));
    }

    // ── Update client info ───────────────────────────────────────

    public function update(Request $request, string $token): RedirectResponse
    {
        $quote = Quote::where('public_token', $token)->firstOrFail();

        $validated = $request->validate([
            'client_name'       => ['nullable', 'string', 'max:255'],
            'salesperson_name'  => ['nullable', 'string', 'max:255'],
            'salesperson_email' => ['nullable', 'email', 'max:255'],
            'template_type'     => ['required', 'in:web'],
        ]);

        $quote->update($validated);

        return back()->with('success', 'Quote details saved.');
    }

    // ── Recalculate ──────────────────────────────────────────────

    public function recalculate(string $token, QuoteCalculator $calculator): RedirectResponse
    {
        $quote = Quote::where('public_token', $token)->firstOrFail();

        $calculator->calculate($quote);

        return back()->with('success', 'Totals updated.');
    }

    // ── Line items ───────────────────────────────────────────────

    public function addItem(Request $request, string $token): RedirectResponse
    {
        $quote = Quote::where('public_token', $token)->firstOrFail();

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

        // Ensure the phase belongs to this quote
        $phase = Phase::where('id', $data['phase_id'])
            ->where('quote_id', $quote->id)
            ->firstOrFail();

        LineItem::create([
            ...$data,
            'is_plugin' => (bool) ($data['is_plugin'] ?? false),
            'quantity'  => $data['quantity'] ?? 1,
            'total'     => 0,
        ]);

        return back()->with('success', 'Item added. Click Recalculate to update totals.');
    }

    public function removeItem(string $token, LineItem $lineItem): RedirectResponse
    {
        $quote = Quote::where('public_token', $token)->firstOrFail();

        // Ensure the line item belongs to this quote
        abort_if($lineItem->phase->quote_id !== $quote->id, 403);

        $lineItem->delete();

        return back()->with('success', 'Item removed.');
    }

    // ── PDF download ─────────────────────────────────────────────

    public function downloadPdf(string $token): Response
    {
        $quote = Quote::where('public_token', $token)
            ->with('phases.lineItems')
            ->firstOrFail();

        $pdf = Pdf::loadView('quotes.pdf', compact('quote'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('quote-' . $quote->public_token . '.pdf');
    }
}

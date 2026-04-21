<?php

namespace App\Http\Controllers;

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

class QuoteController extends Controller
{
    public function index(): View
    {
        // Admin sees all quotes regardless of who created them
        $quotes = Quote::latest()->paginate(20);

        return view('quotes.index', compact('quotes'));
    }

    public function create(): View
    {
        return view('quotes.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_name'       => ['nullable', 'string', 'max:255'],
            'salesperson_name'  => ['nullable', 'string', 'max:255'],
            'salesperson_email' => ['nullable', 'email', 'max:255'],
            'template_type'     => ['required', 'in:web,manual,ilead'],
            'apply_markup'      => ['nullable', 'boolean'],
            'apply_vat'         => ['nullable', 'boolean'],
        ]);

        $quote = Quote::create([
            'user_id'      => auth()->id(),
            'public_token' => Str::uuid()->toString(),
            'apply_markup' => $validated['apply_markup'] ?? true,
            'apply_vat'    => $validated['apply_vat'] ?? true,
            ...$validated,
        ]);

        // Create the three standard phases
        foreach (['design', 'development', 'plugins_pm'] as $type) {
            Phase::create(['quote_id' => $quote->id, 'type' => $type]);
        }

        return redirect()->route('quotes.edit', $quote);
    }

    public function show(Quote $quote): RedirectResponse
    {
        return redirect()->route('quotes.edit', $quote);
    }

    public function edit(Quote $quote): View
    {
        $quote->load('phases.lineItems');

        $templates = LineItemTemplate::orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        return view('quotes.edit', compact('quote', 'templates'));
    }

    public function update(Request $request, Quote $quote): RedirectResponse
    {
        $validated = $request->validate([
            'client_name'       => ['nullable', 'string', 'max:255'],
            'salesperson_name'  => ['nullable', 'string', 'max:255'],
            'salesperson_email' => ['nullable', 'email', 'max:255'],
            'template_type'     => ['required', 'in:web,manual,ilead'],
            'apply_markup'      => ['nullable', 'boolean'],
            'apply_vat'         => ['nullable', 'boolean'],
        ]);

        $quote->update([
            ...$validated,
            'apply_markup' => $request->boolean('apply_markup'),
            'apply_vat'    => $request->boolean('apply_vat'),
        ]);

        return back()->with('success', 'Quote updated.');
    }

    public function destroy(Quote $quote): RedirectResponse
    {
        $quote->delete();

        return redirect()->route('quotes.index')->with('success', 'Quote deleted.');
    }

    public function recalculate(Quote $quote, QuoteCalculator $calculator): RedirectResponse
    {
        $calculator->calculate($quote);

        return back()->with('success', 'Quote recalculated.');
    }

    public function downloadPdf(Quote $quote): Response
    {
        $quote->load('phases.lineItems');

        $pdf = Pdf::loadView('quotes.pdf', compact('quote'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('quote-' . $quote->id . '.pdf');
    }
}

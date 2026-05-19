<?php

namespace App\Http\Controllers;

use App\Models\LineItemTemplate;
use App\Models\Phase;
use App\Models\Quote;
use App\Mail\QuoteShareLinkMail;
use App\Services\QuoteCalculator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class QuoteController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'status'        => ['nullable', 'in:' . implode(',', array_keys(Quote::statuses()))],
            'template_type' => ['nullable', 'in:web,manual,ilead'],
            'search'        => ['nullable', 'string', 'max:255'],
        ]);

        // Admin sees all quotes regardless of who created them.
        $quotes = Quote::query()
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['template_type'] ?? null, fn ($query, $type) => $query->where('template_type', $type))
            ->when($filters['search'] ?? null, function ($query, $search) {
                $search = trim($search);

                $query->where(function ($query) use ($search) {
                    $query
                        ->where('quote_number', 'like', '%' . $search . '%')
                        ->orWhere('client_name', 'like', '%' . $search . '%')
                        ->orWhere('client_contact_name', 'like', '%' . $search . '%')
                        ->orWhere('client_contact_email', 'like', '%' . $search . '%')
                        ->orWhere('salesperson_name', 'like', '%' . $search . '%')
                        ->orWhere('salesperson_email', 'like', '%' . $search . '%');
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('quotes.index', compact('quotes'));
    }

    public function create(): View
    {
        return view('quotes.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_name'          => ['nullable', 'string', 'max:255'],
            'client_contact_name'  => ['nullable', 'string', 'max:255'],
            'client_contact_email' => ['nullable', 'email', 'max:255'],
            'salesperson_name'     => ['nullable', 'string', 'max:255'],
            'salesperson_email'    => ['nullable', 'email', 'max:255'],
            'template_type'        => ['required', 'in:web,manual,ilead'],
            'apply_markup'         => ['nullable', 'boolean'],
            'apply_vat'            => ['nullable', 'boolean'],
        ]);

        $validated['salesperson_name'] = ($validated['salesperson_name'] ?? null) ?: auth()->user()->name;
        $validated['salesperson_email'] = ($validated['salesperson_email'] ?? null) ?: auth()->user()->email;

        $quote = Quote::create([
            'user_id'           => auth()->id(),
            'public_token'      => Str::uuid()->toString(),
            'status'            => 'draft',
            'apply_markup'      => $validated['apply_markup'] ?? true,
            'apply_vat'         => $validated['apply_vat'] ?? true,
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
            'client_name'          => ['nullable', 'string', 'max:255'],
            'client_contact_name'  => ['nullable', 'string', 'max:255'],
            'client_contact_email' => ['nullable', 'email', 'max:255'],
            'salesperson_name'     => ['nullable', 'string', 'max:255'],
            'salesperson_email'    => ['nullable', 'email', 'max:255'],
            'template_type'        => ['required', 'in:web,manual,ilead'],
            'status'               => ['required', 'in:' . implode(',', array_keys(Quote::statuses()))],
            'apply_markup'         => ['nullable', 'boolean'],
            'apply_vat'            => ['nullable', 'boolean'],
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

    public function sendEmail(Request $request, Quote $quote): RedirectResponse
    {
        $validated = $request->validate([
            'to_email' => ['required', 'email', 'max:255'],
            'subject'  => ['required', 'string', 'max:255'],
            'message'  => ['required', 'string', 'max:2000'],
        ]);

        $quote->load('phases.lineItems');

        Mail::to($validated['to_email'])->send(new QuoteShareLinkMail(
            quote: $quote,
            subjectText: $validated['subject'],
            messageText: $validated['message'],
            shareUrl: route('calculator.show', $quote->public_token),
        ));

        if ($quote->status !== 'converted') {
            $quote->update(['status' => 'sent_to_client']);
        }

        return back()->with('success', 'Quote email sent to ' . $validated['to_email'] . '.');
    }

    public function downloadPdf(Quote $quote): Response
    {
        $quote->load('phases.lineItems');

        $pdf = Pdf::loadView('quotes.pdf', compact('quote'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('quote-' . ($quote->quote_number ?? $quote->id) . '.pdf');
    }
}

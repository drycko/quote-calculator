<?php

namespace App\Http\Controllers;

use App\Models\LineItem;
use App\Models\Phase;
use App\Services\QuoteCalculator;
use Illuminate\Http\Request;

class LineItemController extends Controller
{
    public function store(Request $request, QuoteCalculator $calculator)
    {
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

        $phase = Phase::with('quote')->findOrFail($data['phase_id']);
        abort_if($phase->quote->user_id !== auth()->id() && !auth()->user()->hasRole('administrator'), 403);

        $data['is_plugin'] = (bool) ($data['is_plugin'] ?? false);
        $data['quantity']  = $data['quantity'] ?? 1;
        $data['total']     = 0;

        LineItem::create($data);

        $calculator->calculate($phase->quote);

        return redirect(
            route('quotes.edit', $phase->quote_id) . '#phase-' . $phase->id
        )->with('success', 'Line item added.');
    }

    public function destroy(LineItem $lineItem, QuoteCalculator $calculator)
    {
        $phase   = $lineItem->phase;
        $quote   = $phase->quote;
        abort_if($quote->user_id !== auth()->id() && !auth()->user()->hasRole('administrator'), 403);

        $phaseId = $phase->id;
        $lineItem->delete();

        $calculator->calculate($quote);

        return redirect(
            route('quotes.edit', $quote->id) . '#phase-' . $phaseId
        )->with('success', 'Line item removed.');
    }

    public function update(Request $request, LineItem $lineItem, QuoteCalculator $calculator)
    {
        $phase = $lineItem->phase;
        $quote = $phase->quote;
        abort_if($quote->user_id !== auth()->id() && !auth()->user()->hasRole('administrator'), 403);

        $data = $request->validate([
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

        $data['is_plugin'] = (bool) ($data['is_plugin'] ?? false);

        $lineItem->update($data);
        $calculator->calculate($quote);

        return redirect(
            route('quotes.edit', $quote->id) . '#phase-' . $phase->id
        )->with('success', 'Line item updated.');
    }

    public function move(Request $request, LineItem $lineItem, QuoteCalculator $calculator)
    {
        $phase = $lineItem->phase;
        $quote = $phase->quote;
        abort_if($quote->user_id !== auth()->id() && !auth()->user()->hasRole('administrator'), 403);

        $data = $request->validate([
            'phase_id' => ['required', 'integer', 'exists:phases,id'],
        ]);

        $newPhase = Phase::where('id', $data['phase_id'])
            ->where('quote_id', $quote->id)
            ->firstOrFail();

        $lineItem->update(['phase_id' => $newPhase->id]);

        $calculator->calculate($quote);

        return redirect(
            route('quotes.edit', $quote->id) . '#phase-' . $newPhase->id
        )->with('success', 'Item moved.');
    }
}

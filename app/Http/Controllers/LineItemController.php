<?php

namespace App\Http\Controllers;

use App\Models\LineItem;
use App\Models\Phase;
use Illuminate\Http\Request;

class LineItemController extends Controller
{
    public function store(Request $request)
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

        // Authorise: the phase must belong to a quote owned by the user
        $phase = Phase::with('quote')->findOrFail($data['phase_id']);
        abort_if($phase->quote->user_id !== auth()->id(), 403);

        $data['is_plugin'] = (bool) ($data['is_plugin'] ?? false);
        $data['quantity']  = $data['quantity'] ?? 1;
        $data['total']     = 0; // will be set on next recalculate

        LineItem::create($data);

        return redirect()->route('quotes.edit', $phase->quote_id)
                         ->with('success', 'Line item added. Click Recalculate to update totals.');
    }

    public function destroy(LineItem $lineItem)
    {
        $quoteId = $lineItem->phase->quote_id;
        abort_if($lineItem->phase->quote->user_id !== auth()->id(), 403);

        $lineItem->delete();

        return redirect()->route('quotes.edit', $quoteId)
                         ->with('success', 'Line item removed.');
    }
}

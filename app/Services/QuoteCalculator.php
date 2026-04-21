<?php

namespace App\Services;

use App\Models\Quote;

class QuoteCalculator
{
    /**
     * Markup tiers applied to the main (non-plugin) subtotal.
     * Plugins always receive a fixed 10% markup.
     *
     * Format: [minimum_threshold => markup_percentage]
     * Evaluated highest-first.
     */
    private const MARKUP_TIERS = [
        85000  => 10,
        55000  => 12,
        35500  => 15,
        12500  => 18,
        0      => 22.5,
    ];

    private const PLUGIN_MARKUP = 10;
    private const VAT_RATE      = 0.15;

    public function calculate(Quote $quote): Quote
    {
        $quote->load('phases.lineItems');

        // ── Pass 1: fixed | hourly | converted ──────────────────────────
        $mainSubtotal   = 0;
        $pluginSubtotal = 0;

        foreach ($quote->phases as $phase) {
            foreach ($phase->lineItems as $item) {
                if ($item->calculation_type === 'percentage') {
                    continue; // handled in pass 2
                }

                $item->total = $this->calculateItemTotal($item, 0);
                $item->save();

                if ($item->is_plugin) {
                    $pluginSubtotal += $item->total;
                } else {
                    $mainSubtotal += $item->total;
                }
            }
        }

        $subtotal = $mainSubtotal + $pluginSubtotal;

        // ── Pass 2: percentage items (based on pass-1 subtotal) ──────────
        foreach ($quote->phases as $phase) {
            foreach ($phase->lineItems as $item) {
                if ($item->calculation_type !== 'percentage') {
                    continue;
                }

                $item->total = $this->calculateItemTotal($item, $subtotal);
                $item->save();

                if ($item->is_plugin) {
                    $pluginSubtotal += $item->total;
                } else {
                    $mainSubtotal += $item->total;
                }
            }
        }

        $subtotal = $mainSubtotal + $pluginSubtotal;

        // ── Markup (admin quotes only; public quotes have no user_id) ────
        $isPublic = $quote->user_id === null;

        if ($isPublic) {
            $markupAmount      = 0;
            $blendedMarkupRate = 0;
            $mainTotal         = round($mainSubtotal, 2);
            $pluginTotal       = round($pluginSubtotal, 2);
        } else {
            $mainMarkupRate   = $this->resolveMarkupRate($mainSubtotal);
            $mainMarkupAmount = round($mainSubtotal * ($mainMarkupRate / 100), 2);

            $pluginMarkupAmount = round($pluginSubtotal * (self::PLUGIN_MARKUP / 100), 2);

            $markupAmount = $mainMarkupAmount + $pluginMarkupAmount;

            $blendedMarkupRate = $subtotal > 0
                ? round(($markupAmount / $subtotal) * 100, 2)
                : $mainMarkupRate;

            $mainTotal   = round($mainSubtotal + $mainMarkupAmount, 2);
            $pluginTotal = round($pluginSubtotal + $pluginMarkupAmount, 2);
        }

        $totalExVat  = round($mainTotal + $pluginTotal, 2);
        $vat         = round($totalExVat * self::VAT_RATE, 2);
        $totalIncVat = round($totalExVat + $vat, 2);

        // ── Snapshot ─────────────────────────────────────────────────────
        $snapshot = [
            'items'  => $quote->phases->map(fn($phase) => [
                'phase'      => $phase->type,
                'line_items' => $phase->lineItems->map(fn($item) => [
                    'name'             => $item->name,
                    'calculation_type' => $item->calculation_type,
                    'rate'             => $item->rate,
                    'quantity'         => $item->quantity,
                    'percentage_value' => $item->percentage_value,
                    'currency'         => $item->currency,
                    'conversion_rate'  => $item->conversion_rate,
                    'is_plugin'        => $item->is_plugin,
                    'total'            => $item->total,
                ])->values(),
            ])->values(),
            'totals' => [
                'subtotal'       => $subtotal,
                'main_subtotal'  => $mainSubtotal,
                'plugin_subtotal'=> $pluginSubtotal,
                'markup_rate'    => $blendedMarkupRate,
                'markup_amount'  => $markupAmount,
                'main_total'     => $mainTotal,
                'plugin_total'   => $pluginTotal,
                'total_ex_vat'   => $totalExVat,
                'vat'            => $vat,
                'total_inc_vat'  => $totalIncVat,
            ],
        ];

        $quote->update([
            'subtotal'      => $subtotal,
            'main_total'    => $mainTotal,
            'plugin_total'  => $pluginTotal,
            'markup_rate'   => $blendedMarkupRate,
            'markup_amount' => $markupAmount,
            'total_ex_vat'  => $totalExVat,
            'vat'           => $vat,
            'total_inc_vat' => $totalIncVat,
            'snapshot'      => $snapshot,
        ]);

        return $quote->fresh();
    }

    private function calculateItemTotal(object $item, float $subtotal): float
    {
        return match ($item->calculation_type) {
            'fixed'      => (float) ($item->rate ?? 0),
            'hourly'     => (float) ($item->rate ?? 0) * (float) ($item->quantity ?? 1),
            'converted'  => (float) ($item->rate ?? 0) * (float) ($item->conversion_rate ?? 1) * (float) ($item->quantity ?? 1),
            'percentage' => $subtotal * ((float) ($item->percentage_value ?? 0) / 100),
            default      => 0,
        };
    }

    private function resolveMarkupRate(float $amount): float
    {
        foreach (self::MARKUP_TIERS as $threshold => $rate) {
            if ($amount >= $threshold) {
                return $rate;
            }
        }

        return 22.5;
    }
}

<?php

return [

    /*
    |--------------------------------------------------------------------------
    | VAT Rate
    |--------------------------------------------------------------------------
    | Applied to the total ex-VAT when apply_vat is enabled on a quote.
    | Express as a decimal, e.g. 0.15 = 15%.
    */
    'vat_rate' => (float) env('QUOTE_VAT_RATE', 0.15),

    /*
    |--------------------------------------------------------------------------
    | Plugin Markup
    |--------------------------------------------------------------------------
    | Fixed markup percentage applied to all plugin line items.
    */
    'plugin_markup' => (float) env('QUOTE_PLUGIN_MARKUP', 10),

    /*
    |--------------------------------------------------------------------------
    | Markup Tiers
    |--------------------------------------------------------------------------
    | Tiered markup applied to the main (non-plugin) subtotal.
    | Format: [minimum_threshold => markup_percentage].
    | Must be ordered highest-first.
    */
    'markup_tiers' => [
        85000 => 10,
        55000 => 12,
        35500 => 15,
        12500 => 18,
        0     => 22.5,
    ],

];

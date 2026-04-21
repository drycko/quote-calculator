<?php

if (!function_exists('truncate')) {
    function truncate(string $text, int $length = 100, string $suffix = "..."): string
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        return substr($text, 0, $length) . $suffix;
    }
}
// get currency name by code
if (!function_exists('get_currency_name')) {
    function get_currency_name($currencyCode): string
    {
        $countries = get_countries();
        foreach ($countries as $country) {
            if (isset($country['currency']['code']) && $country['currency']['code'] === $currencyCode) {
                return $country['currency']['name'];
            }
        }
        return $currencyCode; // Fallback to code if name not found
    }
}

// get currency symbol by code
if (!function_exists('get_currency_symbol')) {
    function get_currency_symbol($currencyCode = null): string
    {
        
        if ($currencyCode === null) {
            $currencyCode = config('app.currency');
        }
        $countries = get_countries();
        foreach ($countries as $country) {
            if (isset($country['currency']['code']) && $country['currency']['code'] === $currencyCode) {
                return $country['currency']['symbol'];
            }
        }
        return '$'; // Fallback to dollar sign
    }
}

/*
I want to first read the countries from my json file and
return them as an array
*/
if (!function_exists('get_countries')) {
    /**
     * Get the list of countries from the JSON file.
     *
     * @return array
     */
    function get_countries(): array
    {   
        $filePath = public_path('vendor/countries.json');
        
        if (!file_exists($filePath)) {
            // Return a basic fallback with common countries/currencies
            return [
                [
                    'name' => 'South Africa',
                    'code' => 'ZA',
                    'currency' => ['code' => 'ZAR', 'name' => 'South African Rand', 'symbol' => 'R']
                ],
                [
                    'name' => 'United States',
                    'code' => 'US',
                    'currency' => ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$']
                ],
                [
                    'name' => 'United Kingdom',
                    'code' => 'GB',
                    'currency' => ['code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£']
                ],
                [
                    'name' => 'European Union',
                    'code' => 'EU',
                    'currency' => ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€']
                ]
            ];
        }
        
        $json = file_get_contents($filePath);
        $countries = json_decode($json, true);
        // order by name
        usort($countries, function ($a, $b) {
            return strcmp($a['name'], $b['name']);
        });
        return $countries;
    }
}

if (!function_exists('get_countries_list')) {
    /**
     * Get the list of countries as code => name pairs.
     *
     * @return array
     */
    function get_countries_list(): array
    {
        $countries = get_countries();
        $countryList = [];
        foreach ($countries as $country) {
            $countryList[$country['code']] = $country['name'];
        }
        return $countryList;
    }
}

// get currencies from countries.json
if (!function_exists('get_currencies')) {
    /**
     * Get the list of unique currencies from the countries JSON file.
     *
     * @return array
     */
    function get_currencies(): array
    {
        $countries = get_countries();
        $currencies = [];
        foreach ($countries as $country) {
            if (isset($country['currency']['code']) && !in_array($country['currency']['code'], $currencies)) {
                $currencies[] = $country['currency']['code'];
            }
        }
        sort($currencies);
        return $currencies;
    }
}

// format money with currency 
if (!function_exists('format_money')) {
    /**
     * Format a money with the given currency.
     *
     * @param float|int $amount The amount to format
     * @param string|null $currency The currency code (e.g., USD, ZAR). If null, app default currency
     * @param bool $showCurrency Whether to show the currency code
     * @return string
     */
    function format_money($amount, $currency = null, $showCurrency = true): string
    {
        if ($currency === null) {
            $currency = config('app.currency');
        }
        
        // Get currency symbol
        $symbol = get_currency_symbol($currency);
        
        $formattedAmount = number_format((float) $amount, 2, '.', ',');
        
        return $showCurrency ? "{$symbol}{$formattedAmount}" : $formattedAmount;
    }
}

// Fetch live conversion rate between two currencies.
// Uses frankfurter.app (ECB data, free, no API key).
// Results are cached for 24 hours; falls back to DEFAULT_CONVERSION_RATE env value.
if (!function_exists('get_conversion_rate')) {
    function get_conversion_rate(string $from = 'USD', string $to = 'ZAR'): float
    {
        if ($from === $to) {
            return 1.0;
        }

        $cacheKey = 'conversion_rate_' . strtoupper($from) . '_' . strtoupper($to);

        return (float) cache()->remember($cacheKey, now()->addHours(24), function () use ($from, $to) {
            try {
                $response = \Illuminate\Support\Facades\Http::timeout(5)
                    ->get('https://api.frankfurter.app/latest', [
                        'from' => strtoupper($from),
                        'to'   => strtoupper($to),
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $rate = $data['rates'][strtoupper($to)] ?? null;
                    if ($rate !== null) {
                        return (float) $rate;
                    }
                }
            } catch (\Throwable $e) {
                // fall through to default
            }

            return (float) config('app.default_conversion_rate', 18.50);
        });
    }
}
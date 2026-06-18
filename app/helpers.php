<?php

use App\Models\Currency;

if (! function_exists('money')) {
    /**
     * Format a monetary amount. Accepts a Currency model or an ISO code.
     * Falls back to a plain number when the currency is unknown.
     */
    function money(float|string|null $amount, Currency|string|null $currency = null): string
    {
        $amount = (float) $amount;

        if (is_string($currency)) {
            $currency = Currency::where('code', $currency)->first();
        }

        if ($currency instanceof Currency) {
            return $currency->format($amount);
        }

        return number_format($amount, 2);
    }
}

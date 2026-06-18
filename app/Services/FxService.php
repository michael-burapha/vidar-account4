<?php

namespace App\Services;

use App\Models\CompanySetting;
use App\Models\ExchangeRate;

/**
 * Resolves UZS-per-unit rates for converting foreign-currency amounts to the
 * base reporting currency. Returns null when no rate is on file so callers can
 * decide whether to require one.
 */
class FxService
{
    public function baseCurrencyId(): ?int
    {
        return CompanySetting::current()->base_currency_id;
    }

    /** UZS per 1 unit of the given currency on (or before) a date. 1.0 for the base currency. */
    public function rateFor(int $currencyId, ?string $onDate = null): ?float
    {
        if ($currencyId === $this->baseCurrencyId()) {
            return 1.0;
        }

        return ExchangeRate::rateFor($currencyId, $onDate);
    }

    /** Convert an amount in a currency to the base currency, or null if no rate. */
    public function toBase(float $amount, int $currencyId, ?string $onDate = null): ?float
    {
        $rate = $this->rateFor($currencyId, $onDate);

        return $rate === null ? null : round($amount * $rate, 2);
    }
}

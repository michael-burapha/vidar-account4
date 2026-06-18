<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    protected $guarded = [];

    protected $casts = [
        'rate_date' => 'date',
        'rate' => 'decimal:6',
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    /** Most recent UZS-per-unit rate for a currency on or before a given date. */
    public static function rateFor(int $currencyId, ?string $onDate = null): ?float
    {
        $query = static::where('currency_id', $currencyId);

        if ($onDate) {
            $query->whereDate('rate_date', '<=', $onDate);
        }

        $rate = $query->orderByDesc('rate_date')->value('rate');

        return $rate !== null ? (float) $rate : null;
    }
}

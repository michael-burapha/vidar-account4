<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    protected $guarded = [];

    protected $casts = [
        'it_park_resident' => 'boolean',
        'vat_exempt' => 'boolean',
        'it_park_fund_rate' => 'decimal:2',
        'invoice_terms_days' => 'integer',
    ];

    public function baseCurrency()
    {
        return $this->belongsTo(Currency::class, 'base_currency_id');
    }

    /** The single company-settings row, created on demand. */
    public static function current(): self
    {
        return static::query()->firstOrCreate(['id' => 1]);
    }
}

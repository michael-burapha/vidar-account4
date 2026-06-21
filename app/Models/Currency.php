<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'code', 'name', 'symbol', 'decimal_places', 'is_active',
    ];

    protected $casts = [
        'decimal_places' => 'integer',
        'is_active' => 'boolean',
    ];

    public function bankAccounts()
    {
        return $this->hasMany(BankAccount::class);
    }

    public function latestRate(): ?ExchangeRate
    {
        return $this->hasMany(ExchangeRate::class)->orderByDesc('rate_date')->first();
    }

    /** Format an amount in this currency, e.g. "$1,250.00". */
    public function format(float|string $amount): string
    {
        $formatted = number_format((float) $amount, $this->decimal_places);

        return $this->symbol !== ''
            ? trim($this->symbol . $formatted)
            : $this->code . ' ' . $formatted;
    }
}

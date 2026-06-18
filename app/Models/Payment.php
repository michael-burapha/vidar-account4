<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $guarded = [];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
        'amount_base' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        // Keep the parent invoice's paid total and status in sync.
        static::saved(fn (Payment $p) => $p->invoice?->recalculatePaidStatus());
        static::deleted(fn (Payment $p) => $p->invoice?->recalculatePaidStatus());
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}

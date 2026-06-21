<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->label ?: trim($this->bank_name . ' — ' . optional($this->currency)->code);
    }
}

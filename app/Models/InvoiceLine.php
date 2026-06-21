<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceLine extends Model
{
    protected $guarded = [];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'line_total' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /** Net line amount before tax. */
    public function net(): float
    {
        return round((float) $this->quantity * (float) $this->unit_price, 2);
    }

    public function taxAmount(): float
    {
        return round($this->net() * ((float) $this->tax_rate / 100), 2);
    }
}

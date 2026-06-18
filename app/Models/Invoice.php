<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    public const STATUSES = [
        'draft' => 'Draft',
        'sent' => 'Sent',
        'partially_paid' => 'Partially paid',
        'paid' => 'Paid',
        'overdue' => 'Overdue',
        'cancelled' => 'Cancelled',
    ];

    protected $guarded = [];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'contract_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'total' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
        'total_base' => 'decimal:2',
        'is_export' => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function lines()
    {
        return $this->hasMany(InvoiceLine::class)->orderBy('sort_order');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class)->orderBy('payment_date');
    }

    /** Recompute subtotal/tax/total from the saved lines. */
    public function recalculateTotals(): self
    {
        $lines = $this->lines()->get();

        $subtotal = round($lines->sum(fn (InvoiceLine $l) => $l->net()), 2);
        $tax = round($lines->sum(fn (InvoiceLine $l) => $l->taxAmount()), 2);

        $this->subtotal = $subtotal;
        $this->tax_total = $tax;
        $this->total = round($subtotal - (float) $this->discount_total + $tax, 2);

        if ($this->exchange_rate) {
            $this->total_base = round((float) $this->total * (float) $this->exchange_rate, 2);
        }

        return $this;
    }

    /** Sum payments, refresh amount_paid and derive the status. */
    public function recalculatePaidStatus(): void
    {
        $paid = round((float) $this->payments()->sum('amount'), 2);
        $this->amount_paid = $paid;

        if (! in_array($this->status, ['cancelled', 'draft'], true)) {
            if ($paid <= 0) {
                $this->status = $this->isPastDue() ? 'overdue' : 'sent';
            } elseif ($paid + 0.001 >= (float) $this->total) {
                $this->status = 'paid';
            } else {
                $this->status = 'partially_paid';
            }
        }

        $this->saveQuietly();
    }

    public function isPastDue(): bool
    {
        return $this->due_date
            && $this->due_date->isPast()
            && (float) $this->amount_paid + 0.001 < (float) $this->total;
    }

    public function balanceDue(): float
    {
        return round((float) $this->total - (float) $this->amount_paid, 2);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function statusColor(): string
    {
        return [
            'draft' => 'secondary',
            'sent' => 'info',
            'partially_paid' => 'warning',
            'paid' => 'success',
            'overdue' => 'danger',
            'cancelled' => 'dark',
        ][$this->status] ?? 'secondary';
    }

    public function scopeOutstanding(Builder $q): Builder
    {
        return $q->whereNotIn('status', ['paid', 'cancelled', 'draft']);
    }

    public function scopeRevenue(Builder $q): Builder
    {
        // Issued (non-draft, non-cancelled) invoices count as recognised revenue.
        return $q->whereNotIn('status', ['draft', 'cancelled']);
    }
}

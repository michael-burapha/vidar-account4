<?php

namespace App\Services;

use App\Models\CompanySetting;
use App\Models\Invoice;

class InvoiceNumberGenerator
{
    /**
     * Produce the next sequential invoice number, e.g. "VD-2026-0001".
     * Sequence resets per calendar year.
     */
    public function next(?int $year = null): string
    {
        $year ??= (int) date('Y');
        $prefix = CompanySetting::current()->invoice_prefix ?: 'VD';

        $like = sprintf('%s-%d-%%', $prefix, $year);

        $last = Invoice::where('invoice_number', 'like', $like)
            ->orderByDesc('invoice_number')
            ->value('invoice_number');

        $seq = $last ? ((int) substr($last, strrpos($last, '-') + 1)) + 1 : 1;

        return sprintf('%s-%d-%04d', $prefix, $year, $seq);
    }
}

<?php

namespace App\Services;

use App\Models\CompanySetting;
use App\Models\Invoice;
use Carbon\Carbon;

/**
 * IT Park residents pay a monthly contribution to the IT Park fund — by default
 * 1% of the resident's income — in place of the usual profit/turnover/VAT taxes.
 *
 * "Income" here is taken as invoiced revenue (issued, non-cancelled invoices),
 * converted to the base reporting currency (UZS) using the FX snapshot stored on
 * each invoice. Both the invoiced base and the received base are reported so the
 * accountant can reconcile against the chosen recognition basis.
 */
class ItParkFundCalculator
{
    public function forMonth(int $year, int $month): array
    {
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = (clone $start)->endOfMonth();

        return $this->forPeriod($start, $end);
    }

    public function forPeriod(Carbon $start, Carbon $end): array
    {
        $rate = (float) CompanySetting::current()->it_park_fund_rate;

        $invoices = Invoice::revenue()
            ->whereBetween('issue_date', [$start->toDateString(), $end->toDateString()])
            ->get();

        $invoicedBase = round($invoices->sum(fn (Invoice $i) => (float) ($i->total_base ?? 0)), 2);
        $exportBase = round(
            $invoices->where('is_export', true)->sum(fn (Invoice $i) => (float) ($i->total_base ?? 0)),
            2
        );

        return [
            'period_start' => $start->toDateString(),
            'period_end' => $end->toDateString(),
            'fund_rate' => $rate,
            'invoiced_base' => $invoicedBase,
            'export_base' => $exportBase,
            'export_share' => $invoicedBase > 0 ? round($exportBase / $invoicedBase * 100, 1) : 0.0,
            'fund_contribution' => round($invoicedBase * $rate / 100, 2),
            'currency' => optional(CompanySetting::current()->baseCurrency)->code ?? 'UZS',
        ];
    }
}

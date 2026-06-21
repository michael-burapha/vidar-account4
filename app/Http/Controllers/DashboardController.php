<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Currency;
use App\Models\Invoice;
use App\Services\ItParkFundCalculator;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(ItParkFundCalculator $fund)
    {
        $now = Carbon::now();

        // Outstanding (issued but not fully paid) grouped by currency.
        $outstanding = Invoice::outstanding()
            ->selectRaw('currency_id, SUM(total - amount_paid) as balance, COUNT(*) as cnt')
            ->groupBy('currency_id')
            ->with('currency')
            ->get();

        // This-year revenue per currency (issued invoices).
        $revenueByCurrency = Invoice::revenue()
            ->whereYear('issue_date', $now->year)
            ->selectRaw('currency_id, SUM(total) as total')
            ->groupBy('currency_id')
            ->with('currency')
            ->get();

        // Revenue split by client country (export vs domestic), in base currency.
        $byCountry = Invoice::revenue()
            ->whereYear('issue_date', $now->year)
            ->join('clients', 'clients.id', '=', 'invoices.client_id')
            ->selectRaw('clients.country, SUM(invoices.total_base) as base_total, COUNT(*) as cnt')
            ->groupBy('clients.country')
            ->get()
            ->map(function ($row) {
                $row->country_name = Client::countries()[strtoupper($row->country)] ?? $row->country;

                return $row;
            });

        return view('dashboard', [
            'outstanding' => $outstanding,
            'revenueByCurrency' => $revenueByCurrency,
            'byCountry' => $byCountry,
            'fund' => $fund->forMonth($now->year, $now->month),
            'fundYtd' => $fund->forPeriod($now->copy()->startOfYear(), $now->copy()->endOfYear()),
            'recentInvoices' => Invoice::with('client', 'currency')->latest()->limit(8)->get(),
            'overdueCount' => Invoice::where('status', 'overdue')->count(),
            'currencies' => Currency::where('is_active', true)->get(),
        ]);
    }
}

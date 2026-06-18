<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\ExchangeRate;
use Illuminate\Http\Request;

class ExchangeRateController extends Controller
{
    public function index()
    {
        return view('exchange-rates.index', [
            'rates' => ExchangeRate::with('currency')->orderByDesc('rate_date')->paginate(40),
            'currencies' => Currency::where('is_active', true)->where('code', '!=', 'UZS')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'currency_id' => ['required', 'exists:currencies,id'],
            'rate_date' => ['required', 'date'],
            'rate' => ['required', 'numeric', 'min:0'],
        ]);

        ExchangeRate::updateOrCreate(
            ['currency_id' => $data['currency_id'], 'rate_date' => $data['rate_date']],
            ['rate' => $data['rate'], 'source' => 'manual']
        );

        return back()->with('status', 'Exchange rate saved.');
    }

    public function destroy(ExchangeRate $exchangeRate)
    {
        $exchangeRate->delete();

        return back()->with('status', 'Exchange rate deleted.');
    }
}

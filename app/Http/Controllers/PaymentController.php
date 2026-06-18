<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Services\FxService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function store(Request $request, Invoice $invoice, FxService $fx)
    {
        $data = $request->validate([
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'bank_account_id' => ['nullable', 'exists:bank_accounts,id'],
            'method' => ['nullable', 'string', 'max:64'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        // Payments are received in the invoice currency.
        $rate = $fx->rateFor($invoice->currency_id, $data['payment_date']);

        $invoice->payments()->create($data + [
            'currency_id' => $invoice->currency_id,
            'exchange_rate' => $rate,
            'amount_base' => $rate ? round($data['amount'] * $rate, 2) : null,
        ]);

        return back()->with('status', 'Payment recorded.');
    }

    public function destroy(Invoice $invoice, Payment $payment)
    {
        abort_unless($payment->invoice_id === $invoice->id, 404);
        $payment->delete();

        return back()->with('status', 'Payment removed.');
    }
}

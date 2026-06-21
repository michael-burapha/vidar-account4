<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Client;
use App\Models\CompanySetting;
use App\Models\Currency;
use App\Models\Invoice;
use App\Services\FxService;
use App\Services\InvoiceNumberGenerator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with('client', 'currency')->latest('issue_date');

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }
        if ($clientId = $request->get('client_id')) {
            $query->where('client_id', $clientId);
        }

        return view('invoices.index', [
            'invoices' => $query->paginate(25)->withQueryString(),
            'clients' => Client::orderBy('name')->get(),
            'statuses' => Invoice::STATUSES,
            'filterStatus' => $status,
            'filterClient' => $clientId,
        ]);
    }

    public function create(InvoiceNumberGenerator $numbers)
    {
        $company = CompanySetting::current();

        $invoice = new Invoice([
            'invoice_number' => $numbers->next(),
            'issue_date' => Carbon::today()->toDateString(),
            'due_date' => Carbon::today()->addDays($company->invoice_terms_days)->toDateString(),
            'status' => 'draft',
        ]);

        return view('invoices.form', $this->formData($invoice));
    }

    public function store(Request $request, FxService $fx)
    {
        $data = $this->validateInvoice($request);
        $client = Client::findOrFail($data['client_id']);

        $invoice = new Invoice(Arr::except($data, 'lines'));
        $invoice->is_export = $client->isExport();
        $invoice->created_by = $request->user()->id;
        $invoice->exchange_rate = $fx->rateFor((int) $data['currency_id'], $data['issue_date']);
        $invoice->save();

        $this->syncLines($invoice, $request->input('lines', []));
        $invoice->recalculateTotals()->save();

        return redirect()->route('invoices.show', $invoice)->with('status', 'Invoice created.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('client', 'currency', 'bankAccount', 'lines', 'payments.bankAccount', 'payments.currency');

        return view('invoices.show', [
            'invoice' => $invoice,
            'company' => CompanySetting::current(),
            'bankAccounts' => BankAccount::with('currency')->where('is_active', true)->get(),
        ]);
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load('lines');

        return view('invoices.form', $this->formData($invoice));
    }

    public function update(Request $request, Invoice $invoice, FxService $fx)
    {
        $data = $this->validateInvoice($request);
        $client = Client::findOrFail($data['client_id']);

        $invoice->fill(Arr::except($data, 'lines'));
        $invoice->is_export = $client->isExport();
        $invoice->exchange_rate = $fx->rateFor((int) $data['currency_id'], $data['issue_date']);
        $invoice->save();

        $this->syncLines($invoice, $request->input('lines', []));
        $invoice->recalculateTotals()->save();
        $invoice->recalculatePaidStatus();

        return redirect()->route('invoices.show', $invoice)->with('status', 'Invoice updated.');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return redirect()->route('invoices.index')->with('status', 'Invoice deleted.');
    }

    /** Transition a draft to "sent". */
    public function markSent(Invoice $invoice)
    {
        if ($invoice->status === 'draft') {
            $invoice->status = 'sent';
            $invoice->save();
            $invoice->recalculatePaidStatus();
        }

        return back()->with('status', 'Invoice marked as sent.');
    }

    public function pdf(Invoice $invoice)
    {
        $invoice->load('client', 'currency', 'bankAccount.currency', 'lines');

        $pdf = Pdf::loadView('invoices.pdf', [
            'invoice' => $invoice,
            'company' => CompanySetting::current(),
        ])->setPaper('a4');

        return $pdf->stream($invoice->invoice_number . '.pdf');
    }

    // ----------------------------------------------------------------------

    private function formData(Invoice $invoice): array
    {
        return [
            'invoice' => $invoice,
            'clients' => Client::where('is_active', true)->orderBy('name')->get(),
            'currencies' => Currency::where('is_active', true)->get(),
            'bankAccounts' => BankAccount::with('currency')->where('is_active', true)->get(),
        ];
    }

    private function validateInvoice(Request $request): array
    {
        return $request->validate([
            'invoice_number' => ['required', 'string', 'max:64'],
            'client_id' => ['required', 'exists:clients,id'],
            'currency_id' => ['required', 'exists:currencies,id'],
            'bank_account_id' => ['nullable', 'exists:bank_accounts,id'],
            'issue_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:issue_date'],
            'status' => ['required', 'in:' . implode(',', array_keys(Invoice::STATUSES))],
            'discount_total' => ['nullable', 'numeric', 'min:0'],
            'contract_no' => ['nullable', 'string', 'max:64'],
            'contract_date' => ['nullable', 'date'],
            'act_no' => ['nullable', 'string', 'max:64'],
            'notes' => ['nullable', 'string'],
            'terms' => ['nullable', 'string'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.description' => ['required', 'string', 'max:500'],
            'lines.*.quantity' => ['required', 'numeric', 'min:0'],
            'lines.*.unit' => ['nullable', 'string', 'max:32'],
            'lines.*.unit_price' => ['required', 'numeric'],
            'lines.*.tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);
    }

    private function syncLines(Invoice $invoice, array $lines): void
    {
        $invoice->lines()->delete();

        foreach (array_values($lines) as $i => $line) {
            $qty = (float) ($line['quantity'] ?? 0);
            $price = (float) ($line['unit_price'] ?? 0);

            $invoice->lines()->create([
                'description' => $line['description'],
                'quantity' => $qty,
                'unit' => $line['unit'] ?? 'service',
                'unit_price' => $price,
                'tax_rate' => (float) ($line['tax_rate'] ?? 0),
                'line_total' => round($qty * $price, 2),
                'sort_order' => $i,
            ]);
        }
    }
}

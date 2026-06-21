@extends('layouts.app')
@section('title', $invoice->invoice_number)
@section('heading', 'Invoice ' . $invoice->invoice_number)
@section('actions')
    <a href="{{ route('invoices.pdf', $invoice) }}" target="_blank" class="btn btn-outline-secondary"><i class="bi bi-file-pdf"></i> PDF</a>
    <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-outline-secondary"><i class="bi bi-pencil"></i> Edit</a>
    @if ($invoice->status === 'draft')
        <form action="{{ route('invoices.mark-sent', $invoice) }}" method="POST" class="d-inline">
            @csrf <button class="btn btn-primary"><i class="bi bi-send"></i> Mark sent</button>
        </form>
    @endif
@endsection

@section('content')
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card mb-3"><div class="card-body">
            <div class="d-flex justify-content-between">
                <div>
                    <div class="text-muted small">Billed to</div>
                    <strong>{{ $invoice->client->name }}</strong><br>
                    <span class="text-muted">{{ $invoice->client->country_name }}</span>
                </div>
                <div class="text-end">
                    <span class="badge bg-{{ $invoice->statusColor() }} fs-6">{{ $invoice->statusLabel() }}</span>
                    @if ($invoice->is_export)<div><span class="badge bg-info-subtle text-info-emphasis mt-1">export of services</span></div>@endif
                </div>
            </div>
            <hr>
            <table class="table table-sm mb-0">
                <thead><tr><th>Description</th><th class="text-end">Qty</th><th class="text-end">Unit price</th><th class="text-end">Total</th></tr></thead>
                <tbody>
                @foreach ($invoice->lines as $line)
                    <tr>
                        <td>{{ $line->description }}</td>
                        <td class="text-end">{{ rtrim(rtrim(number_format($line->quantity, 4), '0'), '.') }} {{ $line->unit }}</td>
                        <td class="text-end">{{ money($line->unit_price, $invoice->currency) }}</td>
                        <td class="text-end">{{ money($line->line_total, $invoice->currency) }}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                    <tr><td colspan="3" class="text-end">Subtotal</td><td class="text-end">{{ money($invoice->subtotal, $invoice->currency) }}</td></tr>
                    @if ((float) $invoice->discount_total > 0)
                        <tr><td colspan="3" class="text-end">Discount</td><td class="text-end">−{{ money($invoice->discount_total, $invoice->currency) }}</td></tr>
                    @endif
                    @if ((float) $invoice->tax_total > 0)
                        <tr><td colspan="3" class="text-end">Tax</td><td class="text-end">{{ money($invoice->tax_total, $invoice->currency) }}</td></tr>
                    @endif
                    <tr class="fw-bold"><td colspan="3" class="text-end">Total</td><td class="text-end">{{ money($invoice->total, $invoice->currency) }}</td></tr>
                    <tr><td colspan="3" class="text-end text-muted">Paid</td><td class="text-end text-muted">{{ money($invoice->amount_paid, $invoice->currency) }}</td></tr>
                    <tr class="fw-bold"><td colspan="3" class="text-end">Balance due</td><td class="text-end">{{ money($invoice->balanceDue(), $invoice->currency) }}</td></tr>
                </tfoot>
            </table>
            @if ($invoice->notes)<div class="mt-3 small"><strong>Notes:</strong> {{ $invoice->notes }}</div>@endif
        </div></div>

        <div class="card"><div class="card-body">
            <h2 class="h6 text-muted">Payments</h2>
            <table class="table table-sm align-middle">
                <thead><tr><th>Date</th><th>Account</th><th>Reference</th><th class="text-end">Amount</th><th></th></tr></thead>
                <tbody>
                @forelse ($invoice->payments as $p)
                    <tr>
                        <td>{{ $p->payment_date->format('Y-m-d') }}</td>
                        <td>{{ optional($p->bankAccount)->display_name ?? '—' }}</td>
                        <td>{{ $p->reference ?? '—' }}</td>
                        <td class="text-end">{{ money($p->amount, $invoice->currency) }}</td>
                        <td class="text-end">
                            <form action="{{ route('payments.destroy', [$invoice, $p]) }}" method="POST" onsubmit="return confirm('Remove payment?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">×</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-muted">No payments recorded.</td></tr>
                @endforelse
                </tbody>
            </table>

            @if ($invoice->balanceDue() > 0 && ! in_array($invoice->status, ['draft', 'cancelled']))
                <form action="{{ route('payments.store', $invoice) }}" method="POST" class="row g-2 align-items-end mt-2">
                    @csrf
                    <div class="col-md-3">
                        <label class="form-label small mb-0">Date</label>
                        <input type="date" name="payment_date" value="{{ now()->toDateString() }}" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small mb-0">Amount ({{ $invoice->currency->code }})</label>
                        <input type="number" step="0.01" name="amount" value="{{ $invoice->balanceDue() }}" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small mb-0">Account</label>
                        <select name="bank_account_id" class="form-select form-select-sm">
                            <option value="">—</option>
                            @foreach ($bankAccounts as $a)
                                <option value="{{ $a->id }}" @selected($invoice->bank_account_id == $a->id || (! $invoice->bank_account_id && $a->currency_id == $invoice->currency_id))>{{ $a->display_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-0">Reference</label>
                        <input name="reference" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-sm btn-primary w-100">Add</button>
                    </div>
                </form>
            @endif
        </div></div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-3"><div class="card-body">
            <h2 class="h6 text-muted">Details</h2>
            <dl class="row small mb-0">
                <dt class="col-5">Issue date</dt><dd class="col-7">{{ $invoice->issue_date->format('Y-m-d') }}</dd>
                <dt class="col-5">Due date</dt><dd class="col-7">{{ $invoice->due_date->format('Y-m-d') }}</dd>
                <dt class="col-5">Currency</dt><dd class="col-7">{{ $invoice->currency->code }}</dd>
                @if ($invoice->exchange_rate)
                    <dt class="col-5">FX (UZS/unit)</dt><dd class="col-7">{{ number_format($invoice->exchange_rate, 4) }}</dd>
                    <dt class="col-5">Total (base)</dt><dd class="col-7">{{ number_format($invoice->total_base) }} UZS</dd>
                @endif
                <dt class="col-5">Pay into</dt><dd class="col-7">{{ optional($invoice->bankAccount)->display_name ?? '—' }}</dd>
            </dl>
        </div></div>

        @if ($invoice->is_export)
        <div class="card mb-3"><div class="card-body">
            <h2 class="h6 text-muted">Export registration (EEISVO)</h2>
            <dl class="row small mb-0">
                <dt class="col-5">Contract no.</dt><dd class="col-7">{{ $invoice->contract_no ?? '—' }}</dd>
                <dt class="col-5">Contract date</dt><dd class="col-7">{{ optional($invoice->contract_date)->format('Y-m-d') ?? '—' }}</dd>
                <dt class="col-5">Act no.</dt><dd class="col-7">{{ $invoice->act_no ?? '—' }}</dd>
            </dl>
            @if ((float) $invoice->total_base < 5000 * ($invoice->exchange_rate ?? 0) || ! $invoice->exchange_rate)
                <p class="small text-muted mt-2 mb-0">Deals up to USD 5,000 may be formalised by invoice alone; larger contracts must be registered in EEISVO.</p>
            @endif
        </div></div>
        @endif

        <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" onsubmit="return confirm('Delete this invoice?')">
            @csrf @method('DELETE')
            <button class="btn btn-outline-danger btn-sm w-100">Delete invoice</button>
        </form>
    </div>
</div>
@endsection

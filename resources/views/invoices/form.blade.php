@extends('layouts.app')
@section('title', $invoice->exists ? 'Edit invoice' : 'New invoice')
@section('heading', $invoice->exists ? 'Edit invoice ' . $invoice->invoice_number : 'New invoice')

@section('content')
@php
    $oldLines = old('lines', $invoice->exists ? $invoice->lines->toArray() : [
        ['description' => '', 'quantity' => 1, 'unit' => 'service', 'unit_price' => '', 'tax_rate' => 0],
    ]);
@endphp

<form method="POST" action="{{ $invoice->exists ? route('invoices.update', $invoice) : route('invoices.store') }}">
    @csrf
    @if ($invoice->exists) @method('PUT') @endif

    <div class="card mb-3"><div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Invoice number *</label>
                <input name="invoice_number" value="{{ old('invoice_number', $invoice->invoice_number) }}" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Client *</label>
                <select name="client_id" class="form-select" required>
                    <option value="">— select —</option>
                    @foreach ($clients as $c)
                        <option value="{{ $c->id }}" data-currency="{{ $c->default_currency_id }}"
                            @selected(old('client_id', $invoice->client_id) == $c->id)>{{ $c->name }} ({{ $c->country }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Currency *</label>
                <select name="currency_id" id="currency_id" class="form-select" required>
                    @foreach ($currencies as $c)
                        <option value="{{ $c->id }}" data-symbol="{{ $c->symbol }}" data-code="{{ $c->code }}"
                            @selected(old('currency_id', $invoice->currency_id) == $c->id)>{{ $c->code }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Pay into account</label>
                <select name="bank_account_id" class="form-select">
                    <option value="">— none —</option>
                    @foreach ($bankAccounts as $a)
                        <option value="{{ $a->id }}" data-currency="{{ $a->currency_id }}"
                            @selected(old('bank_account_id', $invoice->bank_account_id) == $a->id)>{{ $a->display_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Issue date *</label>
                <input type="date" name="issue_date" value="{{ old('issue_date', optional($invoice->issue_date)->format('Y-m-d')) }}" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Due date *</label>
                <input type="date" name="due_date" value="{{ old('due_date', optional($invoice->due_date)->format('Y-m-d')) }}" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status *</label>
                <select name="status" class="form-select" required>
                    @foreach (\App\Models\Invoice::STATUSES as $key => $label)
                        <option value="{{ $key }}" @selected(old('status', $invoice->status) === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Discount (total)</label>
                <input type="number" step="0.01" name="discount_total" value="{{ old('discount_total', $invoice->discount_total ?? 0) }}" class="form-control" id="discount">
            </div>
        </div>
    </div></div>

    <div class="card mb-3"><div class="card-body">
        <h2 class="h6 text-muted">Lines</h2>
        <table class="table align-middle" id="lines">
            <thead><tr>
                <th style="width:42%">Description</th><th>Qty</th><th>Unit</th>
                <th>Unit price</th><th class="text-end">Line total</th><th></th>
            </tr></thead>
            <tbody></tbody>
        </table>
        <button type="button" class="btn btn-sm btn-outline-primary" id="addLine"><i class="bi bi-plus"></i> Add line</button>

        <div class="row justify-content-end mt-3">
            <div class="col-md-4">
                <div class="d-flex justify-content-between"><span>Subtotal</span><strong id="subtotal">0.00</strong></div>
                <div class="d-flex justify-content-between"><span>Discount</span><strong id="discountView">0.00</strong></div>
                <div class="d-flex justify-content-between"><span>Tax</span><strong id="taxtotal">0.00</strong></div>
                <hr class="my-2">
                <div class="d-flex justify-content-between fs-5"><span>Total</span><strong id="total">0.00</strong></div>
            </div>
        </div>
    </div></div>

    <div class="card mb-3"><div class="card-body">
        <h2 class="h6 text-muted">Export of services <span class="fw-normal small">(for foreign clients — EEISVO contract / act references)</span></h2>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Contract no.</label>
                <input name="contract_no" value="{{ old('contract_no', $invoice->contract_no) }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Contract date</label>
                <input type="date" name="contract_date" value="{{ old('contract_date', optional($invoice->contract_date)->format('Y-m-d')) }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Act no.</label>
                <input name="act_no" value="{{ old('act_no', $invoice->act_no) }}" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Notes (shown on invoice)</label>
                <textarea name="notes" class="form-control" rows="2">{{ old('notes', $invoice->notes) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Terms (shown on invoice)</label>
                <textarea name="terms" class="form-control" rows="2">{{ old('terms', $invoice->terms) }}</textarea>
            </div>
        </div>
    </div></div>

    <button class="btn btn-primary">Save invoice</button>
    <a href="{{ route('invoices.index') }}" class="btn btn-link">Cancel</a>
</form>

<template id="lineTemplate">
    <tr>
        <td><input name="lines[IDX][description]" class="form-control form-control-sm desc"></td>
        <td><input name="lines[IDX][quantity]" type="number" step="0.0001" value="1" class="form-control form-control-sm qty" style="width:90px"></td>
        <td><input name="lines[IDX][unit]" value="service" class="form-control form-control-sm" style="width:100px"></td>
        <td><input name="lines[IDX][unit_price]" type="number" step="0.01" class="form-control form-control-sm price" style="width:120px"></td>
        <td class="text-end lineTotal">0.00</td>
        <td><input type="hidden" name="lines[IDX][tax_rate]" value="0">
            <button type="button" class="btn btn-sm btn-outline-danger removeLine">×</button></td>
    </tr>
</template>

@push('scripts')
<script>
    const tbody = document.querySelector('#lines tbody');
    const tpl = document.querySelector('#lineTemplate').innerHTML;
    let idx = 0;

    function symbol() {
        const o = document.querySelector('#currency_id').selectedOptions[0];
        return o ? (o.dataset.symbol || o.dataset.code + ' ') : '';
    }
    function fmt(n) { return symbol() + Number(n || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}); }

    function recalc() {
        let sub = 0;
        tbody.querySelectorAll('tr').forEach(tr => {
            const q = parseFloat(tr.querySelector('.qty').value) || 0;
            const p = parseFloat(tr.querySelector('.price').value) || 0;
            const lt = q * p;
            tr.querySelector('.lineTotal').textContent = fmt(lt);
            sub += lt;
        });
        const disc = parseFloat(document.querySelector('#discount').value) || 0;
        document.querySelector('#subtotal').textContent = fmt(sub);
        document.querySelector('#discountView').textContent = fmt(disc);
        document.querySelector('#taxtotal').textContent = fmt(0);
        document.querySelector('#total').textContent = fmt(sub - disc);
    }

    function addLine(data = {}) {
        const html = tpl.replaceAll('IDX', idx++);
        const tr = document.createElement('tr');
        tr.innerHTML = html.trim().replace(/^<tr>|<\/tr>$/g, '');
        tbody.appendChild(tr);
        if (data.description !== undefined) tr.querySelector('.desc').value = data.description ?? '';
        if (data.quantity !== undefined) tr.querySelector('.qty').value = data.quantity ?? 1;
        if (data.unit !== undefined) tr.querySelector('[name$="[unit]"]').value = data.unit ?? 'service';
        if (data.unit_price !== undefined) tr.querySelector('.price').value = data.unit_price ?? '';
        tr.querySelectorAll('input').forEach(i => i.addEventListener('input', recalc));
        tr.querySelector('.removeLine').addEventListener('click', () => { tr.remove(); recalc(); });
        recalc();
    }

    document.querySelector('#addLine').addEventListener('click', () => addLine());
    document.querySelector('#discount').addEventListener('input', recalc);
    document.querySelector('#currency_id').addEventListener('change', recalc);

    // Auto-pick the client's default currency when chosen.
    document.querySelector('[name=client_id]').addEventListener('change', function () {
        const cur = this.selectedOptions[0]?.dataset.currency;
        if (cur) document.querySelector('#currency_id').value = cur;
        recalc();
    });

    const existing = @json($oldLines);
    if (existing.length) { existing.forEach(addLine); } else { addLine(); }
</script>
@endpush
@endsection

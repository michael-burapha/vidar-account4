<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { font-size: 12px; color: #1e293b; margin: 0; }
        .wrap { padding: 36px 40px; }
        h1 { font-size: 26px; margin: 0; color: #0f172a; letter-spacing: .04em; }
        .muted { color: #64748b; }
        .right { text-align: right; }
        table { width: 100%; border-collapse: collapse; }
        .meta td { padding: 2px 0; vertical-align: top; }
        .lines th { background: #0f172a; color: #fff; padding: 8px; text-align: left; font-size: 11px; }
        .lines td { padding: 8px; border-bottom: 1px solid #e2e8f0; }
        .totals td { padding: 4px 8px; }
        .totals .grand { font-size: 15px; font-weight: bold; border-top: 2px solid #0f172a; }
        .box { border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 12px; }
        .badge { background: #e0f2fe; color: #0369a1; padding: 2px 8px; border-radius: 10px; font-size: 10px; }
        .footer { margin-top: 28px; font-size: 10px; color: #64748b; border-top: 1px solid #e2e8f0; padding-top: 10px; }
    </style>
</head>
<body>
<div class="wrap">
    <table>
        <tr>
            <td style="width:55%">
                <h1>{{ $company->brand_name }}</h1>
                <div class="muted">
                    {{ $company->address }}@if ($company->city), {{ $company->city }}@endif<br>
                    @if ($company->tax_id_stir)STIR: {{ $company->tax_id_stir }}<br>@endif
                    @if ($company->it_park_reg_no)IT Park resident · {{ $company->it_park_reg_no }}<br>@endif
                    @if ($company->email){{ $company->email }} @endif @if ($company->phone)· {{ $company->phone }}@endif
                </div>
            </td>
            <td class="right">
                <div style="font-size:20px; font-weight:bold;">INVOICE</div>
                <div class="muted">{{ $invoice->invoice_number }}</div>
                @if ($invoice->is_export)<div style="margin-top:6px"><span class="badge">EXPORT OF SERVICES</span></div>@endif
            </td>
        </tr>
    </table>

    <table class="meta" style="margin-top:24px">
        <tr>
            <td style="width:55%">
                <div class="muted" style="text-transform:uppercase; font-size:10px;">Bill to</div>
                <strong>{{ $invoice->client->legal_name ?: $invoice->client->name }}</strong><br>
                {{ $invoice->client->address }}@if ($invoice->client->city)<br>{{ $invoice->client->city }}@endif
                @if ($invoice->client->postcode) {{ $invoice->client->postcode }}@endif<br>
                {{ $invoice->client->country_name }}
                @if ($invoice->client->tax_id)<br>Tax ID: {{ $invoice->client->tax_id }}@endif
            </td>
            <td class="right">
                <table style="width:100%">
                    <tr><td class="right muted">Issue date</td><td class="right">{{ $invoice->issue_date->format('d M Y') }}</td></tr>
                    <tr><td class="right muted">Due date</td><td class="right">{{ $invoice->due_date->format('d M Y') }}</td></tr>
                    <tr><td class="right muted">Currency</td><td class="right">{{ $invoice->currency->code }}</td></tr>
                    @if ($invoice->contract_no)
                        <tr><td class="right muted">Contract</td><td class="right">{{ $invoice->contract_no }}</td></tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    <table class="lines" style="margin-top:18px">
        <thead>
            <tr>
                <th style="width:50%">Description</th>
                <th class="right">Qty</th>
                <th class="right">Unit price</th>
                <th class="right">Amount</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($invoice->lines as $line)
            <tr>
                <td>{{ $line->description }}</td>
                <td class="right">{{ rtrim(rtrim(number_format($line->quantity, 4), '0'), '.') }} {{ $line->unit }}</td>
                <td class="right">{{ $invoice->currency->format($line->unit_price) }}</td>
                <td class="right">{{ $invoice->currency->format($line->line_total) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <table style="margin-top:10px">
        <tr>
            <td style="width:55%; vertical-align:top">
                @if ($invoice->bankAccount)
                    <div class="box">
                        <div class="muted" style="text-transform:uppercase; font-size:10px;">Payment details</div>
                        <strong>{{ $invoice->bankAccount->bank_name }}</strong> ({{ optional($invoice->bankAccount->currency)->code }})<br>
                        @if ($invoice->bankAccount->account_name)Beneficiary: {{ $invoice->bankAccount->account_name }}<br>@endif
                        @if ($invoice->bankAccount->iban)IBAN: {{ $invoice->bankAccount->iban }}<br>@endif
                        @if ($invoice->bankAccount->account_number)Account: {{ $invoice->bankAccount->account_number }}<br>@endif
                        @if ($invoice->bankAccount->swift)SWIFT/BIC: {{ $invoice->bankAccount->swift }}<br>@endif
                        @if ($invoice->bankAccount->mfo)MFO: {{ $invoice->bankAccount->mfo }}<br>@endif
                        @if ($invoice->bankAccount->correspondent_bank)
                            <span class="muted">Correspondent:</span> {{ $invoice->bankAccount->correspondent_bank }}
                            @if ($invoice->bankAccount->correspondent_swift)({{ $invoice->bankAccount->correspondent_swift }})@endif<br>
                        @endif
                    </div>
                @endif
            </td>
            <td style="vertical-align:top">
                <table class="totals">
                    <tr><td class="muted">Subtotal</td><td class="right">{{ $invoice->currency->format($invoice->subtotal) }}</td></tr>
                    @if ((float) $invoice->discount_total > 0)
                        <tr><td class="muted">Discount</td><td class="right">−{{ $invoice->currency->format($invoice->discount_total) }}</td></tr>
                    @endif
                    @if ((float) $invoice->tax_total > 0)
                        <tr><td class="muted">Tax</td><td class="right">{{ $invoice->currency->format($invoice->tax_total) }}</td></tr>
                    @elseif ($company->vat_exempt)
                        <tr><td class="muted">VAT</td><td class="right">Exempt</td></tr>
                    @endif
                    <tr class="grand"><td>Total</td><td class="right">{{ $invoice->currency->format($invoice->total) }}</td></tr>
                    @if ((float) $invoice->amount_paid > 0)
                        <tr><td class="muted">Paid</td><td class="right">{{ $invoice->currency->format($invoice->amount_paid) }}</td></tr>
                        <tr class="grand"><td>Balance due</td><td class="right">{{ $invoice->currency->format($invoice->balanceDue()) }}</td></tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    @if ($invoice->notes)<p style="margin-top:18px"><strong>Notes:</strong> {{ $invoice->notes }}</p>@endif
    @if ($invoice->terms)<p class="muted">{{ $invoice->terms }}</p>@endif

    <div class="footer">
        @if ($company->vat_exempt)Services exported from Uzbekistan are exempt from VAT. @endif
        {{ $company->invoice_footer }}
    </div>
</div>
</body>
</html>

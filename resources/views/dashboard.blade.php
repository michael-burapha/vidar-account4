@extends('layouts.app')
@section('title', 'Dashboard')
@section('heading', 'Dashboard')
@section('actions')
    <a href="{{ route('invoices.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New invoice</a>
@endsection

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card h-100"><div class="card-body">
            <div class="text-muted small text-uppercase">IT Park fund — this month</div>
            <div class="h4 mb-0">{{ number_format($fund['fund_contribution']) }} {{ $fund['currency'] }}</div>
            <div class="small text-muted">{{ $fund['fund_rate'] }}% of {{ number_format($fund['invoiced_base']) }} invoiced</div>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card h-100"><div class="card-body">
            <div class="text-muted small text-uppercase">Export share (YTD)</div>
            <div class="h4 mb-0">{{ $fundYtd['export_share'] }}%</div>
            <div class="small text-muted">{{ number_format($fundYtd['export_base']) }} {{ $fundYtd['currency'] }} exported</div>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card h-100"><div class="card-body">
            <div class="text-muted small text-uppercase">Revenue YTD (base)</div>
            <div class="h4 mb-0">{{ number_format($fundYtd['invoiced_base']) }} {{ $fundYtd['currency'] }}</div>
            <div class="small text-muted">{{ now()->year }} issued invoices</div>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card h-100"><div class="card-body">
            <div class="text-muted small text-uppercase">Overdue</div>
            <div class="h4 mb-0 {{ $overdueCount ? 'text-danger' : '' }}">{{ $overdueCount }}</div>
            <div class="small text-muted">invoices past due</div>
        </div></div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card"><div class="card-body">
            <h2 class="h6 text-uppercase text-muted">Outstanding by currency</h2>
            @forelse ($outstanding as $row)
                <div class="d-flex justify-content-between border-bottom py-2">
                    <span>{{ $row->currency->code }} <span class="text-muted small">({{ $row->cnt }})</span></span>
                    <strong>{{ money($row->balance, $row->currency) }}</strong>
                </div>
            @empty
                <p class="text-muted mb-0">Nothing outstanding.</p>
            @endforelse
        </div></div>
    </div>

    <div class="col-lg-4">
        <div class="card"><div class="card-body">
            <h2 class="h6 text-uppercase text-muted">Revenue {{ now()->year }} by currency</h2>
            @forelse ($revenueByCurrency as $row)
                <div class="d-flex justify-content-between border-bottom py-2">
                    <span>{{ $row->currency->code }}</span>
                    <strong>{{ money($row->total, $row->currency) }}</strong>
                </div>
            @empty
                <p class="text-muted mb-0">No revenue yet.</p>
            @endforelse
        </div></div>
    </div>

    <div class="col-lg-4">
        <div class="card"><div class="card-body">
            <h2 class="h6 text-uppercase text-muted">Revenue by country (base)</h2>
            @forelse ($byCountry as $row)
                <div class="d-flex justify-content-between border-bottom py-2">
                    <span>{{ $row->country_name }}
                        @if (strtoupper($row->country) !== 'UZ')<span class="badge bg-info-subtle text-info-emphasis">export</span>@endif
                    </span>
                    <strong>{{ number_format($row->base_total) }}</strong>
                </div>
            @empty
                <p class="text-muted mb-0">No revenue yet.</p>
            @endforelse
        </div></div>
    </div>
</div>

<div class="card mt-3"><div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h2 class="h6 text-uppercase text-muted mb-0">Recent invoices</h2>
        <a href="{{ route('invoices.index') }}" class="small">View all</a>
    </div>
    <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
            <thead><tr><th>Number</th><th>Client</th><th>Issued</th><th class="text-end">Total</th><th>Status</th></tr></thead>
            <tbody>
            @forelse ($recentInvoices as $inv)
                <tr>
                    <td><a href="{{ route('invoices.show', $inv) }}">{{ $inv->invoice_number }}</a></td>
                    <td>{{ $inv->client->name }}</td>
                    <td>{{ $inv->issue_date->format('Y-m-d') }}</td>
                    <td class="text-end">{{ money($inv->total, $inv->currency) }}</td>
                    <td><span class="badge bg-{{ $inv->statusColor() }}">{{ $inv->statusLabel() }}</span></td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-muted">No invoices yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div></div>
@endsection

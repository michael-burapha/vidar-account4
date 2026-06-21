@extends('layouts.app')
@section('title', 'Invoices')
@section('heading', 'Invoices')
@section('actions')
    <a href="{{ route('invoices.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New invoice</a>
@endsection

@section('content')
<div class="card mb-3"><div class="card-body py-2">
    <form class="row g-2 align-items-end" method="GET">
        <div class="col-md-3">
            <label class="form-label small mb-0">Status</label>
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All</option>
                @foreach ($statuses as $key => $label)
                    <option value="{{ $key }}" @selected($filterStatus === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small mb-0">Client</label>
            <select name="client_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All</option>
                @foreach ($clients as $c)
                    <option value="{{ $c->id }}" @selected($filterClient == $c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-link">Reset</a>
        </div>
    </form>
</div></div>

<div class="card"><div class="card-body">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr>
                <th>Number</th><th>Client</th><th>Issued</th><th>Due</th>
                <th class="text-end">Total</th><th class="text-end">Balance</th><th>Status</th><th></th>
            </tr></thead>
            <tbody>
            @forelse ($invoices as $inv)
                <tr>
                    <td><a href="{{ route('invoices.show', $inv) }}">{{ $inv->invoice_number }}</a>
                        @if ($inv->is_export)<span class="badge bg-info-subtle text-info-emphasis">export</span>@endif</td>
                    <td>{{ $inv->client->name }}</td>
                    <td>{{ $inv->issue_date->format('Y-m-d') }}</td>
                    <td>{{ $inv->due_date->format('Y-m-d') }}</td>
                    <td class="text-end">{{ money($inv->total, $inv->currency) }}</td>
                    <td class="text-end">{{ money($inv->balanceDue(), $inv->currency) }}</td>
                    <td><span class="badge bg-{{ $inv->statusColor() }}">{{ $inv->statusLabel() }}</span></td>
                    <td class="text-end">
                        <a href="{{ route('invoices.pdf', $inv) }}" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="bi bi-file-pdf"></i></a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-muted">No invoices found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div></div>
<div class="mt-3">{{ $invoices->links() }}</div>
@endsection

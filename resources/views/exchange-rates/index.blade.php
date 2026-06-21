@extends('layouts.app')
@section('title', 'Exchange rates')
@section('heading', 'Exchange rates')

@section('content')
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card"><div class="card-body">
            <h2 class="h6 text-muted">Add rate</h2>
            <p class="small text-muted">UZS per 1 unit (CBU official rate).</p>
            <form method="POST" action="{{ route('exchange-rates.store') }}">
                @csrf
                <div class="mb-2">
                    <label class="form-label">Currency</label>
                    <select name="currency_id" class="form-select" required>
                        @foreach ($currencies as $c)
                            <option value="{{ $c->id }}">{{ $c->code }} — {{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label">Date</label>
                    <input type="date" name="rate_date" value="{{ now()->toDateString() }}" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Rate (UZS per unit)</label>
                    <input type="number" step="0.000001" name="rate" class="form-control" required>
                </div>
                <button class="btn btn-primary w-100">Save rate</button>
            </form>
        </div></div>
    </div>

    <div class="col-lg-8">
        <div class="card"><div class="card-body">
            <table class="table table-sm align-middle mb-0">
                <thead><tr><th>Date</th><th>Currency</th><th class="text-end">UZS / unit</th><th>Source</th><th></th></tr></thead>
                <tbody>
                @forelse ($rates as $rate)
                    <tr>
                        <td>{{ $rate->rate_date->format('Y-m-d') }}</td>
                        <td>{{ $rate->currency->code }}</td>
                        <td class="text-end">{{ number_format($rate->rate, 4) }}</td>
                        <td><span class="badge bg-light text-dark">{{ $rate->source }}</span></td>
                        <td class="text-end">
                            <form action="{{ route('exchange-rates.destroy', $rate) }}" method="POST"
                                  onsubmit="return confirm('Delete this rate?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">×</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-muted">No rates yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div></div>
        <div class="mt-3">{{ $rates->links() }}</div>
    </div>
</div>
@endsection

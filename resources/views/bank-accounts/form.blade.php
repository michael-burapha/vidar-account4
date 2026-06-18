@extends('layouts.app')
@section('title', $account->exists ? 'Edit account' : 'New account')
@section('heading', $account->exists ? 'Edit bank account' : 'New bank account')

@section('content')
<form method="POST" action="{{ $account->exists ? route('bank-accounts.update', $account) : route('bank-accounts.store') }}">
    @csrf
    @if ($account->exists) @method('PUT') @endif

    <div class="card"><div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Label</label>
                <input name="label" value="{{ old('label', $account->label) }}" class="form-control" placeholder="Kapital Bank — USD">
            </div>
            <div class="col-md-3">
                <label class="form-label">Bank name *</label>
                <input name="bank_name" value="{{ old('bank_name', $account->bank_name) }}" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Currency *</label>
                <select name="currency_id" class="form-select" required>
                    @foreach ($currencies as $c)
                        <option value="{{ $c->id }}" @selected(old('currency_id', $account->currency_id) == $c->id)>{{ $c->code }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Account name (beneficiary)</label>
                <input name="account_name" value="{{ old('account_name', $account->account_name) }}" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Account number</label>
                <input name="account_number" value="{{ old('account_number', $account->account_number) }}" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">IBAN</label>
                <input name="iban" value="{{ old('iban', $account->iban) }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">SWIFT / BIC</label>
                <input name="swift" value="{{ old('swift', $account->swift) }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">MFO (bank code)</label>
                <input name="mfo" value="{{ old('mfo', $account->mfo) }}" class="form-control">
            </div>
        </div>

        <hr class="my-4">
        <h2 class="h6 text-muted">Correspondent / intermediary bank <span class="fw-normal">(for inbound foreign-currency wires)</span></h2>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Correspondent bank</label>
                <input name="correspondent_bank" value="{{ old('correspondent_bank', $account->correspondent_bank) }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Correspondent SWIFT</label>
                <input name="correspondent_swift" value="{{ old('correspondent_swift', $account->correspondent_swift) }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Correspondent account</label>
                <input name="correspondent_account" value="{{ old('correspondent_account', $account->correspondent_account) }}" class="form-control">
            </div>
            <div class="col-12">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="2">{{ old('notes', $account->notes) }}</textarea>
            </div>
            <div class="col-12 form-check ms-2">
                <input type="checkbox" name="is_active" value="1" id="is_active" class="form-check-input"
                       @checked(old('is_active', $account->is_active))>
                <label for="is_active" class="form-check-label">Active</label>
            </div>
        </div>
    </div></div>

    <div class="mt-3">
        <button class="btn btn-primary">Save</button>
        <a href="{{ route('bank-accounts.index') }}" class="btn btn-link">Cancel</a>
    </div>
</form>
@endsection

@extends('layouts.app')
@section('title', 'Settings')
@section('heading', 'Company settings')

@section('content')
<form method="POST" action="{{ route('settings.update') }}">
    @csrf @method('PUT')

    <div class="card mb-3"><div class="card-body">
        <h2 class="h6 text-muted">Company</h2>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Legal name *</label>
                <input name="legal_name" value="{{ old('legal_name', $company->legal_name) }}" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Brand name *</label>
                <input name="brand_name" value="{{ old('brand_name', $company->brand_name) }}" class="form-control" required>
            </div>
            <div class="col-md-8">
                <label class="form-label">Address</label>
                <input name="address" value="{{ old('address', $company->address) }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">City</label>
                <input name="city" value="{{ old('city', $company->city) }}" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label">Country *</label>
                <input name="country" value="{{ old('country', $company->country) }}" maxlength="2" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">STIR / Tax ID</label>
                <input name="tax_id_stir" value="{{ old('tax_id_stir', $company->tax_id_stir) }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Director</label>
                <input name="director_name" value="{{ old('director_name', $company->director_name) }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Accountant</label>
                <input name="accountant_name" value="{{ old('accountant_name', $company->accountant_name) }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Email</label>
                <input type="email" name="email" value="{{ old('email', $company->email) }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Phone</label>
                <input name="phone" value="{{ old('phone', $company->phone) }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Website</label>
                <input name="website" value="{{ old('website', $company->website) }}" class="form-control">
            </div>
        </div>
    </div></div>

    <div class="card mb-3"><div class="card-body">
        <h2 class="h6 text-muted">IT Park &amp; tax</h2>
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">IT Park registration no.</label>
                <input name="it_park_reg_no" value="{{ old('it_park_reg_no', $company->it_park_reg_no) }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">IT Park fund rate (%)</label>
                <input type="number" step="0.01" name="it_park_fund_rate" value="{{ old('it_park_fund_rate', $company->it_park_fund_rate) }}" class="form-control">
            </div>
            <div class="col-md-2 form-check ms-3">
                <input type="checkbox" name="it_park_resident" value="1" id="itp" class="form-check-input" @checked(old('it_park_resident', $company->it_park_resident))>
                <label for="itp" class="form-check-label">IT Park resident</label>
            </div>
            <div class="col-md-2 form-check">
                <input type="checkbox" name="vat_exempt" value="1" id="vat" class="form-check-input" @checked(old('vat_exempt', $company->vat_exempt))>
                <label for="vat" class="form-check-label">VAT exempt</label>
            </div>
        </div>
    </div></div>

    <div class="card mb-3"><div class="card-body">
        <h2 class="h6 text-muted">Invoicing</h2>
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Base currency *</label>
                <select name="base_currency_id" class="form-select" required>
                    @foreach ($currencies as $c)
                        <option value="{{ $c->id }}" @selected(old('base_currency_id', $company->base_currency_id) == $c->id)>{{ $c->code }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Invoice prefix *</label>
                <input name="invoice_prefix" value="{{ old('invoice_prefix', $company->invoice_prefix) }}" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Payment terms (days) *</label>
                <input type="number" name="invoice_terms_days" value="{{ old('invoice_terms_days', $company->invoice_terms_days) }}" class="form-control" required>
            </div>
            <div class="col-12">
                <label class="form-label">Invoice footer</label>
                <textarea name="invoice_footer" class="form-control" rows="2">{{ old('invoice_footer', $company->invoice_footer) }}</textarea>
            </div>
        </div>
    </div></div>

    <button class="btn btn-primary">Save settings</button>
</form>
@endsection

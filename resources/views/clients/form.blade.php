@extends('layouts.app')
@section('title', $client->exists ? 'Edit client' : 'New client')
@section('heading', $client->exists ? 'Edit client' : 'New client')

@section('content')
<form method="POST" action="{{ $client->exists ? route('clients.update', $client) : route('clients.store') }}">
    @csrf
    @if ($client->exists) @method('PUT') @endif

    <div class="card"><div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Name *</label>
                <input name="name" value="{{ old('name', $client->name) }}" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Legal name</label>
                <input name="legal_name" value="{{ old('legal_name', $client->legal_name) }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Country *</label>
                <select name="country" class="form-select" required>
                    @foreach (\App\Models\Client::countries() as $code => $label)
                        <option value="{{ $code }}" @selected(old('country', $client->country) === $code)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Default currency</label>
                <select name="default_currency_id" class="form-select">
                    <option value="">—</option>
                    @foreach ($currencies as $c)
                        <option value="{{ $c->id }}" @selected(old('default_currency_id', $client->default_currency_id) == $c->id)>{{ $c->code }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Tax / VAT ID</label>
                <input name="tax_id" value="{{ old('tax_id', $client->tax_id) }}" class="form-control">
            </div>
            <div class="col-md-8">
                <label class="form-label">Address</label>
                <input name="address" value="{{ old('address', $client->address) }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">City</label>
                <input name="city" value="{{ old('city', $client->city) }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Contact person</label>
                <input name="contact_person" value="{{ old('contact_person', $client->contact_person) }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Email</label>
                <input type="email" name="email" value="{{ old('email', $client->email) }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Phone</label>
                <input name="phone" value="{{ old('phone', $client->phone) }}" class="form-control">
            </div>
            <div class="col-12">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="2">{{ old('notes', $client->notes) }}</textarea>
            </div>
            <div class="col-12 form-check ms-2">
                <input type="checkbox" name="is_active" value="1" id="is_active" class="form-check-input"
                       @checked(old('is_active', $client->is_active))>
                <label for="is_active" class="form-check-label">Active</label>
            </div>
        </div>
    </div></div>

    <div class="mt-3">
        <button class="btn btn-primary">Save</button>
        <a href="{{ route('clients.index') }}" class="btn btn-link">Cancel</a>
    </div>
</form>
@endsection

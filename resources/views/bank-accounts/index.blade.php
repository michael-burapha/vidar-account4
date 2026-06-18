@extends('layouts.app')
@section('title', 'Bank accounts')
@section('heading', 'Bank accounts')
@section('actions')
    <a href="{{ route('bank-accounts.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New account</a>
@endsection

@section('content')
<div class="card"><div class="card-body">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>Account</th><th>Currency</th><th>Account no. / IBAN</th><th>SWIFT</th><th></th></tr></thead>
            <tbody>
            @forelse ($accounts as $account)
                <tr>
                    <td><strong>{{ $account->display_name }}</strong>
                        @unless ($account->is_active)<span class="badge bg-secondary">inactive</span>@endunless</td>
                    <td>{{ optional($account->currency)->code }}</td>
                    <td>{{ $account->iban ?: $account->account_number ?: '—' }}</td>
                    <td>{{ $account->swift ?: '—' }}</td>
                    <td class="text-end">
                        <a href="{{ route('bank-accounts.edit', $account) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <form action="{{ route('bank-accounts.destroy', $account) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Delete this account?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-muted">No bank accounts yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div></div>
@endsection

@extends('layouts.app')
@section('title', 'Clients')
@section('heading', 'Clients')
@section('actions')
    <a href="{{ route('clients.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New client</a>
@endsection

@section('content')
<div class="card"><div class="card-body">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>Name</th><th>Country</th><th>Default currency</th><th>Email</th><th></th></tr></thead>
            <tbody>
            @forelse ($clients as $client)
                <tr>
                    <td>
                        <strong>{{ $client->name }}</strong>
                        @unless ($client->is_active)<span class="badge bg-secondary">inactive</span>@endunless
                        @if ($client->isExport())<span class="badge bg-info-subtle text-info-emphasis">export</span>@endif
                    </td>
                    <td>{{ $client->country_name }}</td>
                    <td>{{ optional($client->defaultCurrency)->code ?? '—' }}</td>
                    <td>{{ $client->email ?? '—' }}</td>
                    <td class="text-end">
                        <a href="{{ route('clients.edit', $client) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <form action="{{ route('clients.destroy', $client) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Delete this client?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-muted">No clients yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div></div>
<div class="mt-3">{{ $clients->links() }}</div>
@endsection

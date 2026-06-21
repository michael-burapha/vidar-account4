<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Currency;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        return view('clients.index', [
            'clients' => Client::with('defaultCurrency')->orderBy('name')->paginate(25),
        ]);
    }

    public function create()
    {
        return view('clients.form', [
            'client' => new Client(['country' => 'UZ', 'is_active' => true]),
            'currencies' => Currency::where('is_active', true)->get(),
        ]);
    }

    public function store(Request $request)
    {
        Client::create($this->validated($request));

        return redirect()->route('clients.index')->with('status', 'Client created.');
    }

    public function edit(Client $client)
    {
        return view('clients.form', [
            'client' => $client,
            'currencies' => Currency::where('is_active', true)->get(),
        ]);
    }

    public function update(Request $request, Client $client)
    {
        $client->update($this->validated($request));

        return redirect()->route('clients.index')->with('status', 'Client updated.');
    }

    public function destroy(Client $client)
    {
        if ($client->invoices()->exists()) {
            return back()->withErrors(['client' => 'Cannot delete a client that has invoices.']);
        }

        $client->delete();

        return redirect()->route('clients.index')->with('status', 'Client deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'country' => ['required', 'string', 'size:2'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'postcode' => ['nullable', 'string', 'max:32'],
            'tax_id' => ['nullable', 'string', 'max:64'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:64'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'default_currency_id' => ['nullable', 'exists:currencies,id'],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
        ]) + ['is_active' => $request->boolean('is_active')];
    }
}

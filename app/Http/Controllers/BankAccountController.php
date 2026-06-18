<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Currency;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function index()
    {
        return view('bank-accounts.index', [
            'accounts' => BankAccount::with('currency')->orderBy('label')->get(),
        ]);
    }

    public function create()
    {
        return view('bank-accounts.form', [
            'account' => new BankAccount(['bank_name' => 'Kapital Bank', 'is_active' => true]),
            'currencies' => Currency::where('is_active', true)->get(),
        ]);
    }

    public function store(Request $request)
    {
        BankAccount::create($this->validated($request));

        return redirect()->route('bank-accounts.index')->with('status', 'Bank account created.');
    }

    public function edit(BankAccount $bankAccount)
    {
        return view('bank-accounts.form', [
            'account' => $bankAccount,
            'currencies' => Currency::where('is_active', true)->get(),
        ]);
    }

    public function update(Request $request, BankAccount $bankAccount)
    {
        $bankAccount->update($this->validated($request));

        return redirect()->route('bank-accounts.index')->with('status', 'Bank account updated.');
    }

    public function destroy(BankAccount $bankAccount)
    {
        $bankAccount->delete();

        return redirect()->route('bank-accounts.index')->with('status', 'Bank account deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'label' => ['nullable', 'string', 'max:255'],
            'bank_name' => ['required', 'string', 'max:255'],
            'account_name' => ['nullable', 'string', 'max:255'],
            'currency_id' => ['required', 'exists:currencies,id'],
            'account_number' => ['nullable', 'string', 'max:64'],
            'iban' => ['nullable', 'string', 'max:64'],
            'swift' => ['nullable', 'string', 'max:32'],
            'mfo' => ['nullable', 'string', 'max:32'],
            'inn' => ['nullable', 'string', 'max:32'],
            'correspondent_bank' => ['nullable', 'string', 'max:255'],
            'correspondent_swift' => ['nullable', 'string', 'max:32'],
            'correspondent_account' => ['nullable', 'string', 'max:64'],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
        ]) + ['is_active' => $request->boolean('is_active')];
    }
}

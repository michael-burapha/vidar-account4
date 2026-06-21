<?php

namespace App\Http\Controllers;

use App\Models\CompanySetting;
use App\Models\Currency;
use Illuminate\Http\Request;

class CompanySettingController extends Controller
{
    public function edit()
    {
        return view('settings.edit', [
            'company' => CompanySetting::current(),
            'currencies' => Currency::where('is_active', true)->get(),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'legal_name' => ['required', 'string', 'max:255'],
            'brand_name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'country' => ['required', 'string', 'size:2'],
            'tax_id_stir' => ['nullable', 'string', 'max:64'],
            'it_park_reg_no' => ['nullable', 'string', 'max:64'],
            'it_park_fund_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'director_name' => ['nullable', 'string', 'max:255'],
            'accountant_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:64'],
            'website' => ['nullable', 'string', 'max:255'],
            'base_currency_id' => ['required', 'exists:currencies,id'],
            'invoice_prefix' => ['required', 'string', 'max:16'],
            'invoice_terms_days' => ['required', 'integer', 'min:0', 'max:365'],
            'invoice_footer' => ['nullable', 'string'],
        ]);

        $company = CompanySetting::current();
        $company->fill($data);
        $company->it_park_resident = $request->boolean('it_park_resident');
        $company->vat_exempt = $request->boolean('vat_exempt');
        $company->save();

        return back()->with('status', 'Company settings saved.');
    }
}

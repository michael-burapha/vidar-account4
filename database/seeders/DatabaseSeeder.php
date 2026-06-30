<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\CompanySetting;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --- Currencies -----------------------------------------------------
        $currencies = [
            ['code' => 'UZS', 'name' => 'Uzbekistani Som', 'symbol' => 'soʻm ', 'decimal_places' => 2],
            ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$', 'decimal_places' => 2],
            ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'decimal_places' => 2],
            ['code' => 'KZT', 'name' => 'Kazakhstani Tenge', 'symbol' => '₸', 'decimal_places' => 2],
            ['code' => 'GBP', 'name' => 'Pound Sterling', 'symbol' => '£', 'decimal_places' => 2],
        ];

        foreach ($currencies as $c) {
            Currency::updateOrCreate(['code' => $c['code']], $c);
        }

        $uzs = Currency::where('code', 'UZS')->first();
        $usd = Currency::where('code', 'USD')->first();
        $eur = Currency::where('code', 'EUR')->first();
        $kzt = Currency::where('code', 'KZT')->first();

        // --- Company settings (Vidar Digital, IT Park resident) -------------
        $company = CompanySetting::current();
        $company->fill([
            'legal_name' => 'Vidar Digital',
            'brand_name' => 'Vidar Digital',
            'country' => 'UZ',
            'it_park_resident' => true,
            'vat_exempt' => true,
            'it_park_fund_rate' => 1.00,
            'base_currency_id' => $uzs->id,
            'invoice_prefix' => 'VD',
            'invoice_terms_days' => 14,
            'invoice_footer' => 'Vidar Digital is a resident of IT Park Uzbekistan. '
                . 'Services exported from Uzbekistan are exempt from VAT.',
        ]);
        $company->save();

        // --- Kapital Bank accounts (one per currency held) ------------------
        $accounts = [
            ['currency_id' => $usd->id, 'label' => 'Kapital Bank — USD'],
            ['currency_id' => $eur->id, 'label' => 'Kapital Bank — EUR'],
            ['currency_id' => $uzs->id, 'label' => 'Kapital Bank — UZS'],
            ['currency_id' => $kzt->id, 'label' => 'Kapital Bank — KZT'],
        ];

        foreach ($accounts as $a) {
            BankAccount::updateOrCreate(
                ['label' => $a['label']],
                $a + ['bank_name' => 'Kapital Bank', 'account_name' => 'Vidar Digital', 'is_active' => true]
            );
        }

        // --- Admin user -----------------------------------------------------
        User::updateOrCreate(
            ['email' => 'admin@vidar.digital'],
            ['name' => 'Vidar Admin', 'password' => Hash::make('password1')]
        );
    }
}

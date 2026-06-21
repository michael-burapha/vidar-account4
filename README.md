# Vidar Digital — Accounting & Invoicing

A standalone Laravel 11 application for **Vidar Digital**, a resident of **IT Park
Uzbekistan**. It handles multi-currency invoicing to clients in Denmark, Thailand,
the United Kingdom, Uzbekistan and Kazakhstan, tracks receipts into Kapital Bank
currency accounts, and reports the figures an IT Park resident needs (export share,
monthly IT Park fund contribution).

> This project is **completely independent** — it shares no code, database or
> infrastructure with any other system.

## Why it is built this way (Uzbekistan / IT Park rules)

The regime drives several design decisions:

| Rule | Source | Effect in the app |
|------|--------|-------------------|
| IT Park residents are **exempt from profit/turnover tax and VAT** on core IT activity | [PwC](https://taxsummaries.pwc.com/republic-of-uzbekistan/corporate/tax-credits-and-incentives), [tax-legal.uz](https://tax-legal.uz/en/it-park-questions/) | Invoices carry **no VAT** (`vat_exempt` on company settings); the PDF prints "VAT — Exempt". Per-line `tax_rate` exists for flexibility but defaults to 0. |
| Residents pay **~1% of income** to the IT Park fund monthly | [PwC](https://taxsummaries.pwc.com/republic-of-uzbekistan/corporate/tax-credits-and-incentives) | `ItParkFundCalculator` computes the monthly/annual contribution; shown on the dashboard. Rate is configurable in Settings. |
| Sales to **foreign** clients are an **export of services** (no UZ VAT) and contracts/acts must be registered in **EEISVO**; deals ≤ USD 5,000 may be a plain invoice | [Resolution 589](https://it-park.uz/documents/acts/589_en.pdf), [bne](https://www.intellinews.com/uzbekistan-mandates-export-targets-for-it-park-residents-327704/) | Invoices to non-UZ clients are auto-flagged `is_export`; the form captures **contract no./date and act no.** for EEISVO. |
| **Export targets** matter for keeping residency | [bne](https://www.intellinews.com/uzbekistan-mandates-export-targets-for-it-park-residents-327704/) | Dashboard reports **export share %** of revenue. |
| Domestic UZ B2B sales need an **EHF e-invoice** via SoliqOnline (JSON + digital signature) | [EDICOM](https://edicomgroup.com/electronic-invoicing/uzbekistan) | **Out of scope for v1 (PDF only).** The data model is structured so EHF/SoliqOnline and EEISVO submission can be added later. |
| Statutory books follow **National Accounting Standards (NSBU)**, chart of accounts per NSBU No. 21 | [cis-legislation](https://cis-legislation.com/document.fwx?rgn=6449) | v1 is invoicing-first. A general ledger / NSBU chart of accounts is a planned phase 2. |

Amounts are reported in a **base currency (UZS)** using the **CBU official rate**
snapshotted onto each invoice at issue date, so revenue and the fund contribution
are stable even as rates move.

## Features (v1)

- **Clients** in UZ / DK / TH / GB / KZ, with default currency. Foreign = export.
- **Bank accounts** — Kapital Bank, one per currency (EUR / USD / UZS / KZT), with
  IBAN/SWIFT/MFO and correspondent-bank fields for inbound foreign wires.
- **Multi-currency invoices** with line items, discounts, and a live total; FX rate
  + base-currency total snapshotted at issue.
- **Professional PDF invoices** (DomPDF) with bank/payment details, export marking,
  and VAT-exempt footer.
- **Payments** recorded against invoices; status auto-derives
  (draft → sent → partially paid → paid → overdue).
- **Exchange rates** (UZS per unit) — manual entry now; ready for a CBU auto-fetch.
- **Dashboard** — outstanding by currency, revenue by currency and by country,
  export share, monthly + YTD IT Park fund contribution, overdue count.
- **Settings** — company/IT Park details, fund rate, base currency, invoice prefix.

## Tech stack

Laravel 11 · PHP 8.2+ · MySQL (SQLite used for the test suite) · Blade + Bootstrap 5
(CDN, no build step) · `barryvdh/laravel-dompdf` for PDFs.

## Local setup

```bash
composer install
cp .env.example .env
php artisan key:generate

# Configure MySQL in .env (DB_DATABASE=vidar_accounting), then:
php artisan migrate --seed

php artisan serve
```

Seeded login: **admin@vidar.digital** / **password** (change immediately).
The seed also creates the 5 currencies, Vidar Digital company settings (IT Park
resident, VAT-exempt, 1% fund), and the four Kapital Bank accounts.

### Quick try with SQLite (no MySQL needed)

```bash
cp .env.example .env && php artisan key:generate
sed -i 's/^DB_CONNECTION=mysql/DB_CONNECTION=sqlite/' .env
touch database/database.sqlite
php artisan migrate --seed && php artisan serve
```

## Tests

```bash
php artisan test
```

Feature tests cover authentication, export-invoice creation with FX snapshot,
payment/status flow, the IT Park fund calculation, PDF rendering, and that every
main page renders.

## Project layout

```
app/
  Http/Controllers/   Dashboard, Invoice, Payment, Client, BankAccount,
                      ExchangeRate, CompanySetting, Auth
  Models/             Invoice, InvoiceLine, Payment, Client, BankAccount,
                      Currency, ExchangeRate, CompanySetting
  Services/           InvoiceNumberGenerator, ItParkFundCalculator, FxService
database/migrations/  currencies, company_settings, bank_accounts, clients,
                      exchange_rates, invoices, invoice_lines, payments
resources/views/      layouts, auth, dashboard, invoices (incl. pdf), clients,
                      bank-accounts, exchange-rates, settings
```

## Roadmap

- **Phase 2:** NSBU general ledger + chart of accounts (NSBU No. 21), FX revaluation,
  statutory financial statements.
- **E-invoicing:** SoliqOnline EHF (JSON + digital signature) for domestic UZ sales;
  EEISVO export-contract registration.
- **Automation:** scheduled CBU exchange-rate fetch; email invoices to clients.

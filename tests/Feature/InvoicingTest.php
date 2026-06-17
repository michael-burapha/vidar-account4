<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Models\Invoice;
use App\Models\User;
use App\Services\ItParkFundCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoicingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function admin(): User
    {
        return User::where('email', 'admin@vidar.digital')->first();
    }

    public function test_login_screen_is_reachable(): void
    {
        $this->get('/login')->assertOk()->assertSee('Vidar Digital');
    }

    public function test_dashboard_requires_authentication(): void
    {
        $this->get('/')->assertRedirect('/login');
    }

    public function test_can_create_an_export_invoice_with_fx_snapshot(): void
    {
        $usd = Currency::where('code', 'USD')->first();

        ExchangeRate::create([
            'currency_id' => $usd->id,
            'rate_date' => '2026-06-01',
            'rate' => 12500,
            'source' => 'manual',
        ]);

        $client = Client::create([
            'name' => 'Copenhagen Apps ApS',
            'country' => 'DK',
            'default_currency_id' => $usd->id,
        ]);

        $response = $this->actingAs($this->admin())->post('/invoices', [
            'invoice_number' => 'VD-2026-0001',
            'client_id' => $client->id,
            'currency_id' => $usd->id,
            'issue_date' => '2026-06-10',
            'due_date' => '2026-06-24',
            'status' => 'sent',
            'discount_total' => 0,
            'lines' => [
                ['description' => 'Software development — June', 'quantity' => 10, 'unit' => 'day', 'unit_price' => 500, 'tax_rate' => 0],
                ['description' => 'Hosting', 'quantity' => 1, 'unit' => 'month', 'unit_price' => 200, 'tax_rate' => 0],
            ],
        ]);

        $invoice = Invoice::first();
        $response->assertRedirect("/invoices/{$invoice->id}");

        $this->assertEquals(5200, (float) $invoice->total);
        $this->assertTrue($invoice->is_export, 'Foreign client should be flagged export');
        $this->assertEquals(12500, (float) $invoice->exchange_rate);
        $this->assertEquals(5200 * 12500, (float) $invoice->total_base);
    }

    public function test_payment_marks_invoice_paid_and_updates_balance(): void
    {
        $usd = Currency::where('code', 'USD')->first();
        $client = Client::create(['name' => 'UK Ltd', 'country' => 'GB']);

        $invoice = Invoice::create([
            'invoice_number' => 'VD-2026-0002',
            'client_id' => $client->id,
            'currency_id' => $usd->id,
            'issue_date' => '2026-06-10',
            'due_date' => '2026-06-24',
            'status' => 'sent',
            'total' => 1000,
            'subtotal' => 1000,
        ]);

        $this->actingAs($this->admin())->post("/invoices/{$invoice->id}/payments", [
            'payment_date' => '2026-06-20',
            'amount' => 1000,
        ])->assertRedirect();

        $invoice->refresh();
        $this->assertEquals('paid', $invoice->status);
        $this->assertEquals(0.0, $invoice->balanceDue());
    }

    public function test_it_park_fund_is_one_percent_of_invoiced_base(): void
    {
        $usd = Currency::where('code', 'USD')->first();
        $client = Client::create(['name' => 'KZ LLP', 'country' => 'KZ']);

        Invoice::create([
            'invoice_number' => 'VD-2026-0003',
            'client_id' => $client->id,
            'currency_id' => $usd->id,
            'issue_date' => '2026-06-05',
            'due_date' => '2026-06-19',
            'status' => 'sent',
            'is_export' => true,
            'total' => 1000,
            'exchange_rate' => 12000,
            'total_base' => 12000000,
        ]);

        $result = app(ItParkFundCalculator::class)->forMonth(2026, 6);

        $this->assertEquals(12000000, $result['invoiced_base']);
        $this->assertEquals(120000, $result['fund_contribution']);
        $this->assertEquals(100.0, $result['export_share']);
    }

    public function test_main_pages_render(): void
    {
        $this->actingAs($this->admin());

        foreach (['/', '/invoices', '/invoices/create', '/clients', '/clients/create',
                  '/bank-accounts', '/bank-accounts/create', '/exchange-rates', '/settings'] as $path) {
            $this->get($path)->assertOk();
        }
    }

    public function test_invoice_pdf_renders(): void
    {
        $usd = Currency::where('code', 'USD')->first();
        $client = Client::create(['name' => 'Thai Co', 'country' => 'TH']);

        $invoice = Invoice::create([
            'invoice_number' => 'VD-2026-0004',
            'client_id' => $client->id,
            'currency_id' => $usd->id,
            'issue_date' => '2026-06-10',
            'due_date' => '2026-06-24',
            'status' => 'sent',
            'total' => 500,
            'subtotal' => 500,
        ]);
        $invoice->lines()->create([
            'description' => 'Consulting', 'quantity' => 1, 'unit' => 'service',
            'unit_price' => 500, 'line_total' => 500,
        ]);

        $response = $this->actingAs($this->admin())->get("/invoices/{$invoice->id}/pdf");
        $response->assertOk();
        $this->assertStringContainsString('application/pdf', $response->headers->get('content-type'));
    }
}

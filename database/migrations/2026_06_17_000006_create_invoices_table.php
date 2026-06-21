<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('client_id')->constrained();
            $table->foreignId('currency_id')->constrained();
            $table->foreignId('bank_account_id')->nullable()->constrained(); // account to be paid into
            $table->date('issue_date');
            $table->date('due_date');
            $table->string('status')->default('draft'); // draft, sent, partially_paid, paid, overdue, cancelled

            $table->decimal('subtotal', 18, 2)->default(0);
            $table->decimal('discount_total', 18, 2)->default(0);
            $table->decimal('tax_total', 18, 2)->default(0); // 0 for IT Park VAT-exempt sales
            $table->decimal('total', 18, 2)->default(0);
            $table->decimal('amount_paid', 18, 2)->default(0);

            // Snapshot of FX at issue, for reporting in base currency (UZS)
            $table->decimal('exchange_rate', 18, 6)->nullable(); // UZS per unit of invoice currency
            $table->decimal('total_base', 18, 2)->nullable();    // total converted to base currency

            // Export-of-services fields (EEISVO registration for foreign clients)
            $table->boolean('is_export')->default(false);
            $table->string('contract_no')->nullable();
            $table->date('contract_date')->nullable();
            $table->string('act_no')->nullable();         // act of work performed

            $table->text('notes')->nullable();
            $table->text('terms')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

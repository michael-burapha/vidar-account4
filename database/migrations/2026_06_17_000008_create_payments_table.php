<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bank_account_id')->nullable()->constrained();
            $table->foreignId('currency_id')->constrained();
            $table->date('payment_date');
            $table->decimal('amount', 18, 2);                 // in payment currency
            $table->decimal('exchange_rate', 18, 6)->nullable(); // UZS per unit at receipt date
            $table->decimal('amount_base', 18, 2)->nullable();   // amount converted to base currency
            $table->string('method')->default('bank_transfer');
            $table->string('reference')->nullable();          // bank reference / SWIFT MT103
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

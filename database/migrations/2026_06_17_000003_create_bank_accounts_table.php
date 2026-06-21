<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('label')->nullable();              // e.g. "Kapital Bank — USD"
            $table->string('bank_name')->default('Kapital Bank');
            $table->string('account_name')->nullable();        // beneficiary name
            $table->foreignId('currency_id')->constrained()->cascadeOnUpdate();
            $table->string('account_number')->nullable();      // 20-digit UZ account / IBAN
            $table->string('iban')->nullable();
            $table->string('swift')->nullable();               // bank BIC/SWIFT
            $table->string('mfo')->nullable();                 // UZ bank code (МФО)
            $table->string('inn')->nullable();                 // bank INN (for UZ requisites)
            // Correspondent / intermediary bank for inbound foreign-currency wires
            $table->string('correspondent_bank')->nullable();
            $table->string('correspondent_swift')->nullable();
            $table->string('correspondent_account')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};

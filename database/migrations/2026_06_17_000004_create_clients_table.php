<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('legal_name')->nullable();
            $table->char('country', 2)->default('UZ');         // ISO 3166-1 alpha-2: DK, TH, GB, UZ, KZ
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('postcode')->nullable();
            $table->string('tax_id')->nullable();              // VAT/registration no. of the client
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('contact_person')->nullable();
            $table->foreignId('default_currency_id')->nullable()->constrained('currencies');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->string('legal_name')->default('Vidar Digital');
            $table->string('brand_name')->default('Vidar Digital');
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->char('country', 2)->default('UZ');
            $table->string('tax_id_stir')->nullable();        // STIR/INN — tax identification number
            $table->string('it_park_reg_no')->nullable();     // IT Park resident certificate number
            $table->boolean('it_park_resident')->default(true);
            $table->boolean('vat_exempt')->default(true);      // IT Park residents are VAT-exempt
            $table->decimal('it_park_fund_rate', 5, 2)->default(1.00); // % of income paid to IT Park fund
            $table->string('director_name')->nullable();
            $table->string('accountant_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->foreignId('base_currency_id')->nullable();   // reporting currency (UZS)
            $table->string('invoice_prefix', 16)->default('VD');
            $table->unsignedInteger('invoice_terms_days')->default(14);
            $table->text('invoice_footer')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};

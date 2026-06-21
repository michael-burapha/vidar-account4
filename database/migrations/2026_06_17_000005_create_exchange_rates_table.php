<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('currency_id')->constrained()->cascadeOnDelete();
            $table->date('rate_date');
            $table->decimal('rate', 18, 6);          // UZS per 1 unit of the currency (CBU official rate)
            $table->string('source')->default('manual'); // 'CBU' once auto-fetched
            $table->timestamps();

            $table->unique(['currency_id', 'rate_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};

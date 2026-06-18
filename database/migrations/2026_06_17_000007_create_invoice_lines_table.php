<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->string('description');
            $table->decimal('quantity', 14, 4)->default(1);
            $table->string('unit', 32)->default('service'); // service, hour, day, month, item
            $table->decimal('unit_price', 18, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);   // %, 0 for IT Park VAT-exempt
            $table->decimal('line_total', 18, 2)->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_lines');
    }
};
